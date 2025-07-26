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

namespace MTG;

use Discord\Http\Http as DiscordHttp;

/**
 * Discord HTTP client.
 *
 * @author Valithor Obsidion <valithor@discordphp.org>
 */
class Http extends DiscordHttp
{
    /**
     * MTG Http version.
     *
     * @var string
     */
    public const MTG_VERSION = 'v1.0.0';

    /**
     * Current MTG HTTP API version.
     *
     * @var string
     */
    public const MTG_HTTP_API_VERSION = 1;

    /**
     * MTG API base URL.
     *
     * @var string
     */
    public const MTG_BASE_URL = 'https://api.magicthegathering.io/v'.self::MTG_HTTP_API_VERSION;
}