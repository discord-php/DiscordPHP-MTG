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

namespace MTG\Http;

use Discord\Http\EndpointInterface;
use Discord\Http\EndpointTrait;

class Endpoint implements EndpointInterface
{
    use EndpointTrait;

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

    /**
     * Regex to identify parameters in endpoints.
     *
     * @var string
     */
    public const REGEX = '/:([^\/]*)/';

    /**
     * The string version of the endpoint, including all parameters.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Array of placeholders to be replaced in the endpoint.
     *
     * @var string[]
     */
    protected $vars = [];

    /**
     * Array of arguments to substitute into the endpoint.
     *
     * @var string[]
     */
    protected $args = [];

    /**
     * Array of query data to be appended
     * to the end of the endpoint with `http_build_query`.
     *
     * @var array
     */
    protected $query = [];

    /**
     * Creates an endpoint class.
     *
     * @param string $endpoint
     */
    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;

        if (preg_match_all(self::REGEX, $endpoint, $vars)) {
            $this->vars = $vars[1] ?? [];
        }
    }
}
