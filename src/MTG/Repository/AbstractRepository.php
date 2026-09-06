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

namespace MTG\Repository;

use Discord\Discord;
use Discord\Repository\AbstractRepository as DiscordAbstractRepository;
use MTG\Http\Http;
use MTG\MTG;

/**
 * Base class for the MTG read-only repositories: DiscordPHP's
 * {@see DiscordAbstractRepository} behaviour (keyed, cached collection of
 * Parts) backed by {@see \MTG\Http\Http} and MTG {@see \MTG\Http\Endpoint}s
 * instead of the Discord API. Concrete repositories ({@see CardRepository},
 * {@see SetRepository}) declare their endpoint map and Part class.
 *
 * @see \Discord\Repository\AbstractRepository The upstream this extends
 *
 * @author Valithor Obsidion <valithor@discordphp.org>
 *
 * @since 0.1.0
 */
abstract class AbstractRepository extends DiscordAbstractRepository
{
    use AbstractRepositoryTrait;

    /**
     * The extended HTTP client.
     *
     * @var Http Client.
     */
    protected $mtg_http;

    /**
     * AbstractRepository constructor.
     *
     * @param MTG|Discord $discord
     * @param array       $vars    An array of variables used for the endpoint.
     */
    public function __construct(protected $discord, array $vars = [])
    {
        parent::__construct($discord, $vars);
        $this->mtg_http = $discord->getMtgHttpClient();
    }
}
