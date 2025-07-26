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

namespace MTG\Http\Drivers;

use Discord\Http\Drivers\React as DiscordReact;
use MTG\Http\Request;
use React\Promise\PromiseInterface;

class React extends DiscordReact
{
    /**
     * Runs the request using the React HTTP client.
     * 
     * @param Request $request The request to run.
     * 
     * @return PromiseInterface
     */
    public function runRequest($request): PromiseInterface
    {
        return $this->browser->{$request->getMethod()}(
            $request->getUrl(),
            $request->getHeaders(),
            $request->getContent()
        );
    }
}