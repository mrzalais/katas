<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use GildedRose\Item;

trait ItemFactory
{
    public function createItem(string $name = null, int $sellIn = null, int $quality = null): Item
    {
        $faker = Factory::create();

        return new Item(
            $name ?? $faker->name,
            $sellIn ?? $faker->numberBetween(1, 30),
            $quality ?? $faker->numberBetween(0, 50)
        );
    }
}
