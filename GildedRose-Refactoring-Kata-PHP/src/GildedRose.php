<?php

declare(strict_types=1);

namespace GildedRose;

use GildedRose\Exceptions\NegativeItemQualityException;
use GildedRose\Exceptions\ExcessiveItemQualityException;

final class GildedRose
{
    /**
     * @var Item[]
     */
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @throws NegativeItemQualityException
     * @throws ExcessiveItemQualityException
     */
    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $this->verifyItemQuality($item);

            switch ($item->name) {
                case 'Sulfuras, Hand of Ragnaros':
                    return;
                case 'Aged Brie':
                    if ($item->quality < 50) {
                        ++$item->quality;
                    }
                    break;
                case 'Backstage passes to a TAFKAL80ETC concert':
                    switch ($item->sellIn) {
                        case $item->sellIn < 0:
                            $item->quality = 0;
                            break;
                        case $item->sellIn <= 5:
                            $item->quality += 3;
                            break;
                        case $item->sellIn <= 10:
                            $item->quality += 2;
                            break;
                        case $item->sellIn > 10:
                            ++$item->quality;
                            break;
                    }
                    break;
                default:
                    if ($item->sellIn < 0) {
                        $item->quality -= 2;
                    } else {
                        --$item->quality;
                    }
            }
            --$item->sellIn;
        }
    }

    /**
     * @throws ExcessiveItemQualityException
     * @throws NegativeItemQualityException
     */
    public function verifyItemQuality(Item $item): void
    {
        if ($item->quality < 0) {
            throw new NegativeItemQualityException();
        }
        if ($item->name !== 'Sulfuras, Hand of Ragnaros' && $item->quality > 50) {
            throw new ExcessiveItemQualityException();
        }
    }
}
