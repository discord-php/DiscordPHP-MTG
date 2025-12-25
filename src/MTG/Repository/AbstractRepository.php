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

use Discord\Repository\AbstractRepository as DiscordAbstractRepository;
use MTG\Http\Http;
use MTG\MTG;

/**
 * Repositories provide a way to store and update parts on the Discord server.
 *
 * @author Valithor Obsidion <valithor@discordphp.org>
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
     * @param MTG   $mtg
     * @param array $vars An array of variables used for the endpoint.
     */
    public function __construct(protected $mtg, array $vars = [])
    {
        parent::__construct($mtg, $vars);
        $this->mtg_http = $mtg->getMtgHttpClient();
    }
}
