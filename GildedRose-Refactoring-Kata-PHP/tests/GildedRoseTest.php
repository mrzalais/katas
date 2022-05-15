<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use PHPUnit\Framework\TestCase;
use GildedRose\Exceptions\NegativeItemQualityException;
use GildedRose\Exceptions\ExcessiveItemQualityException;

class GildedRoseTest extends TestCase
{
    use ItemFactory;

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testSellInValueDropsByOneWhenUpdateIsCalled(): void
    {
        $items[] = $this->createItem(null, 1);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->sellIn);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testQualityDegradesWhenUpdateIsCalled(): void
    {
        $items[] = $this->createItem(null, 1, 50);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(49, $items[0]->quality);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testQualityDegradesTwiceAsFastWhenSellByDateHasPassed(): void
    {
        $items[] = $this->createItem(null, -1, 50);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(48, $items[0]->quality);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testQualityCanNotBeNegative(): void
    {
        $items[] = $this->createItem(null, null, -1);
        $gildedRose = new GildedRose($items);
        $this->expectException(NegativeItemQualityException::class);
        $gildedRose->updateQuality();
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testAgedBrieIncreasesInQualityAsTimePasses(): void
    {
        $items[] = $this->createItem('Aged Brie', 1, 49);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testQualityOfAnItemDoesNotGoAboveFifty(): void
    {
        $items[] = $this->createItem('Aged Brie', 1, 50);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     */
    public function testSulfurasDoesNotDegradeInQuality(): void
    {
        $items[] = $this->createItem('Sulfuras, Hand of Ragnaros', null, 80);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(80, $items[0]->quality);
    }

    /**
     * @throws NegativeItemQualityException|ExcessiveItemQualityException
     * @dataProvider backstagePassProvider
     */
    public function testBackstagePassesIncreaseInQualityAsSellInApproaches(
        int $initialQuality,
        int $expectedQuality,
        int $sellIn,
    ): void
    {
        $items[] = $this->createItem('Backstage passes to a TAFKAL80ETC concert', $sellIn, $initialQuality);
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame($expectedQuality, $items[0]->quality);
    }

    public function backstagePassProvider(): array
    {
        return [
            [
                'initialQuality' => 49,
                'expectedQuality' => 50,
                'sellIn' => 100,
            ],
            [
                'initialQuality' => 49,
                'expectedQuality' => 50,
                'sellIn' => 11,
            ],
            [
                'initialQuality' => 48,
                'expectedQuality' => 50,
                'sellIn' => 10,
            ],
            [
                'initialQuality' => 47,
                'expectedQuality' => 50,
                'sellIn' => 5,
            ],
            [
                'initialQuality' => 47,
                'expectedQuality' => 50,
                'sellIn' => 4,
            ],
            [
                'initialQuality' => 47,
                'expectedQuality' => 50,
                'sellIn' => 4,
            ],
            [
                'initialQuality' => 50,
                'expectedQuality' => 0,
                'sellIn' => -1,
            ],
        ];
    }
}
