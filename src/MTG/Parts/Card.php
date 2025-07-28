<?php

declare(strict_types=1);

/*
 * This file is a part of the MTG Card Info App project.
 *
 * Copyright (c) 2025-present Valithor Obsidion <valithor@discordphp.org>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace MTG\Parts;

use Carbon\Carbon;
use Discord\Builders\Components\Container;
use Discord\Builders\Components\MediaGallery;
use Discord\Builders\Components\Separator;
use Discord\Builders\Components\TextDisplay;
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

    /**
     * Gets the release date of the card.
     *
     * @return ?Carbon|null
     * 
     * @since 0.3.0
     */
    public function getReleaseDateAttribute(): ?Carbon
    {
        if (!isset($this->attributes['releaseDate'])) {
            return null;
        }

        return Carbon::parse($this->attributes['releaseDate']);
    }

    /**
     * Converts the card to a container with components.
     * 
     * @return ExCollectionInterface<Ruling>|null
     * 
     * @since 0.3.0
     */
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

    /**
     * Gets the foreign names of the card.
     * 
     * @return ExCollectionInterface<ForeignName>|null
     * 
     * @since 0.3.0
     */
    public function getForeignNamesAttribute(): ?ExCollectionInterface
    {
        if (!isset($this->attributes['foreignNames']) || !is_array($this->attributes['foreignNames'])) {
            return null;
        }

        $collection = Collection::for(ForeignName::class);

        foreach ($this->attributes['foreignNames'] as $idx => $foreignName) {
            $collection->set($idx, $this->factory->part(ForeignName::class, (array) $foreignName));
        }

        return $collection;
    }

    /**
     * Gets the legality of the card.
     * 
     * @return ExCollectionInterface<Legality>|null
     * 
     * @since 0.3.0
     */
    public function getLegalitiesAttribute(): ?ExCollectionInterface
    {
        if (!isset($this->attributes['legalities']) || !is_array($this->attributes['legalities'])) {
            return null;
        }

        $collection = Collection::for(Legality::class);

        foreach ($this->attributes['legalities'] as $idx => $legality) {
            $collection->set($idx, $this->factory->part(Legality::class, (array) $legality));
        }

        return $collection;
    }

    /**
     * Converts the card to a container with components.
     * 
     * @return Container|null
     * 
     * @since 0.3.0
     */
    public function toContainer(): ?Container
    {
        if (isset($this->attributes['imageUrl'])) {            
            return Container::new()->addComponent(MediaGallery::new()->addItem($this->imageUrl));
        }

        if (!isset($this->attributes['name'])) {
            return null;
        }

        $components = [];
        $components[] = TextDisplay::new("$this->name $this->manaCost");
        $components[] = Separator::new();
        $line = '';
        if (isset($this->attributes['supertypes'])) {
            $line .= implode(' ', $this->supertypes) . ' ';
        }
        if (isset($this->attributes['types'])) {
            $line = implode(' ', $this->types);
        }
        if (isset($this->attributes['subtypes'])) {
            $line .= ' - ';
            $line .= implode(' ', $this->subtypes);
        }
        if (isset($this->attributes['rarity'])) {
            $line .= " ($this->rarity)";
        }
        $components[] = TextDisplay::new($line);
        if (isset($this->attributes['text'])) {
            $components[] = Separator::new();
            $components[] = TextDisplay::new($this->text);
        }
        if (isset($this->attributes['artist'])) {
            $components[] = Separator::new();
            $footer = $this->artist;
            if (isset($this->attributes['power'], $this->attributes['toughness'])) {
                $footer .= "              ({$this->power}/{$this->toughness})";
            }
        }

        return Container::new()->addComponents($components);
    }
}
