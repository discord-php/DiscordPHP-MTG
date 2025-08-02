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

use Discord\Helpers\Collection;
use Discord\Helpers\ExCollectionInterface;
use Discord\Http\Endpoint;
use MTG\Http\Endpoint as HttpEndpoint;
use MTG\Parts\Card;
use MTG\Parts\Set;
use React\Promise\PromiseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WeakReference;

use function Discord\studly;

/**
 * Repository for Magic: The Gathering sets.
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

            $collection = Collection::for($this->class, $this->discrim);

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
}
