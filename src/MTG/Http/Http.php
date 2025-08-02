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

use Discord\Http\DriverInterface;
use Discord\Http\Endpoint;
use Discord\Http\HttpInterface;
use Discord\Http\HttpTrait;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use SplQueue;

/**
 * Discord HTTP client.
 *
 * @author Valithor Obsidion <valithor@discordphp.org>
 */
class Http implements HttpInterface
{
    use HttpTrait;

    /**
     * MTG Http version.
     *
     * @var string
     */
    public const VERSION = 'v1.0.0';

    /**
     * Current MTG HTTP API version.
     *
     * @var string
     */
    public const HTTP_API_VERSION = 1;

    /**
     * MTG API base URL.
     *
     * @var string
     */
    public const BASE_URL = 'https://api.magicthegathering.io/v'.self::HTTP_API_VERSION;

    /**
     * Authentication token.
     *
     * @var string
     */
    private $token;

    /**
     * Logger for HTTP requests.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * HTTP driver.
     *
     * @var DriverInterface
     */
    protected $driver;

    /**
     * ReactPHP event loop.
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Array of request buckets.
     *
     * @var Bucket[]
     */
    protected $buckets = [];

    /**
     * The current rate-limit.
     *
     * @var RateLimit
     */
    protected $rateLimit;

    /**
     * Timer that resets the current global rate-limit.
     *
     * @var TimerInterface
     */
    protected $rateLimitReset;

    /**
     * Request queue to prevent API
     * overload.
     *
     * @var SplQueue
     */
    protected $queue;

    /**
     * Request queue to prevent API
     * overload.
     *
     * @var SplQueue
     */
    protected $interactionQueue;

    /**
     * Number of requests that are waiting for a response.
     *
     * @var int
     */
    protected $waiting = 0;

    /**
     * Whether react/promise v3 is used, if false, using v2.
     */
    protected $promiseV3 = true;

    /**
     * Http wrapper constructor.
     *
     * @param string               $token
     * @param LoopInterface        $loop
     * @param DriverInterface|null $driver
     */
    public function __construct(string $token, LoopInterface $loop, LoggerInterface $logger, ?DriverInterface $driver = null)
    {
        $this->token = $token;
        $this->loop = $loop;
        $this->logger = $logger;
        $this->driver = $driver;
        $this->queue = new SplQueue();
        $this->interactionQueue = new SplQueue();
    }

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
