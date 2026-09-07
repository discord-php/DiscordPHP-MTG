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

use Discord\Helpers\ExCollectionInterface;
use MTG\MTG;
use MTG\Parts\Card;
use MTG\Parts\Set;
use PHPUnit\Framework\TestCase;

/**
 * Live end-to-end tests against api.magicthegathering.io — network-bound, so
 * they exercise the whole request path rather than isolating a unit.
 */
final class MTGTest extends TestCase
{
    /**
     * @covers \MTG\Repository\CardRepository
     * @covers \MTG\Parts\Card
     */
    public function testCardInfoRetrieval()
    {
        wait(function (MTG $mtg, $resolve) {
            /** @var Card $card */
            $card = $mtg->getFactory()->part(Card::class);
            $card->setPageSize(1);
            $mtg->cards->getCards(['name' => 'Black Lotus'])->then(function (ExCollectionInterface $cards) {
                $this->assertInstanceOf(ExCollectionInterface::class, $cards);
                $this->assertInstanceOf(Card::class, $cards->first());
            })->then($resolve, $resolve);
        }, 10);
    }

    /**
     * @covers \MTG\Repository\SetRepository
     * @covers \MTG\Parts\Set
     */
    public function testSetLookupByCode()
    {
        wait(function (MTG $mtg, $resolve) {
            $mtg->sets->getSets(['name' => 'Khans of Tarkir'])->then(function (ExCollectionInterface $sets) {
                $this->assertInstanceOf(Set::class, $sets->first());
                $this->assertSame('KTK', $sets->first()->code);
            })->then($resolve, $resolve);
        }, 10);
    }

    /**
     * @covers \MTG\MTG::getTypes
     * @covers \MTG\MTG::getSubtypes
     * @covers \MTG\MTG::getSupertypes
     * @covers \MTG\MTG::getFormats
     */
    public function testReferenceListsAreNonEmpty()
    {
        wait(function (MTG $mtg, $resolve) {
            \React\Promise\all([
                $mtg->getTypes(),
                $mtg->getSubtypes(),
                $mtg->getSupertypes(),
                $mtg->getFormats(),
            ])->then(function (array $lists) {
                foreach ($lists as $list) {
                    $this->assertIsArray($list);
                    $this->assertNotEmpty($list);
                }
                [$types, , $supertypes] = $lists;
                $this->assertContains('Creature', $types);
                $this->assertContains('Legendary', $supertypes);
            })->then($resolve, $resolve);
        }, 15);
    }
}
