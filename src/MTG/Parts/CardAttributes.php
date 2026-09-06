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

namespace MTG\Parts;

/**
 * This query will return a maximum of 100 cards.
 *
 * Paginate the response using the page parameter.
 *
 * Each field below can be used as a query parameter. By default, fields that have a singular value such as rarity, set, and name will always use a logical “or” operator when querying with a list of values. Fields that can have multiple values such as colors, supertypes, and subtypes can use a logical “and” or a logical “or” operator.
 *
 * The accepted delimiters when querying fields are the pipe character or a comma character. The pipe represents a logical “or”, and a comma represents a logical “and”. The comma can only be used with fields that accept multiple values (like colors).
 *
 * Example:name=nissa, worldwaker|jace|ajani, caller More examples: colors=red,white,blue versus colors=red|white|blue
 *
 * @link https://docs.magicthegathering.io/#api_v1cards_list
 *
 * @since v1.0.0
 *
 * @property ?string|null   $name          The card name. For split, double-faced and flip cards, just the name of one side of the card. Each ‘sub-card’ has its own record.
 * @property ?string|null   $layout        The card layout. Possible values: normal, split, flip, double-faced, token, plane, scheme, phenomenon, leveler, vanguard, aftermath.
 * @property int            $cmc           Converted mana cost. Always a number.
 * @property ?string[]|null $colors        The card colors. Usually derived from the casting cost, but some cards are special (like the back of dual sided cards and Ghostfire).
 * @property ?string[]|null $colorIdentity The card’s color identity, by color code. E.g., [“Red”, “Blue”] becomes [“R”, “U”]. Includes colors from the card’s rules text.
 * @property ?string|null   $type          The card type. This is the type you would see on the card if printed today. Note: The dash is a UTF8 ‘long dash’ as per the MTG rules.
 * @property ?string[]|null $supertypes    The supertypes of the card. These appear to the far left of the card type. Example: Basic, Legendary, Snow, World, Ongoing.
 * @property ?string[]|null $types         The types of the card. These appear to the left of the dash in a card type. Example: Instant, Sorcery, Artifact, Creature, Enchantment, Land, Planeswalker.
 * @property ?string[]|null $subtypes      The subtypes of the card. These appear to the right of the dash in a card type. Each word is its own subtype. Example: Trap, Arcane, Equipment, Aura, Human, Rat, Squirrel, etc.
 * @property ?string|null   $rarity        The rarity of the card. Examples: Common, Uncommon, Rare, Mythic Rare, Special, Basic Land.
 * @property ?string|null   $set           The set the card belongs to (set code).
 * @property ?string|null   $setName       The set name the card belongs to.
 * @property ?string|null   $text          The oracle text of the card. May contain mana symbols and other symbols.
 * @property ?string|null   $flavor        The flavor text of the card.
 * @property ?string|null   $artist        The artist of the card. May not match what is on the card as MTGJSON corrects many card misprints.
 * @property ?string|null   $number        The card number. Printed at the bottom-center of the card in small text. This is a string, not an integer, because some cards have letters in their numbers.
 * @property ?string|null   $power         The power of the card. Only present for creatures. This is a string, not an integer, because some cards have powers like: “1+*”.
 * @property ?string|null   $toughness     The toughness of the card. Only present for creatures. This is a string, not an integer, because some cards have toughness like: “1+*”.
 * @property ?int|null      $loyalty       The loyalty of the card. Only present for planeswalkers.
 * @property ?string|null   $language      The language the card is printed in. Use this parameter along with the name parameter when searching by foreignName.
 * @property ?string|null   $gameFormat    The game format, such as Commander, Standard, Legacy, etc. (when used, legality defaults to Legal unless supplied).
 * @property ?string|null   $legality      The legality of the card for a given format, such as Legal, Banned or Restricted.
 * @property ?int|null      $page          The page of data to request.
 * @property ?int|null      $pageSize      The amount of data to return in a single request. The default (and max) is 100.
 * @property ?string|null   $orderBy       The field to order by in the response results.
 * @property ?string|null   $random        Fetch any number of cards (controlled by pageSize) randomly.
 * @property ?string|null   $contains      Filter cards based on whether or not they have a specific field available (like imageUrl).
 * @property ?string|null   $id            A unique id for this card. It is made up by doing an SHA1 hash of setCode + cardName + cardImageName.
 * @property ?int|null      $multiverseid  The multiverseid of the card on Wizard’s Gatherer web page. Cards from sets that do not exist on Gatherer will NOT have a multiverseid.
 *
 * The fields below are also part of the response (if not null), but cannot currently be used as query parameters
 * @property-read ?array|null                                    $names
 * @property-read ?string|null                                   $manaCost
 * @property-read ?array|null                                    $variations
 * @property-read ?string|null                                   $imageUrl
 * @property-read                                                $watermark
 * @property-read ?string|null                                   $border
 * @property-read                                                $timeshifted
 * @property-read                                                $hand
 * @property-read                                                $life
 * @property-read                                                $reserved
 * @property-read ?Carbon|null                                   $releaseDate
 * @property-read                                                $starter
 * @property-read ?ExCollectionInterface<Rulings>|Rulings[]|null $rulings      Array of rulings, each containing "date" and "text".
 * @property-read                                                $foreignNames
 * @property-read                                                $printings
 * @property-read                                                $originalText
 * @property-read                                                $originalType
 * @property-read                                                $legalities
 * @property-read                                                $source
 */
