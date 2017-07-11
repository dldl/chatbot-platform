<?php

namespace dLdL\ChatbotPlatform\Helper;

/**
 * A collection of items.
 */
class ItemsCollection
{
    private $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function has(...$items): bool
    {
        foreach ($items as $item) {
            if (!in_array($item, $this->items)) {
                return false;
            }
        }

        return true;
    }

    public function hasAny(): bool
    {
        return count($this->items) > 0;
    }

    public function add($item): void
    {
        $this->items[$item] = $item;
    }

    public function remove($item): void
    {
        unset($this->items[$item]);
    }

    public function all()
    {
        return $this->items;
    }

    public function __toString()
    {
        $nbItems = count($this->items);
        $string = '';
        for ($i = 0; $i < $nbItems; ++$i) {
            $string = (string) $this->items[$i];

            if ($i !== $nbItems - 1) {
                $string .= ', ';
            }
        }

        return $string;
    }
}
