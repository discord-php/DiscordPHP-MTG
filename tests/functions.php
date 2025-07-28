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

use MTG\MTG;
use Psr\Log\NullLogger;

const TIMEOUT = 10;

function wait(callable $callback, float $timeout = TIMEOUT, ?callable $timeoutFn = null)
{
    $mtg = MTGSingleton::get();

    $result = null;
    $finally = null;
    $timedOut = false;

    $mtg->getLoop()->futureTick(function () use ($callback, $mtg, &$result, &$finally) {
        $resolve = function ($x = null) use ($mtg, &$result) {
            $result = $x;
            $mtg->getLoop()->stop();
        };

        try {
            $finally = $callback($mtg, $resolve);
        } catch (\Throwable $e) {
            $resolve($e);
        }
    });

    $timeout = $mtg->getLoop()->addTimer($timeout, function () use ($mtg, &$timedOut) {
        $timedOut = true;
        $mtg->getLoop()->stop();
    });

    $mtg->getLoop()->run();
    $mtg->getLoop()->cancelTimer($timeout);

    if ($result instanceof Exception) {
        throw $result;
    }

    if (is_callable($finally)) {
        $finally();
    }

    if ($timedOut) {
        if ($timeoutFn != null) {
            $timeoutFn();
        } else {
            throw new \Exception('Timed out');
        }
    }

    return $result;
}

function getMockMtg(): MTG
{
    return new MTG(['token' => '', 'logger' => new NullLogger()]);
}
