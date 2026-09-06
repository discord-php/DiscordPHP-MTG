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
 * One entry from a {@see Card}'s `legalities` array — whether the card is
 * `Legal` / `Banned` / `Restricted` in a given format. Not a standalone
 * endpoint; it only appears inside the card response.
 *
 * @link https://docs.magicthegathering.io/#api_v1cards_get Card object (see the `legalities` field)
 *
 * @see \MTG\Parts\Card The parent object
 *
 * @property string $format   The format of the card.
 * @property string $legality The legality status of the card in the format.
 *
 * @since 0.3.0
 */
class Legality extends Part
{
    /**
     * @inheritDoc
     */
    protected $fillable = [
        'format',
        'legality',
    ];
}
