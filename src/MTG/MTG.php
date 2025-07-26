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

use Discord\Discord;
use Discord\Http\Drivers\React;
use Discord\Stats;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use React\Promise\PromiseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function React\Promise\reject;

/**
 * The MTG client class.
 * 
 * @version 1.0.0
 * 
 * @property CardRepository $cards
 */
class MTG extends Discord
{
    use HelperTrait;

    public const string EMBED_FOOTER = '';

    protected Browser $browser;

    protected Stats $stats;

    /**
     * The extended HTTP client.
     *
     * @var Http Extended Discord HTTP client.
     */
    protected $http;

    /**
     * The extended Client class.
     *
     * @var Client Extended Discord client.
     */
    protected $client;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->http = new Http(
            'Bot '.$this->token,
            $this->loop,
            $this->options['logger'],
            new React($this->loop, $options['socket_options'])
        );
        $this->client = $this->factory->part(Client::class, (array) $this->client);
        $this->stats = Stats::new($this);
    }
}