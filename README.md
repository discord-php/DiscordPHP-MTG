# DiscordPHP-MTG

A Magic: The Gathering API Library and bot for Discord, built using [DiscordPHP](https://github.com/discord-php/DiscordPHP).

## Features

- Search cards by any [MTG API](https://docs.magicthegathering.io/) filter (name, colors, type, set, format legality, …) and fetch a card by id
- Look sets up by name or block, and open a booster pack for a set
- Read the API reference lists: card types, subtypes, supertypes and game formats
- Rich card rendering for Discord: Components V2 container, mana/symbol emojis, plus buttons for the raw JSON, image, rulings, legalities, foreign names and set
- Ships as an installable slash-command bot (`/card_search`), user-installable and usable anywhere

## Requirements

- PHP 8.3 or higher
- Composer

## Installation

1. Clone the repository:
   ```cmd
   git clone https://github.com/discord-php/DiscordPHP-MTG.git
   cd DiscordPHP-MTG
   ```

2. Install dependencies:
   ```cmd
   composer install
   ```

## Usage

1. Copy and rename `.env.example` to `.env` and configure your bot token.
2. Run the bot:
   ```cmd
   php bot.php
   ```
3. Alternatively, package the bot into an executable binary:
   ```powershell
   composer run-script phpacker
   ```

### As a library

`MTG` extends the DiscordPHP client, so the MTG repositories hang off it and every call returns a promise:

```php
$mtg = new \MTG\MTG(['token' => getenv('TOKEN')]);

$mtg->cards->getCards(['name' => 'Black Lotus'])->then(fn ($cards) => $cards->first());
$mtg->cards->fetch('cardId')->then(fn ($card) => $card);
$mtg->sets->getSets(['name' => 'Khans of Tarkir'])->then(fn ($sets) => $sets->first());
$mtg->sets->generateBooster('KTK')->then(fn ($pack) => $pack);   // needs a self-hosted mtg-api

$mtg->getTypes();       // ['Artifact', 'Creature', 'Instant', …]
$mtg->getSubtypes();    // ['Elf', 'Equipment', …]
$mtg->getSupertypes();  // ['Basic', 'Legendary', 'Snow', …]
$mtg->getFormats();     // ['Standard', 'Modern', 'Commander', …]
```

Pass `mtg_api_key` in the options array to send an `X-Api-Key` header for a higher rate limit.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

This project is licensed under the MIT License. See [LICENSE.md](LICENSE.md) for details.
