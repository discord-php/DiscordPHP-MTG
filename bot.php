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

use \Exception;
//use Clue\React\Redis\Factory as Redis;
use Discord\Parts\Channel\Channel;
use Discord\Parts\User\User;
//use Discord\Helpers\CacheConfig;
use Discord\WebSockets\Intents;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use MTG\Parts\Card;
use MTG\Repository\CardsRepository;
use React\EventLoop\Loop;

use function React\Async\async;
use function React\Promise\set_rejection_handler;

$technician_id = getenv('technician_id') ?: '116927250145869826'; // Default to Valithor Obsidion's ID

ini_set('zend.assertions', '1'); // Enable assertions for development

define('MTGCARDINFOBOT_START', microtime(true));
ini_set('display_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ignore_user_abort(true);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1'); // Unlimited memory usage
define('MAIN_INCLUDED', 1); // Token and SQL credential files may be protected locally and require this to be defined to access

//if (! $token_included = require getcwd() . '/token.php') // $token
    //throw new \Exception('Token file not found. Create a file named token.php in the root directory with the bot token.');
if (! $autoloader = require file_exists(__DIR__.'/vendor/autoload.php') ? __DIR__.'/vendor/autoload.php' : __DIR__.'/../../autoload.php') {
    throw new \Exception('Composer autoloader not found. Run `composer update` and try again.');
}

function loadEnv(string $filePath = __DIR__ . '/.env'): void
{
    if (! file_exists($filePath)) throw new Exception("The .env file does not exist.");

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $trimmedLines = array_map('trim', $lines);
    $filteredLines = array_filter($trimmedLines, fn($line) => $line && ! str_starts_with($line, '#'));

    array_walk($filteredLines, function($line) {
        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if (! array_key_exists($name, $_ENV)) putenv(sprintf('%s=%s', $name, $value));
    });
}
loadEnv(getcwd() . '/.env');

$streamHandler = new StreamHandler('php://stdout', Level::Debug);
$streamHandler->setFormatter(new LineFormatter(null, null, true, true, true));
$logger = new Logger('MTGCARDINFOBOT', [$streamHandler]);
//file_put_contents('output.log', ''); // Clear the contents of 'output.log'
//$logger->pushHandler(new StreamHandler('output.log', Level::Debug));
$logger->info('Loading configurations for the bot...');
set_rejection_handler(function(\Throwable $e) use ($logger): void
{
    //if ($e->getMessage() === 'Cannot resume a fiber that is not suspended') return;
    $logger->warning("Unhandled Promise Rejection: {$e->getMessage()} [{$e->getFile()}:{$e->getLine()}] " . str_replace('#', '\n#', $e->getTraceAsString()));
});

$mtg = new MTG([
    'loop' => Loop::get(),
    'logger' => $logger,
    /*
    'cache' => new CacheConfig(
        $interface = new RedisCache(
            (new Redis(Loop::get()))->createLazyClient('127.0.0.1:6379'),
            'dphp:cache:
        '),
        $compress = true, // Enable compression if desired
        $sweep = false // Disable automatic cache sweeping if desired
    ),
    */
    'socket_options' => [
        'dns' => '8.8.8.8',
    ],
    'token' => getenv('TOKEN'),
    //'loadAllMembers' => true,
    'storeMessages' => true, // Only needed if messages need to be stored in the cache
    'intents' => Intents::getDefaultIntents() /*| Intents::GUILD_MEMBERS | Intents::GUILD_PRESENCES*/ | Intents::MESSAGE_CONTENT,
    'useTransportCompression' => false, // Disable zlib-stream
    'usePayloadCompression' => true,
]);

$webapi = null;
$socket = null;

$global_error_handler = async(function (int $errno, string $errstr, ?string $errfile, ?int $errline) use (&$mtg, &$logger, &$technician_id) {
    /** @var ?MTG $mtg */
    if (
        $mtg // If the bot is running
        // fsockopen
        && ! str_ends_with($errstr, 'Connection timed out') 
        && ! str_ends_with($errstr, '(Connection timed out)')
        && ! str_ends_with($errstr, 'Connection refused') // Usually happens if the verifier server doesn't respond quickly enough
        && ! str_contains($errstr, '(Connection refused)') // Usually happens in localServerPlayerCount
        //&& ! str_ends_with($errstr, 'Network is unreachable')
        //&& ! str_ends_with($errstr, '(Network is unreachable)')
        && ! str_ends_with($errstr, '(A connection attempt failed because the connected party did not properly respond after a period of time, or established connection failed because connected host has failed to respond)')

        // Connectivity issues
        && ! str_ends_with($errstr, 'No route to host') // Usually happens if the verifier server is down
        && ! str_ends_with($errstr, 'No address associated with hostname') // Either the DNS or the VPS is acting up
        && ! str_ends_with($errstr, 'Temporary failure in name resolution') // Either the DNS or the VPS is acting up
        && ! str_ends_with($errstr, 'Bad Gateway') // Usually happens if the verifier server's PHP-CGI is down
        //&& ! str_ends_with($errstr, 'HTTP request failed!')

        //&& ! str_contains($errstr, 'Undefined array key')
    )
    {
        $logger->error($msg = sprintf("[%d] Fatal error on `%s:%d`: %s\nBacktrace:\n```\n%s\n```", $errno, $errfile, $errline, $errstr, implode("\n", array_map(fn($trace) => ($trace['file'] ?? '') . ':' . ($trace['line'] ?? '') . ($trace['function'] ?? ''), debug_backtrace()))));
        if (! getenv('testing')) {
            $promise = $mtg->users->fetch($technician_id);
            $promise = $promise->then(fn (User $user) => $user->getPrivateChannel());
            $promise = $promise->then(fn (Channel $channel) => $channel->sendMessage(MTG::createBuilder()->setContent($msg)));
        }
    }
});
set_error_handler($global_error_handler);

