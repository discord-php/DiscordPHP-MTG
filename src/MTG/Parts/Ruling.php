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
 * Represents a ruling associated with a card.
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
