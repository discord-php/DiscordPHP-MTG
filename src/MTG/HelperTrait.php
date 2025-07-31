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

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message\AllowedMentions;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Guild\Emoji;
use Discord\Repository\EmojiRepository;

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
     * 
     * @since 0.1.0
     */
    public static function createBuilder(bool $prevent_mentions = false): MessageBuilder
    {
        $builder = MessageBuilder::new();
        if ($prevent_mentions) {
            $builder->setAllowedMentions(AllowedMentions::none());
        }

        return $builder;
    }

    /**
     * Creates and returns a new Embed instance with optional footer, color, timestamp, and URL.
     *
     * @param bool|null $footer Whether to include the default footer in the embed. Defaults to true.
     * @param int       $color  The color to set for the embed. Defaults to 0xE1452D.
     *
     * @return Embed
     * 
     * @since 0.1.0
     */
    public function createEmbed(?bool $footer = true, int $color = 0xE1452D): Embed
    {
        assert($this instanceof MTG);

        $embed = new Embed($this);
        if ($footer) {
            $embed->setFooter(MTG::EMBED_FOOTER);
        }

        return $embed
            ->setColor($color)
            ->setTimestamp()
            ->setURL('');
    }

    /**
     * Converts a card's encapsulated name to its corresponding emoji representation.
     * The name of the emoji should be stored in the application with a trailing underscore, e.g. U_.
     *
     * @param string $name The encapsulated name to convert, e.g. {U}.
     *
     * @return string The emoji representation of the encapsulated name.
     * 
     * @since 0.4.0
     */
    public function encapsulatedSymbolsToEmojis(string $subject): string
    {
        preg_match_all('/\{([a-zA-Z0-9]+)\}/', $subject, $matches);
        foreach ($matches as $array) {
            foreach ($array as $search) {
                if (str_starts_with($search, '{')) {
                    continue;
                }
                if ($replaced = $this->__encapsulatedSymbolsToEmojis($subject, $search)) {
                    $subject = $replaced;
                    continue;
                }
                if (is_numeric($search)) {
                    if ($replaced = str_replace('{'.$search.'}', $this->__createColorless($search), $subject)) {
                        $subject = $replaced;
                    }
                    continue;
                }
            }
        }

        return $subject;
    }

    /**
     * Converts encapsulated symbol placeholders within a string to their corresponding emoji representations.
     *
     * @param string $subject The input string containing symbol placeholders to be replaced.
     * @param string $search  The symbol name to search for and replace with its emoji.
     *
     * @return string|null The string with symbols replaced by emojis, or null if no emoji is found.
     *
     * @since 0.4.0
     */
    public function __encapsulatedSymbolsToEmojis(string $subject, string $search): ?string
    {
        /** @var EmojiRepository $emojis */
        $emojis = $this->emojis;

        if (! $emoji = $emojis->get('name', $search.'_')) {
            return null;
        }

        return self::encapsulated_emoji_str_replace($search, $emoji, $subject);
    }

    /**
     * Generates a string representation of colorless mana symbols based on the given numeric value.
     *
     * @param string $numeric The numeric value representing the amount of colorless mana.
     *
     * @return string|null The string of colorless mana emojis, or null if emojis are not found.
     *
     * @since 0.4.0
     */
    protected function __createColorless(string $numeric): ?string
    {
        /** @var EmojiRepository $emojis */
        $emojis = $this->emojis;

        if (! $zero = $emojis->get('name', '0_')) {
            return null;
        }
        
        if (! $colorless = $emojis->get('name', 'C_')) {
            return null;
        }

        /** @var Emoji $zero */
        /** @var Emoji $colorless */
        return ($numeric === 0)
            ? (string) $zero
            : str_repeat((string) $colorless, (int) $numeric);
    }

    /**
     * Replaces a placeholder in the given string with the string representation of an Emoji object.
     *
     * @param string $string The input string containing the placeholder.
     * @param string $value  The value to be replaced, used as the placeholder inside curly braces.
     * @param Emoji  $emoji  The Emoji object whose string representation will replace the placeholder.

     * @return string The resulting string with the placeholder replaced by the emoji.
     * 
     * @since 0.4.0
     */
    public static function encapsulated_emoji_str_replace(string $search, Emoji $emoji, string $subject): string
    {
        return str_replace('{'.$search.'}', (string) $emoji, $subject);
    }

    /**
     * Converts a color identity string to its corresponding integer representation.
     *
     * @param string|null $identity The color identity string to convert.
     *
     * @return int|null
     * 
     * @since 0.4.0
     */
    public static function colorIdentityToInteger(?string $identity): ?int
    {
        switch($identity) {
            case null:
                return \Discord\COLORTABLE['gray'];
            case 'W':
                return \Discord\COLORTABLE['white'];
            case 'U':
                return \Discord\COLORTABLE['blue'];
            case 'B':
                return \Discord\COLORTABLE['black'];
            case 'R':
                return \Discord\COLORTABLE['red'];
            case 'G':
                return \Discord\COLORTABLE['green'];
        }

        return null;
    }
}
