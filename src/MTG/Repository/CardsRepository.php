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

namespace MTG;

use Discord\Helpers\ExCollectionInterface;
use Discord\Http\Endpoint;
use Discord\Repository\AbstractRepository;
use MTG\Parts\Part\Card;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function React\Promise\reject;

class CardsRepository extends AbstractRepository
{
    /**
     * {@inheritDoc}
     */
    protected $endpoints = [
        'get' => Http::MTG_BASE_URL . '/cards',
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
    public function getCardInfo(array $params): PromiseInterface
    {
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

        // Fields that accept multiple values and can use AND (comma) or OR (pipe)
        $multiValueAndOrFields = [
            'colors', 'colorIdentity', 'supertypes', 'types', 'subtypes'
        ];

        foreach ($params as $key => $value) {
            if (is_string($value) && !in_array($key, $multiValueAndOrFields, true) && strpos($value, ',') !== false) {
                return reject(new \InvalidArgumentException("Field '{$key}' cannot contain a comma."));
            }
        }

        $endpoint = new Endpoint($this->endpoints['get']);

        foreach ($params as $key => $value) {
            $endpoint->addQuery($key, $value);
        }

        return $this->http->get($endpoint)->then(function (ResponseInterface $response) {
            $data = json_decode((string)$response->getBody(), true);
            $this->discord->getLogger()->debug('Fetched card info', ['response' => $data]);
            return $data['cards'] ?? []; // @TODO: Probably wrong
        });
    }
}