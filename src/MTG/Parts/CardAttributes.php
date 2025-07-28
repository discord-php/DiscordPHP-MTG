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

    public function setSubtypes(?array $subtypes): self
    {
        $this->subtypes = $subtypes;

        return $this;
    }

    public function getSubtypes(): ?array
    {
        return $this->subtypes ?? null;
    }

    public function setRarity(?string $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity ?? null;
    }

    public function setSet(?string $set): self
    {
        $this->set = $set;

        return $this;
    }

    public function getSet(): ?string
    {
        return $this->set ?? null;
    }

    public function setSetName(?string $setName): self
    {
        $this->setName = $setName;

        return $this;
    }

    public function getSetName(): ?string
    {
        return $this->setName ?? null;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text ?? null;
    }

    public function setFlavor(?string $flavor): self
    {
        $this->flavor = $flavor;

        return $this;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor ?? null;
    }

    public function setArtist(?string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist ?? null;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number ?? null;
    }

    public function setPower(?string $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getPower(): ?string
    {
        return $this->power ?? null;
    }

    public function setToughness(?string $toughness): self
    {
        $this->toughness = $toughness;

        return $this;
    }

    public function getToughness(): ?string
    {
        return $this->toughness ?? null;
    }

    public function setLoyalty(?int $loyalty): self
    {
        $this->loyalty = $loyalty;

        return $this;
    }

    public function getLoyalty(): ?int
    {
        return $this->loyalty ?? null;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language ?? null;
    }

    public function setGameFormat(?string $gameFormat): self
    {
        $this->gameFormat = $gameFormat;

        return $this;
    }

    public function getGameFormat(): ?string
    {
        return $this->gameFormat ?? null;
    }

    public function setLegality(?string $legality): self
    {
        $this->legality = $legality;

        return $this;
    }

    public function getLegality(): ?string
    {
        return $this->legality ?? null;
    }

    public function setPage(?int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page ?? null;
    }

    public function setPageSize(?int $pageSize): self
    {
        if ($pageSize !== null && ($pageSize > 100 || $pageSize < 1)) {
            throw new \InvalidArgumentException('Page size must be between 1 and 100.');
        }

        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize ?? null;
    }

    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy ?? null;
    }

    public function setRandom(?bool $random): self
    {
        if ($random) {
            $this->random = 'true';
        }

        return $this;
    }

    public function getRandom(): ?bool
    {
        return $this->random ?? null;
    }

    public function setContains(?string $contains): self
    {
        $this->contains = $contains;

        return $this;
    }

    public function getContains(): ?string
    {
        return $this->contains ?? null;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id ?? null;
    }

    public function setMultiverseid(?int $multiverseid): self
    {
        $this->multiverseid = $multiverseid;

        return $this;
    }

    public function getMultiverseid(): ?int
    {
        return $this->multiverseid ?? null;
    }
}
