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

use Discord\Http\Endpoint;
use Discord\Http\Http as DiscordHttp;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

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

    /**
     * Authentication token.
     *
     * @var string
     */
    private $token;

    /**
     * Builds and queues a request.
     *
     * @param string   $method
     * @param Endpoint $url
     * @param mixed    $content
     * @param array    $headers
     *
     * @return PromiseInterface
     */
    public function queueRequest(string $method, Endpoint $url, $content, array $headers = []): PromiseInterface
    {
        $deferred = new Deferred();

        if (is_null($this->driver)) {
            $deferred->reject(new \Exception('HTTP driver is missing.'));

            return $deferred->promise();
        }

        $headers = array_merge($headers, [
            'User-Agent' => $this->getUserAgent(),
            'Authorization' => $this->token,
            'X-Ratelimit-Precision' => 'millisecond',
        ]);

        $baseHeaders = [
            'User-Agent' => $this->getUserAgent(),
            'Authorization' => $this->token,
            'X-Ratelimit-Precision' => 'millisecond',
        ];

        if (! is_null($content) && ! isset($headers['Content-Type'])) {
            $baseHeaders = array_merge(
                $baseHeaders,
                $this->guessContent($content)
            );
        }

        $headers = array_merge($baseHeaders, $headers);

        $request = new Request($deferred, $method, $url, $content ?? '', $headers);
        $this->sortIntoBucket($request);

        return $deferred->promise();
    }
}
