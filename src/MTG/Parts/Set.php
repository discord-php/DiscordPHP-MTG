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
use Discord\Builders\Components\Container;
use Discord\Builders\Components\TextDisplay;
use Discord\Parts\Part;

/**
 * Represents a Magic: The Gathering set.
 *
 * @since 0.5.0
 */
class Set extends Part
{
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'name',
        'block',
        // The fields below are also part of the response (if not null), but cannot currently be used as query parameters
        'code',
        'gathererCode',
        'oldCode',
        'magicCardsInfoCode',
        'releaseDate',
        'border',
        'expansion',
        'onlineOnly',
        'booster',
    ];

    /**
     * Gets the release date of the set.
     *
     * @return ?Carbon|null
     *
     * @since 0.5.0
     */
    public function getReleaseDateAttribute(): ?Carbon
    {
        if (! isset($this->attributes['releaseDate'])) {
            return null;
        }

        return Carbon::parse($this->attributes['releaseDate']);
    }

    /**
     * Converts the set to a container with components.
     *
     * @return Container|null
     *
     * @since 0.5.0
     */
    public function toContainer(): ?Container
    {
        if (! isset($this->attributes['name'])) {
            return null;
        }

        $components = [
            TextDisplay::new('Code: '.$this->code),
            TextDisplay::new('Name: '.$this->name)
        ];

        if (isset($this->attributes['block'])) {
            $components[] = TextDisplay::new('Block: '.$this->block);
        }

        if (isset($this->attributes['releaseDate'])) {
            $components[] = TextDisplay::new('Release Date: '.$this->attributes['releaseDate']);
        }

        if (isset($this->attributes['onlineOnly'])) {
            $components[] = TextDisplay::new('Online Only: '.($this->attributes['onlineOnly'] ? 'Yes' : 'No'));
        }

        return Container::new()->addComponents($components);
    }
}
