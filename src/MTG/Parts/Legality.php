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
 * Represents a legality associated with a card.
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
