<?php

declare(strict_types=1);

/*
 * This file is a part of the MTG Card Info App project.
 *
 * Copyright (c) 2025-present Valithor Obsidion <valithor@valzargaming.com>
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
use React\Promise\PromiseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WeakReference;

use function React\Promise\reject;
use function React\Promise\resolve;

class CardsRepository extends AbstractRepository
{
    /**
     * {@inheritDoc}
     */
    protected $endpoints = [
        'all' => HttpEndpoint::CARDS,
        'get' => HttpEndpoint::CARD,
    ];

    /**
     * {@inheritDoc}
     */
    protected $class = Card::class;

    /**
     * Fetch card information by query parameters.
     *
     * @param Card|array $params
     *
     * @return PromiseInterface<Card[]|ExCollectionInterface<Card>>
     */
    public function getCardInfo(Card|array $params): PromiseInterface
    {
        if ($params instanceof Card) {
            $params = $params->jsonSerialize();
        } else {
            $resolver = new OptionsResolver();
            $resolver
                ->setDefined([
                    'name',
                    'layout',
                    'cmc',
                    'colors',
                    'colorIdentity',
                    'type',
                    'supertypes',
                    'types',
                    'subtypes',
                    'rarity',
                    'set',
                    'setName',
                    'text',
                    'flavor',
                    'artist',
                    'number',
                    'power',
                    'toughness',
                    'loyalty',
                    'language',
                    'gameFormat',
                    'legality',
                    'page',
                    'pageSize',
                    'orderBy',
                    'random',
                    'contains',
                    'id',
                    'multiverseid',
                ])
                ->setAllowedTypes('name', ['string'])
                ->setAllowedTypes('layout', ['string'])
                ->setAllowedTypes('colors', ['string'])
                ->setAllowedTypes('colorIdentity', ['string'])
                ->setAllowedTypes('supertypes', ['string'])
                ->setAllowedTypes('types', ['string'])
                ->setAllowedTypes('subtypes', ['string'])
                ->setAllowedTypes('rarity', ['string'])
                ->setAllowedTypes('set', ['string'])
                ->setAllowedTypes('text', ['string'])
                ->setAllowedTypes('artist', ['string'])
                ->setAllowedTypes('number', ['string'])
                ->setAllowedTypes('page', ['int'])
                ->setAllowedTypes('pageSize', ['int'])
                ->setAllowedTypes('orderBy', ['string'])
                ->setDefaults([
                    'language' => 'English',
                ]);

            $params = $resolver->resolve($params);
        }

        // Fields that accept multiple values and can use AND (comma) or OR (pipe)
        $multiValueAndOrFields = [
            'colors', 'colorIdentity', 'supertypes', 'types', 'subtypes',
        ];

        foreach ($params as $key => $value) {
            if (is_string($value) && ! in_array($key, $multiValueAndOrFields, true) && strpos($value, ',') !== false) {
                return reject(new \InvalidArgumentException("Field '{$key}' cannot contain a comma."));
            }
        }

        $endpoint = new Endpoint($this->endpoints['all']);

        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $endpoint->addQuery($key, $value);
        }

        return $this->mtg_http->get($endpoint)->then(function ($response) {
            $response = $response->cards;

            $collection = Collection::for($this->class);

            foreach ($response as $cardData) {
                $card = $this->factory->create($this->class, array_merge($this->vars, (array) $cardData), true);
                $card->created = true;
                $this->items[$card->{$this->discrim}] = WeakReference::create($card);
                $this->cache->set($card->{$this->discrim}, $card);
                $collection->pushItem($card);
            }

            return $collection;
        });
    }

    /**
     * @param object $response
     *
     * @return PromiseInterface<static>
     */
    protected function cacheFreshen($response): PromiseInterface
    {
        foreach ($response as $value) {
            foreach ($value as $value) {
                $value = array_merge($this->vars, (array) $value);
                $part = $this->factory->create($this->class, $value, true);
                $items[$part->{$this->discrim}] = $part;
            }
        }

        if (empty($items)) {
            return resolve($this);
        }

        return $this->cache->setMultiple($items)->then(fn ($success) => $this);
    }

    /**
     * Gets a part from the repository or Discord servers.
     *
     * @param string $id    The ID to search for.
     * @param bool   $fresh Whether we should skip checking the cache.
     *
     * @throws \Exception
     *
     * @return PromiseInterface<Part>
     */
    public function fetch(string $id, bool $fresh = false): PromiseInterface
    {
        if (! $fresh) {
            if (isset($this->items[$id])) {
                $part = $this->items[$id];
                if ($part instanceof WeakReference) {
                    $part = $part->get();
                }

                if ($part) {
                    $this->items[$id] = $part;

                    return resolve($part);
                }
            } else {
                return $this->cache->get($id)->then(function ($part) use ($id) {
                    if ($part === null) {
                        return $this->fetch($id, true);
                    }

                    return $part;
                });
            }
        }

        if (! isset($this->endpoints['get'])) {
            return reject(new \Exception('You cannot get this part.'));
        }

        $part = $this->factory->part($this->class, [$this->discrim => $id]);
        $endpoint = new Endpoint($this->endpoints['get']);
        $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));

        return $this->mtg_http->get($endpoint)->then(function ($response) use ($part, $id) {
            $response = $response->card;
            $part->created = true;
            $part->fill(array_merge($this->vars, (array) $response));

            return $this->cache->set($id, $part)->then(fn ($success) => $part);
        });
    }
}
