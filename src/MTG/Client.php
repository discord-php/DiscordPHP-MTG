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

namespace MTG;

use Discord\Parts\User\Client as DiscordClient;
use Discord\Repository\EmojiRepository;
use Discord\Repository\GuildRepository;
use Discord\Repository\PrivateChannelRepository;
use Discord\Repository\SoundRepository;
use Discord\Repository\UserRepository;
use MTG\Repository\CardsRepository;

class Client extends DiscordClient
{
    /**
     * @inheritDoc
     */
    protected $repositories = [
        'emojis' => EmojiRepository::class,
        'guilds' => GuildRepository::class,
        'private_channels' => PrivateChannelRepository::class,
        'sounds' => SoundRepository::class,
        'users' => UserRepository::class,
        'cards' => CardsRepository::class,
    ];
}
