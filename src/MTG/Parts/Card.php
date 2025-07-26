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

namespace MTG\Parts;

use Discord\Helpers\Collection;
use Discord\Helpers\ExCollectionInterface;
use Discord\Parts\Part;

class Card extends Part
{
    use CardAttributes;

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
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

        // The fields below are also part of the response (if not null), but cannot currently be used as query parameters
        'names',
        'manaCost',
        'variations',
        'imageUrl',
        'watermark',
        'border',
        'timeshifted',
        'hand',
        'life',
        'reserved',
        'releaseDate',
        'starter',
        'rulings',
        'foreignNames',
        'printings',
        'originalText',
        'originalType',
        'legalities',
        'source'
    ];

    public function getRulingsAttribute(): ?ExCollectionInterface
    {
        if (! isset($this->attributes['rulings']) || ! is_array($this->attributes['rulings'])) {
            return null;
        }

        $collection = Collection::for(Ruling::class);

        foreach ($this->attributes['rulings'] as $idx => $ruling) {
            $collection->set($idx, $this->factory->part(Ruling::class, (array) $ruling));
        }

        return $collection;
    }
}
