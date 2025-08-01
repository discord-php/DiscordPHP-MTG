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
}