trait CardAttributes
{
    /**
     * Sets the name of the card.
     *
     * @param string|null $name Name of the card.
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the name of the card.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    /**
     * Sets the layout of the card.
     *
     * @param string|null $layout Layout of the card.
     *
     * @return $this
     */
    public function setLayout(?string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Gets the layout of the card.
     *
     * @return string|null
     */
    public function getLayout(): ?string
    {
        return $this->layout ?? null;
    }

    /**
     * Sets the converted mana cost of the card.
     *
     * @param int $cmc Converted mana cost of the card.
     *
     * @return $this
     */
    public function setCmc(?int $cmc = 0): self
    {
        $this->cmc = $cmc ?? 0;

        return $this;
    }

    /**
     * Gets the converted mana cost of the card.
     *
     * @return int
     */
    public function getCmc(): int
    {
        return $this->cmc ?? 0;
    }

    /**
     * Sets the colors of the card.
     *
     * @param string[]|null $colors Colors of the card.
     *
     * @return $this
     */
    public function setColors(?array $colors): self
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Gets the colors of the card.
     *
     * @return string[]|null
     */
    public function getColors(): ?array
    {
        return $this->colors ?? null;
    }

    /**
     * Sets the color identity of the card.
     *
     * @param string[]|null $colorIdentity Color identity of the card.
     *
     * @return $this
     */
    public function setColorIdentity(?array $colorIdentity): self
    {
        $this->colorIdentity = $colorIdentity;

        return $this;
    }

    /**
     * Gets the color identity of the card.
     *
     * @return string[]|null
     */
    public function getColorIdentity(): ?array
    {
        return $this->colorIdentity ?? null;
    }

    /**
     * Sets the type of the card.
     *
     * @param string|null $type Type of the card.
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        // Replace ASCII dash with UTF8 long dash (U+2014)
        if ($type !== null) {
            $type = str_replace('-', '—', $type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Gets the type of the card.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    /**
     * Sets the supertypes of the card.
     *
     * @param string[]|null $supertypes Supertypes of the card.
     *
     * @return $this
     */
    public function setSupertypes(?array $supertypes): self
    {
        $this->supertypes = $supertypes;

        return $this;
    }

    /**
     * Gets the supertypes of the card.
     *
     * @return string[]|null
     */
    public function getSupertypes(): ?array
    {
        return $this->supertypes ?? null;
    }

    /**
     * Sets the types of the card.
     *
     * @param string[]|null $types Types of the card.
     */
    public function setTypes(?array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Gets the types of the card.
     *
     * @return string[]|null
     */
    public function getTypes(): ?array
    {
        return $this->types ?? null;
    }

    /**
     * Sets the subtypes of the card (e.g. "Human", "Wizard", "Equipment").
     *
     * @param string[]|null $subtypes Subtypes of the card.
     */
    public function setSubtypes(?array $subtypes): self
    {
        $this->subtypes = $subtypes;

        return $this;
    }

    /**
     * Gets the subtypes of the card.
     *
     * @return string[]|null
     */
    public function getSubtypes(): ?array
    {
        return $this->subtypes ?? null;
    }

    /**
     * Sets the rarity of the card (e.g. "Common", "Rare", "Mythic Rare").
     *
     * @param string|null $rarity Rarity of the card.
     */
    public function setRarity(?string $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Gets the rarity of the card.
     *
     * @return string|null
     */
    public function getRarity(): ?string
    {
        return $this->rarity ?? null;
    }

    /**
     * Sets the code of the set the card belongs to.
     *
     * @param string|null $set Set code.
     */
    public function setSet(?string $set): self
    {
        $this->set = $set;

        return $this;
    }

    /**
     * Gets the code of the set the card belongs to.
     *
     * @return string|null
     */
    public function getSet(): ?string
    {
        return $this->set ?? null;
    }

    /**
     * Sets the full name of the set the card belongs to.
     *
     * @param string|null $setName Set name.
     */
    public function setSetName(?string $setName): self
    {
        $this->setName = $setName;

        return $this;
    }

    /**
     * Gets the full name of the set the card belongs to.
     *
     * @return string|null
     */
    public function getSetName(): ?string
    {
        return $this->setName ?? null;
    }

    /**
     * Sets the card's oracle rules text.
     *
     * @param string|null $text Rules text.
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Gets the card's oracle rules text.
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text ?? null;
    }

    /**
     * Sets the card's flavor text.
     *
     * @param string|null $flavor Flavor text.
     */
    public function setFlavor(?string $flavor): self
    {
        $this->flavor = $flavor;

        return $this;
    }

    /**
     * Gets the card's flavor text.
     *
     * @return string|null
     */
    public function getFlavor(): ?string
    {
        return $this->flavor ?? null;
    }

    /**
     * Sets the name of the artist who illustrated the card.
     *
     * @param string|null $artist Artist name.
     */
    public function setArtist(?string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Gets the name of the artist who illustrated the card.
     *
     * @return string|null
     */
    public function getArtist(): ?string
    {
        return $this->artist ?? null;
    }

    /**
     * Sets the card's collector number within its set.
     *
     * @param string|null $number Collector number.
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Gets the card's collector number within its set.
     *
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number ?? null;
    }

    /**
     * Sets the creature's power. A string because it may be non-numeric (e.g. "*").
     *
     * @param string|null $power Power.
     */
    public function setPower(?string $power): self
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Gets the creature's power.
     *
     * @return string|null
     */
    public function getPower(): ?string
    {
        return $this->power ?? null;
    }

    /**
     * Sets the creature's toughness. A string because it may be non-numeric (e.g. "*").
     *
     * @param string|null $toughness Toughness.
     */
    public function setToughness(?string $toughness): self
    {
        $this->toughness = $toughness;

        return $this;
    }

    /**
     * Gets the creature's toughness.
     *
     * @return string|null
     */
    public function getToughness(): ?string
    {
        return $this->toughness ?? null;
    }

    /**
     * Sets the planeswalker's starting loyalty.
     *
     * @param int|null $loyalty Starting loyalty.
     */
    public function setLoyalty(?int $loyalty): self
    {
        $this->loyalty = $loyalty;

        return $this;
    }

    /**
     * Gets the planeswalker's starting loyalty.
     *
     * @return int|null
     */
    public function getLoyalty(): ?int
    {
        return $this->loyalty ?? null;
    }

    /**
     * Sets the language to match foreign card names against when querying.
     *
     * @param string|null $language Language name.
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the language used when querying foreign card names.
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language ?? null;
    }

    /**
     * Sets the game format to filter legality against (e.g. "Standard", "Modern").
     *
     * @param string|null $gameFormat Game format name.
     */
    public function setGameFormat(?string $gameFormat): self
    {
        $this->gameFormat = $gameFormat;

        return $this;
    }

    /**
     * Gets the game format used to filter legality.
     *
     * @return string|null
     */
    public function getGameFormat(): ?string
    {
        return $this->gameFormat ?? null;
    }

    /**
     * Sets the legality status to filter by ("Legal", "Banned" or "Restricted").
     *
     * @param string|null $legality Legality status.
     */
    public function setLegality(?string $legality): self
    {
        $this->legality = $legality;

        return $this;
    }

    /**
     * Gets the legality status used to filter results.
     *
     * @return string|null
     */
    public function getLegality(): ?string
    {
        return $this->legality ?? null;
    }

    /**
     * Sets the results page number to request (1-based pagination).
     *
     * @param int|null $page Page number.
     */
    public function setPage(?int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Gets the results page number.
     *
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page ?? null;
    }

    /**
     * Sets the number of results per page.
     *
     * @param int|null $pageSize Page size, 1-100.
     *
     * @throws \InvalidArgumentException When outside the 1-100 range.
     */
    public function setPageSize(?int $pageSize): self
    {
        if ($pageSize !== null && ($pageSize > 100 || $pageSize < 1)) {
            throw new \InvalidArgumentException('Page size must be between 1 and 100.');
        }

        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * Gets the number of results per page.
     *
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize ?? null;
    }

    /**
     * Sets the field to order results by.
     *
     * @param string|null $orderBy Field name.
     */
    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Gets the field results are ordered by.
     *
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy ?? null;
    }

    /**
     * Requests a single random card matching the other filters. Only a truthy
     * value has an effect; `false`/`null` leaves the flag unset.
     *
     * @param bool|null $random Whether to return a random card.
     */
    public function setRandom(?bool $random): self
    {
        if ($random) {
            $this->random = 'true';
        }

        return $this;
    }

    /**
     * Gets whether a random card was requested.
     *
     * @return bool|null
     */
    public function getRandom(): ?bool
    {
        return $this->random ?? null;
    }

    /**
     * Restricts results to cards that have the named field(s) present.
     *
     * @param string|null $contains Comma-separated field names.
     */
    public function setContains(?string $contains): self
    {
        $this->contains = $contains;

        return $this;
    }

    /**
     * Gets the "contains" field filter.
     *
     * @return string|null
     */
    public function getContains(): ?string
    {
        return $this->contains ?? null;
    }

    /**
     * Sets the card's unique id (a hash of set code, name and collector number).
     *
     * @param string|null $id Card id.
     */
    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the card's unique id.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id ?? null;
    }

    /**
     * Sets the card's Gatherer multiverse id.
     *
     * @param int|null $multiverseid Multiverse id.
     */
    public function setMultiverseid(?int $multiverseid): self
    {
        $this->multiverseid = $multiverseid;

        return $this;
    }

    /**
     * Gets the card's Gatherer multiverse id.
     *
     * @return int|null
     */
    public function getMultiverseid(): ?int
    {
        return $this->multiverseid ?? null;
    }
}
