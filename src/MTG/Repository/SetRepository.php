<?php

declare(strict_types=1);

/*
 * This file is a part of the DiscordPHP-MTG project.
 *
 * Copyright (c) 2025-present Valithor Obsidion <valithor@discordphp.org>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace MTG\Repository;

use Discord\Helpers\ExCollectionInterface;
use Discord\Http\Endpoint;
use MTG\Http\Endpoint as HttpEndpoint;
use MTG\Parts\Card;
use MTG\Parts\Set;
use React\Promise\PromiseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WeakReference;

use function Discord\studly;
use function React\Promise\reject;

/**
 * Reads the API's `sets` endpoint — list sets, fetch one by code, and
 * generate a booster pack from a set — hydrating {@see Set} parts.
 *
 * @link https://docs.magicthegathering.io/#api_v1sets_list List sets
 * @link https://docs.magicthegathering.io/#api_v1sets_get Fetch one set by code
 * @link https://docs.magicthegathering.io/#api_v1booster_get Generate a booster pack
 *
 * @since 0.3.0
 */
class SetRepository extends AbstractRepository
{
    /**
     * @inheritDoc
     */
    protected $discrim = 'name';

    /**
     * @inheritDoc
     */
    protected $endpoints = [
        'all' => HttpEndpoint::SETS,
        'get' => HttpEndpoint::SET,
    ];

    /**
     * @inheritDoc
     */
    protected $class = Set::class;

    /**
     * Returns the id attribute.
     *
     * @return string The id attribute.
     */
    protected function getIdAttribute(): string
    {
        return $this->name;
    }

    /**
     * Fetch card information by query parameters.
     *
     * @param Card|Set|array $params
     * @param array          $params['name']  The full name of the set, e.g. "Masters 25".
     *                                        This is the same as the `setName` attribute of the Card.
     * @param array          $params['block'] The block name, e.g. "Core Set".
     *
     * @return PromiseInterface<ExCollectionInterface<Set>|Set[]>
     *
     * @since 0.5.0
     */
    public function getSets($params = []): PromiseInterface
    {
        if ($params instanceof Card) {
            $params = ['name' => $params->setName];
        } elseif ($params instanceof Set) {
            $params = $params->jsonSerialize();
        } else {
            // Convert underscore_case keys to camelCase
            foreach ($params as $key => $value) {
                $newKey = lcfirst(studly($key));
                unset($params[$key]);
                $params[$newKey] = $value;
            }

            $resolver = new OptionsResolver();
            $resolver
                ->setDefined([
                    'name',
                    'block',
                ])
                ->setAllowedTypes('name', ['string'])
                ->setAllowedTypes('block', ['string']);

            $params = $resolver->resolve($params);
        }

        $endpoint = new Endpoint($this->endpoints['all']);

        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $endpoint->addQuery($key, $value);
        }

        return $this->mtg_http->get($endpoint)->then(function ($response) {
            $response = $response->sets;

            $collection = ($this->discord->getCollectionClass())::for($this->class, $this->discrim);

            foreach ($response as $setData) {
                $set = $this->factory->create($this->class, array_merge($this->vars, (array) $setData), true);
                $set->created = true;
                $this->items[$set->{$this->discrim}] = WeakReference::create($set);
                $this->cache->set($set->{$this->discrim}, $set);
                $collection->pushItem($set);
            }

            return $collection;
        });
    }

    /**
     * Opens a booster pack for a set (`GET /sets/:id/booster`): the API rolls a
     * pack against that set's booster configuration and returns the cards.
     *
     * Note: the public `api.magicthegathering.io` host currently answers this
     * route with HTTP 400 (its dataset no longer carries booster configs); the
     * call is correct and works against a self-hosted mtg-api instance.
     *
     * @param Set|string $set A {@see Set} or a set code (e.g. `"KTK"`).
     *
     * @return PromiseInterface<ExCollectionInterface<Card>> The rolled cards. Never cached — a pack is random.
     *
     * @link https://docs.magicthegathering.io/#api_v1booster_get
     *
     * @since 1.1.0
     */
    public function generateBooster(Set|string $set): PromiseInterface
    {
        $code = $set instanceof Set ? (string) $set->code : $set;

        if ($code === '') {
            return reject(new \InvalidArgumentException('A set code is required to generate a booster.'));
        }

        $endpoint = new HttpEndpoint(HttpEndpoint::SETS_BOOSTER);
        $endpoint->bindAssoc(['id' => $code]);

        return $this->mtg_http->get($endpoint)->then(function ($response): ExCollectionInterface {
            // No discriminator: a pack can contain duplicate cards (basic lands),
            // and keying by `id` would silently collapse them.
            $collection = ($this->discord->getCollectionClass())::for(Card::class, null);

            foreach ($response->cards ?? [] as $cardData) {
                $collection->pushItem($this->factory->create(Card::class, array_merge($this->vars, (array) $cardData), true));
            }

            return $collection;
        });
    }
}
