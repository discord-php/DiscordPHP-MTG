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
 * Foreign language names for the card, if this card in this set was printed in another language. Not available for all sets.
 *
 * @property string $language     The language of the card.
 * @property string $name         The name of the card in the foreign language.
 * @property string $multiverseid The multiverse ID of the card.
 *
 * @since 0.3.0
 */
class ForeignName extends Part
{
    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'language',
        'name',
        'multiverseid',
    ];
}