use React\Socket\SocketServer;
use React\Http\HttpServer;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
$socket = new SocketServer(
    sprintf('%s:%s', '0.0.0.0', getenv('http_port') ?: 55555),
    [
        'tcp' => [
            'so_reuseport' => true
        ]
    ],
    Loop::get()
);
/**
 * Handles the HTTP request using the HttpServiceManager.
 *
 * @param ServerRequestInterface $request The HTTP request object.
 * @return Response The HTTP response object.
 */
$webapi = new HttpServer(Loop::get(), async(function (ServerRequestInterface $request) use (&$mtg, &$logger): Response
{
    /** @var ?MTG $mtg */
    if (! $mtg || ! $mtg instanceof MTG) {
        $logger->warning('MTG instance not found. Please check the server settings.');
        return new Response(Response::STATUS_SERVICE_UNAVAILABLE, ['Content-Type' => 'text/plain'], 'Service Unavailable');
    }
    return new Response(Response::STATUS_IM_A_TEAPOT, ['Content-Type' => 'text/plain'], 'Service Not Yet Implemented');
}));

/**
 * This code snippet handles the error event of the web API.
 * It logs the error message, file, line, and trace, and handles specific error cases.
 * If the error message starts with 'Received request with invalid protocol version', it is ignored.
 * If the error message starts with 'The response callback', it triggers a restart process.
 * The restart process includes sending a message to a specific Discord channel and closing the socket connection.
 * After a delay of 5 seconds, the script is restarted by calling the 'restart' function and closing the Discord connection.
 *
 * @param Exception $e The exception object representing the error.
 * @param \Psr\Http\Message\RequestInterface|null $request The HTTP request object associated with the error, if available.
 * @param object $mtg The main object of the application.
 * @param object $socket The socket object.
 * @param bool $testing Flag indicating if the script is running in testing mode.
 * @return void
 */
$webapi->on('error', async(function (Exception $e, ?\Psr\Http\Message\RequestInterface $request = null) use (&$mtg, &$logger, &$socket, $technician_id) {
    if (
        str_starts_with($e->getMessage(), 'Received request with invalid protocol version')
    ) return; // Ignore this error, it's not important
    $error = "[WEBAPI] {$e->getMessage()} [{$e->getFile()}:{$e->getLine()}] " . str_replace('\n', PHP_EOL, $e->getTraceAsString());
    $logger->error("[WEBAPI] $error");
    if ($request) $logger->error('[WEBAPI] Request: ' .  preg_replace('/(?<=key=)[^&]+/', '********', $request->getRequestTarget()));
    if (str_starts_with($e->getMessage(), 'The response callback')) {
        $logger->info('[WEBAPI] ERROR - RESTART');
        /** @var ?MTG $mtg */
        if (! $mtg) return;
        if (! getenv('testing')) {
            $promise = $mtg->users->fetch($technician_id);
            $promise = $promise->then(fn (User $user) => $user->getPrivateChannel());
            $promise = $promise->then(fn (Channel $channel) => $channel->sendMessage(MTG::createBuilder()->setContent('Restarting due to error in HttpServer API...')));
        }
        $socket->close();
    }
}));

$mtg->on('init', function (MTG $mtg) {
    /** @var Card $card */
    $card = $mtg->getFactory()->part(Card::class);
    $card->setRandom(true);
    //$promise = $mtg->cards->getCardInfo($card);
    
    /*$mtg->cards->freshen()->then(function (CardsRepository $repository) {
        var_dump($repository->first());
    });*/

    $mtg->cards->fetch('5f8287b1-5bb6-5f4c-ad17-316a40d5bb0c')->then(function ($card) {
        var_dump($card);
    });
});

$mtg->run();