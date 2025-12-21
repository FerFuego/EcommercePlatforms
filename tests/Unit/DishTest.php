<?php

namespace Tests\Unit;

use App\Models\Dish;
use App\Models\Cook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_cook()
    {
        $cook = Cook::factory()->create();
        $dish = Dish::factory()->create(['cook_id' => $cook->id]);

        $this->assertInstanceOf(Cook::class, $dish->cook);
        $this->assertEquals($cook->id, $dish->cook->id);
    }

    /** @test */
    public function it_can_decrement_stock()
    {
        $dish = Dish::factory()->create(['available_stock' => 10]);

        $result = $dish->decrementStock(3);

        $this->assertTrue($result);
        $this->assertEquals(7, $dish->fresh()->available_stock);
    }

    /** @test */
    public function it_cannot_decrement_stock_below_zero()
    {
        $dish = Dish::factory()->create(['available_stock' => 2]);

        $result = $dish->decrementStock(5);

        $this->assertFalse($result);
        $this->assertEquals(2, $dish->fresh()->available_stock);
    }

    /** @test */
    public function it_can_increment_stock()
    {
        $dish = Dish::factory()->create(['available_stock' => 5]);

        $dish->incrementStock(3);

        $this->assertEquals(8, $dish->fresh()->available_stock);
    }

    /** @test */
    public function it_scopes_available_dishes()
    {
        Dish::factory()->create([
            'is_active' => true,
            'available_stock' => 10,
        ]);

        Dish::factory()->create([
            'is_active' => false,
            'available_stock' => 10,
        ]);

        Dish::factory()->create([
            'is_active' => true,
            'available_stock' => 0,
        ]);

        $available = Dish::available()->get();

        $this->assertCount(1, $available);
    }

    /** @test */
    public function it_scopes_active_dishes()
    {
        Dish::factory()->create(['is_active' => true]);
        Dish::factory()->create(['is_active' => true]);
        Dish::factory()->create(['is_active' => false]);

        $active = Dish::active()->get();

        $this->assertCount(2, $active);
    }

    /** @test */
    public function it_scopes_dishes_by_diet()
    {
        $veganDish = Dish::factory()->create([
            'diet_tags' => ['vegan', 'gluten-free'],
        ]);

        $vegetarianDish = Dish::factory()->create([
            'diet_tags' => ['vegetarian'],
        ]);

        $regularDish = Dish::factory()->create([
            'diet_tags' => [],
        ]);

        $veganOnly = Dish::byDiet(['vegan'])->get();
        $this->assertCount(1, $veganOnly);
        $this->assertTrue($veganOnly->contains($veganDish));

        $vegetarianOrVegan = Dish::byDiet(['vegan', 'vegetarian'])->get();
        $this->assertCount(2, $vegetarianOrVegan);
    }

    /** @test */
    public function it_knows_if_available_on_a_day()
    {
        $dish = Dish::factory()->create([
            'available_days' => [1, 3, 5], // Monday, Wednesday, Friday
        ]);

        $this->assertTrue($dish->isAvailableOnDay(1));
        $this->assertFalse($dish->isAvailableOnDay(2));
        $this->assertTrue($dish->isAvailableOnDay(5));
    }

    /** @test */
    public function it_casts_diet_tags_to_array()
    {
        $dish = Dish::factory()->create([
            'diet_tags' => ['vegan', 'gluten-free', 'low-carb'],
        ]);

        $this->assertIsArray($dish->diet_tags);
        $this->assertCount(3, $dish->diet_tags);
        $this->assertContains('vegan', $dish->diet_tags);
    }

    /** @test */
    public function it_casts_available_days_to_array()
    {
        $dish = Dish::factory()->create([
            'available_days' => [1, 2, 3, 4, 5],
        ]);

        $this->assertIsArray($dish->available_days);
        $this->assertCount(5, $dish->available_days);
    }

    /** @test */
    public function it_knows_if_has_stock()
    {
        $dishWithStock = Dish::factory()->create(['available_stock' => 5]);
        $dishWithoutStock = Dish::factory()->create(['available_stock' => 0]);

        $this->assertTrue($dishWithStock->hasStock());
        $this->assertFalse($dishWithoutStock->hasStock());
    }

    /** @test */
    public function price_is_cast_to_float()
    {
        $dish = Dish::factory()->create(['price' => '1299.50']);

        $this->assertIsFloat($dish->price);
        $this->assertEquals(1299.50, $dish->price);
    }
}
