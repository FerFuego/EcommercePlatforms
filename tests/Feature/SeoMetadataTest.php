<?php

namespace Tests\Feature;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_seo_canonical_open_graph_and_json_ld()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="http://localhost">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('"@type": "Organization"', false);
        $response->assertSee('"@type": "WebSite"', false);
    }

    public function test_catalog_has_canonical_and_breadcrumb_schema()
    {
        $response = $this->get('/marketplace/catalog');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="http://localhost/marketplace/catalog">', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_sitemap_xml_returns_valid_xml_structure()
    {
        $user = User::factory()->create(['role' => 'cook']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'active' => true,
        ]);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'is_active' => true,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
        $response->assertSee(route('marketplace.cook.profile', $cook->id), false);
        $response->assertSee(route('marketplace.dish.detail', $dish->id), false);
    }

    public function test_cook_profile_and_dish_detail_contain_schema_org_json_ld()
    {
        $user = User::factory()->create(['name' => 'Chef Maria', 'role' => 'cook']);
        $cook = Cook::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'active' => true,
            'rating_avg' => 4.8,
            'rating_count' => 12,
        ]);
        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Empanadas Caseras Salteñas',
            'price' => 1200,
            'is_active' => true,
            'available_stock' => 10,
            'available_days' => [],
        ]);

        $cookResponse = $this->get('/marketplace/cook/' . $cook->id);
        $cookResponse->assertStatus(200);
        $cookResponse->assertSee('"@type": "Restaurant"', false);
        $cookResponse->assertSee('"name": "Chef Maria"', false);

        $dishResponse = $this->get('/marketplace/dish/' . $dish->id);
        $dishResponse->assertStatus(200);
        $dishResponse->assertSee('"@type": ["Product", "MenuItem"]', false);
        $dishResponse->assertSee('"name": "Empanadas Caseras Salteñas"', false);
        $dishResponse->assertSee('"price": "1200"', false);
    }
}
