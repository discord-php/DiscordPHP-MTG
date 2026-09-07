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
use MTG\Http\Endpoint;
use MTG\Http\Http;
use MTG\Repository\CardRepository;
use MTG\Repository\SetRepository;
use Psr\Log\NullLogger;
use React\Promise\PromiseInterface;

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
     *                       `socket_options` for the HTTP driver and an optional
     *                       `mtg_api_key` (`X-Api-Key`, raises the MTG API rate
     *                       limit). After the parent boots, the MTG HTTP client,
     *                       the {@see Client} part and the {@see Stats} tracker
     *                       are wired up.
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->mtg_http = new Http(
            '', // The MTG API is unauthenticated — never forward the Discord bot token to it.
            $this->loop,
            $this->options['logger'] ?? new NullLogger(),
            new React($this->loop, $options['socket_options'] ?? []),
            $options['mtg_api_key'] ?? null,
        );
        $this->client = $this->factory->part(Client::class, (array) $this->client);
        $this->stats = Stats::new($this);
    }

    /**
     * Fetches the API's list of all card types (e.g. `Creature`, `Instant`).
     *
     * @link https://docs.magicthegathering.io/#api_v1types_list
     *
     * @return PromiseInterface<string[]>
     */
    public function getTypes(): PromiseInterface
    {
        return $this->mtg_http->get(new Endpoint(Endpoint::TYPES))->then(static fn ($response) => (array) ($response->types ?? []));
    }

    /**
     * Fetches the API's list of all card subtypes (e.g. `Elf`, `Equipment`).
     *
     * @link https://docs.magicthegathering.io/#api_v1subtypes_list
     *
     * @return PromiseInterface<string[]>
     */
    public function getSubtypes(): PromiseInterface
    {
        return $this->mtg_http->get(new Endpoint(Endpoint::SUBTYPES))->then(static fn ($response) => (array) ($response->subtypes ?? []));
    }

    /**
     * Fetches the API's list of all card supertypes (e.g. `Legendary`, `Snow`).
     *
     * @link https://docs.magicthegathering.io/#api_v1supertypes_list
     *
     * @return PromiseInterface<string[]>
     */
    public function getSupertypes(): PromiseInterface
    {
        return $this->mtg_http->get(new Endpoint(Endpoint::SUPERTYPES))->then(static fn ($response) => (array) ($response->supertypes ?? []));
    }

    /**
     * Fetches the API's list of all game formats (e.g. `Standard`, `Commander`).
     *
     * @link https://docs.magicthegathering.io/#api_v1formats_list
     *
     * @return PromiseInterface<string[]>
     */
    public function getFormats(): PromiseInterface
    {
        return $this->mtg_http->get(new Endpoint(Endpoint::FORMATS))->then(static fn ($response) => (array) ($response->formats ?? []));
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
