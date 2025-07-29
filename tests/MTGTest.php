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

use Discord\Helpers\ExCollectionInterface;
use MTG\MTG;
use MTG\Parts\Card;
use PHPUnit\Framework\TestCase;

final class MTGTest extends TestCase
{
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
}
