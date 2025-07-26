<?php

declare(strict_types=1);

/*
 * This file is a part of the MTG Card Info App project.
 *
 * Copyright (c) 2025-present Valithor Obsidion <valithor@valzargaming.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace MTG\Http;

use Discord\Http\Endpoint as DiscordEndpoint;

class Endpoint extends DiscordEndpoint
{
    // GET
    public const GATEWAY = 'gateway';
    // GET
    public const CARDS = 'cards';
    // GET
    public const CARD = self::CARDS.'/:id';
    // GET
    public const SETS = 'sets';
    // GET
    public const SET = self::SETS.'/:id';
    // GET
    public const SETS_BOOSTER = self::SETS.'/:id/booster';
    // GET
    public const TYPES = 'types';
    // GET
    public const SUBTYPES = 'subtypes';
    // GET
    public const SUPERTYPES = 'supertypes';
    // GET
    public const FORMATS = 'formats';
}
