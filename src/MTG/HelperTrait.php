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

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message\AllowedMentions;
use Discord\Parts\Embed\Embed;

trait HelperTrait
{
    /**
     * Creates a new instance of MessageBuilder.
     *
     * Optionally prevents mentions in the message by setting allowed mentions to none.
     *
     * @param bool $prevent_mentions Whether to prevent mentions in the message. Defaults to false.
     * 
     * @return MessageBuilder
     */
    public static function createBuilder(bool $prevent_mentions = false): MessageBuilder
    {
        $builder = MessageBuilder::new();
        if ($prevent_mentions) $builder->setAllowedMentions(AllowedMentions::none());
        return $builder;
    }

    /**
     * Creates and returns a new Embed instance with optional footer, color, timestamp, and URL.
     *
     * @param bool|null $footer Whether to include the default footer in the embed. Defaults to true.
     * @param int       $color  The color to set for the embed. Defaults to 0xE1452D.
     * 
     * @return Embed
     */
    public function createEmbed(?bool $footer = true, int $color = 0xE1452D): Embed
    {
        assert($this instanceof MTG);

        $embed = new Embed($this);
        if ($footer) $embed->setFooter(MTG::EMBED_FOOTER);
        return $embed
            ->setColor($color)
            ->setTimestamp()
            ->setURL('');
    }
}