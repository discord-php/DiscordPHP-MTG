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

namespace MTG\Http;

use Discord\Http\Request as DiscordRequest;

/**
 * A single queued HTTP request against the MTG API. Identical in behaviour to
 * the DiscordPHP request it extends; it exists only so MTG transport code
 * depends on an MTG-owned type.
 *
 * @see \Discord\Http\Request The DiscordPHP request this extends
 * @see \MTG\Http\Http Where these are created and sorted into rate-limit buckets
 *
 * @author Valithor Obsidion <valithor@discordphp.org>
 *
 * @since 0.1.0
 */
class Request extends DiscordRequest
{
    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return Http::BASE_URL.'/'.$this->url;
    }
}
