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

namespace MTG;

use Discord\Discord;
use Discord\MessageCommandClient;
use Discord\Http\Drivers\React;
use Discord\Stats;
use MTG\Http\Http;
use MTG\Repository\CardRepository;
use MTG\Repository\SetRepository;

/**
 * The MTG client class — a DiscordPHP {@see MessageCommandClient} extended with
 * an async HTTP client for the "Magic: The Gathering Developers" REST API and
 * the card / set repositories that read it.
 *
 * @see \Discord\MessageCommandClient The DiscordPHP client this extends
 * @see CardRepository
 * @see SetRepository
 *
 * @version 1.0.0
 *
 * @property CardRepository $cards
 * @property SetRepository  $sets
 */
class MTG extends MessageCommandClient
{
    use HelperTrait;

    public const string GITHUB = 'https://github.com/discord-php/DiscordPHP-MTG';

    protected Stats $stats;

    /**
     * The extended HTTP client.
     *
     * @var Http Extended Discord HTTP client.
     */
    protected $mtg_http;

    /**
     * The extended Client class.
     *
     * @var Client Extended Discord client.
     */
    protected $client;

    /**
     * @param array $options Options passed straight to the DiscordPHP client, plus
     *                       `socket_options` for the HTTP driver. After the parent
     *                       boots, the MTG HTTP client, the {@see Client} part and
     *                       the {@see Stats} tracker are wired up.
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->mtg_http = new Http(
            'Bot '.$this->token,
            $this->loop,
            $this->options['logger'] ?? null,
            new React($this->loop, $options['socket_options'] ?? [])
        );
        $this->client = $this->factory->part(Client::class, (array) $this->client);
        $this->stats = Stats::new($this);
    }

    /**
     * Gets the MTG HTTP client.
     *
     * @return Http
     */
    public function getMtgHttpClient(): Http
    {
        return $this->mtg_http;
    }

    /**
     * Handles dynamic get calls to the client.
     *
     * @param string $name Variable name.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        static $allowed = ['loop', 'options', 'logger', 'http', 'mtg_http', 'application_commands'];

        if (in_array($name, $allowed)) {
            return $this->{$name};
        }

        if (null === $this->client) {
            return;
        }

        return $this->client->{$name};
    }
}
