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
use Discord\Builders\Components\Button;
use Discord\Builders\Components\Container;
use Discord\Builders\Components\MediaGallery;
use Discord\Builders\Components\Section;
use Discord\Builders\Components\Separator;
use Discord\Builders\Components\TextDisplay;
use Discord\Helpers\Collection;
use Discord\Helpers\ExCollectionInterface;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Part;
use MTG\HelperTrait;

/**
 * Represents a Magic: The Gathering card.
 *
 * @property-read ?Embed image_embed The image for the card.
 */
class Card extends Part
{
    use CardAttributes;

    /**
     * @inheritDoc
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
        'source',
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
        if (! isset($this->attributes['releaseDate'])) {
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
        if (! isset($this->attributes['foreignNames']) || ! is_array($this->attributes['foreignNames'])) {
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
        if (! isset($this->attributes['legalities']) || ! is_array($this->attributes['legalities'])) {
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
    public function toContainer(bool $image_only = true): ?Container
    {
        if (isset($this->attributes['imageUrl']) && $image_only) {
            return Container::new()->addComponent(MediaGallery::new()->addItem($this->imageUrl));
        }

        if (! isset($this->attributes['name'])) {
            return null;
        }

        if (isset($this->attributes['layout'])) {
            if ($this->layout === 'normal') {
                return $this->normalLayoutContainer();
            }
        }
        
        return null;
    }

    /**
     * Generates an Embed object for the card's image.
     *
     * @return Embed|null
     *
     * @since 0.4.0
     */
    public function getImageEmbedAttribute(): ?Embed
    {
        if (! isset($this->attributes['imageUrl'])) {
            return null;
        }

        $embed = new Embed($this->discord);

        return $embed
            ->setTitle($this->name ?? 'Untitled')
            ->setImage($this->attributes['imageUrl']);
    }

    /**
     * Builds and returns a Container representing the normal layout for a Magic: The Gathering card.
     *
     * @return Container
     *
     * @since 0.4.0
     */
    public function normalLayoutContainer(): Container
    {
        /** @var HelperTrait $discord */
        $discord = $this->discord;
        $components = [Section::new()
            ->addComponent(TextDisplay::new($this->name))
            ->setAccessory(Button::new(Button::STYLE_SECONDARY, 'mana_cost')
                ->setLabel(($this->mana_cost === null || $this->mana_cost === '{0}') ? '​' : $this->mana_cost)
                ->setEmoji(($this->mana_cost === null || $this->mana_cost === '{0}') ? $this->discord->emojis->get('name', '0_') : null)
                ->setDisabled(true)),
            Separator::new(),
        ];

        $type_text = '';
        if (isset($this->attributes['supertypes'])) {
            $type_text .= implode(' ', $this->supertypes).' ';
        }
        if (isset($this->attributes['types'])) {
            $type_text .= implode(' ', $this->types);
        }
        if (isset($this->attributes['subtypes'])) {
            $type_text .= ' - ';
            $type_text .= implode(' ', $this->subtypes);
        }
        $set_rarity_text = '';
        if (isset($this->attributes['set'])) {
            $set_rarity_text .= " $this->set";
        }
        if (isset($this->attributes['rarity'])) {
            $set_rarity_text .= " ($this->rarity)";
        }
        $components[] = Section::new()
            ->addComponent(TextDisplay::new($type_text))
            ->setAccessory(Button::new(Button::STYLE_SECONDARY, 'search_card_set')->setLabel($set_rarity_text)->setDisabled(true));

        if (isset($this->attributes['text'])) {
            $components[] = Separator::new();
            $components[] = TextDisplay::new($discord->encapsulatedSymbolsToEmojis($this->text));
        }

        $components[] = Separator::new();
        $artist_textdisplay = TextDisplay::new($this->attributes['artist'] ?? 'No Artist Attribution');
        $components[] = isset($this->attributes['power'], $this->attributes['toughness'])
            ? Section::new()
                ->addComponent($artist_textdisplay)
                ->setAccessory(Button::new(Button::STYLE_SECONDARY, 'power_toughness')->setLabel("({$this->power}/{$this->toughness})")->setDisabled(true))
            : $artist_textdisplay;

        return Container::new()->addComponents($components);
    }
}
