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
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Part;
use MTG\HelperTrait;
use MTG\MTG;
use React\Promise\PromiseInterface;

/**
 * Represents a Magic: The Gathering card.
 *
 * @property ExCollectionInterface<Legality>    $legalities
 * @property ExCollectionInterface<Ruling>      $rulings
 * @property ExCollectionInterface<ForeignName> $foreignNames
 *
 * @property-read Embed|null  $image_embed       The image for the card as an embed.
 * @property-read Button      $json_button       The button to view the card as JSON.
 * @property-read Button|null $view_image_button The button to view the card image.
 *
 * @since 0.4.0
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
        'set', // Short name
        'setName', // Full name, can be used to query /sets
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
     * @return ExCollectionInterface<Ruling>
     *
     * @since 0.3.0
     */
    public function getRulingsAttribute(): ExCollectionInterface
    {
        $collection = Collection::for(Ruling::class);

        if (! isset($this->attributes['rulings']) || ! is_array($this->attributes['rulings'])) {
            return $collection;
        }

        foreach ($this->attributes['rulings'] as $idx => $ruling) {
            $collection->set($idx, $this->factory->part(Ruling::class, (array) $ruling));
        }

        return $collection;
    }

    /**
     * Gets the foreign names of the card.
     *
     * @return ExCollectionInterface<ForeignName>
     *
     * @since 0.3.0
     */
    public function getForeignNamesAttribute(): ExCollectionInterface
    {
        $collection = Collection::for(ForeignName::class);

        if (! isset($this->attributes['foreignNames']) || ! is_array($this->attributes['foreignNames'])) {
            return $collection;
        }

        foreach ($this->attributes['foreignNames'] as $idx => $foreignName) {
            $collection->set($idx, $this->factory->part(ForeignName::class, (array) $foreignName));
        }

        return $collection;
    }

    /**
     * Gets the legality of the card.
     *
     * @return ExCollectionInterface<Legality>
     *
     * @since 0.3.0
     */
    public function getLegalitiesAttribute(): ExCollectionInterface
    {
        $collection = Collection::for(Legality::class);

        if (! isset($this->attributes['legalities']) || ! is_array($this->attributes['legalities'])) {
            return $collection;
        }

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
    public function toContainer(?Interaction $interaction = null): ?Container
    {
        if (! isset($this->attributes['name'])) {
            return null;
        }

        if (isset($this->attributes['layout'])) {
            switch ($this->layout) {
                case 'normal':
                case 'meld':
                case 'transform':
                case 'default':
                    return $this->normalLayoutContainer($interaction);
            }
        }

        if (isset($this->attributes['imageUrl'])) {
            return Container::new()->addComponent(MediaGallery::new()->addItem($this->imageUrl));
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
    public function normalLayoutContainer(?Interaction $interaction): Container
    {
        /** @var HelperTrait $mtg */
        $mtg = $this->discord;

        $ci_emoji = (($this->colorIdentity) ? implode('', array_map(fn ($c) => $this->discord->emojis->get('name', 'CI_'.$c.'_'), $this->colorIdentity)) : '');
        $mana_cost = $mtg->encapsulatedSymbolsToEmojis($this->manaCost ?? '');

        $components = [TextDisplay::new("$ci_emoji {$this->name} $mana_cost")];

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
        if (isset($this->attributes['rarity'])) {
            $type_text .= " ($this->rarity)";
        }
        if (isset($this->attributes['set'])) {
            $components[] = Separator::new();
            $components[] = Section::new()
                ->addComponent(TextDisplay::new($type_text))
                ->setAccessory($this->getSetButton($interaction));
        }

        if (isset($this->attributes['text'])) {
            $components[] = Separator::new();
            $components[] = TextDisplay::new($mtg->encapsulatedSymbolsToEmojis($this->text));
        }

        if (isset($this->attributes['power'], $this->attributes['toughness'])) {
            $components[] = Separator::new();
            $components[] = TextDisplay::new(
                '('.
                str_replace('*', '\*', $this->power).
                '/'.
                str_replace('*', '\*', $this->toughness).
                ')'
            );
        }
        if (isset($this->attributes['loyalty'], $this->attributes['loyalty'])) {
            $components[] = Separator::new();
            $components[] = TextDisplay::new("[{$this->loyalty}]");
        }

        return Container::new()->addComponents($components);
    }

    /**
     * Gets a button to view the raw JSON of the card.
     *
     * @param Interaction|null $interaction
     *
     * @return Button|null
     *
     * @since 0.5.0
     */
    public function getJsonButton(Interaction $interaction): Button
    {
        return Button::new(Button::STYLE_SECONDARY, "JSON_{$this->id}")
            ->setLabel('JSON')
            ->setListener(
                fn () => $interaction->sendFollowUpMessage(
                    MTG::createBuilder()->addFileFromContent("{$this->id}.json", json_encode($this, JSON_PRETTY_PRINT)),
                    true
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
    }

    /**
     * Gets a button to view the image of the card.
     *
     * @param Interaction|null $interaction
     *
     * @return Button|null
     *
     * @since 0.5.0
     */
    public function getViewImageButton(Interaction $interaction): ?Button
    {
        if (! isset($this->attributes['imageUrl'])) {
            return null;
        }

        return Button::new(Button::STYLE_SECONDARY, "VIEW_IMAGE_{$this->id}")
            ->setLabel('View Image')
            ->setListener(
                fn () => $interaction->sendFollowUpMessage(
                    MTG::createBuilder()->addEmbed($this->image_embed),
                    true
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
    }

    /**
     * Gets a button to view the set the card belongs to.
     *
     * @param Interaction|null $interaction
     *
     * @return Button|null
     *
     * @since 0.5.0
     */
    public function getSetButton(?Interaction $interaction = null): ?Button
    {
        if (! isset($this->attributes['setName'])) {
            return null;
        }

        /** @var MTG $mtg */
        $mtg = $this->discord;

        $button = Button::new(Button::STYLE_SECONDARY, "SET_{$this->setName}")->setLabel("{$this->set} - {$this->setName}");

        if ($interaction) {
            $button->setListener(
                fn () => $mtg->sets->getSets($this)->then(
                    fn (ExCollectionInterface $sets): PromiseInterface => $interaction->sendFollowUpMessage(
                        ($container = $sets->first()?->toContainer($interaction))
                            ? MTG::createBuilder()->addComponent($container)
                            : MTG::createBuilder()->setContent('No sets found.'),
                        true
                    ),
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
        } else {
            $button->setDisabled(true);
        }

        return $button;
    }

    /**
     * Gets a button to view the foreign names for the card.
     *
     * @param Interaction $interaction
     *
     * @return Button|null
     *
     * @since 0.7.0
     */
    public function getForeignNamesButton(Interaction $interaction): ?Button
    {
        if (! isset($this->attributes['foreignNames'])) {
            return null;
        }

        if (! $foreign = $this->foreignNames->reduce(function ($carry, $fn) {
            /** @var ForeignName $fn */
            $carry[$fn->language][] = $fn->name;

            return $carry;
        }, [])) {
            return null;
        }

        /** @var ExCollectionInterface $foreign */
        $foreign_text = implode(PHP_EOL, array_map(
            fn ($name, $language) => "$language: ".implode(', ', $name),
            $foreign->toArray(),
            $foreign->keys()
        ));

        return Button::new(Button::STYLE_SECONDARY, "FOREIGN_NAMES_{$this->id}")
            ->setLabel('Foreign Names')
            ->setListener(
                fn () => $interaction->sendFollowUpMessage(
                    MTG::createBuilder()->setContent($foreign_text),
                    true
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
    }

    /**
     * Gets a button to view the legal formats for the card.
     *
     * @param Interaction $interaction
     *
     * @return Button|null
     *
     * @since 0.6.0
     */
    public function getLegalitiesButton(Interaction $interaction): ?Button
    {
        if (! isset($this->attributes['legalities'])) {
            return null;
        }

        if (! $legalities = $this->legalities->reduce(function ($carry, $legality) {
            /** @var Legality $legality */
            $carry[$legality->legality][] = $legality->format;

            return $carry;
        }, [])) {
            return null;
        }

        /** @var ExCollectionInterface $legalities */
        $legalities_text = implode(PHP_EOL, array_map(
            fn ($formats, $legality) => "$legality: ".implode(', ', $formats),
            $legalities->toArray(),
            $legalities->keys()
        ));

        return Button::new(Button::STYLE_SECONDARY, "LEGALITIES_{$this->id}")
            ->setLabel('Legalities')
            ->setListener(
                fn () => $interaction->sendFollowUpMessage(
                    MTG::createBuilder()->setContent($legalities_text),
                    true
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
    }

    public function getRulingsButton(Interaction $interaction): ?Button
    {
        if (! isset($this->attributes['rulings'])) {
            return null;
        }

        if (! $rulings = $this->rulings->reduce(function ($carry, $ruling) {
            /** @var Ruling $ruling */
            $carry[$ruling->date][] = $ruling->text;

            return $carry;
        }, [])) {
            return null;
        }

        /** @var ExCollectionInterface $rulings */
        $rulings_text = implode(PHP_EOL, array_map(
            fn ($formats, $ruling) => PHP_EOL."$ruling: ".PHP_EOL.'- '.implode(PHP_EOL.'- ', $formats),
            $rulings->toArray(),
            $rulings->keys()
        ));

        return Button::new(Button::STYLE_SECONDARY, "RULINGS_{$this->id}")
            ->setLabel('Rulings')
            ->setListener(
                fn () => $interaction->sendFollowUpMessage(
                    MTG::createBuilder()->setContent($rulings_text),
                    true
                ),
                $this->getDiscord(),
                true, // One-time listener
                300 // delete listener after 5 minutes
            );
    }
}
