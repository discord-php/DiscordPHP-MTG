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

use Discord\Parts\Part;

/**
 * One entry from a {@see Card}'s `rulings` array — an official clarification,
 * with the date it was issued. Not a standalone endpoint; it only appears
 * inside the card response.
 *
 * @link https://docs.magicthegathering.io/#api_v1cards_get Card object (see the `rulings` field)
 *
 * @see \MTG\Parts\Card The parent object
 *
 * @property string $date The date the ruling was issued.
 * @property string $text The text content of the ruling.
 *
 * @since 0.3.0
 */
class Ruling extends Part
{
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'date',
        'text',
    ];
}
