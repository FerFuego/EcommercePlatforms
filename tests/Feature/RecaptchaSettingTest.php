<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Rules\Recaptcha;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecaptchaSettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_nullable_rules_even_when_enabled_due_to_testing_env()
    {
        Setting::updateOrCreate(['key' => 'recaptcha_enabled'], ['value' => '1']);
        
        $rules = Recaptcha::rules();
        
        // In tests, it should always be nullable to not break automated tests
        $this->assertEquals(['nullable'], $rules);
    }

    /** @test */
    public function it_returns_nullable_rule_when_disabled()
    {
        Setting::updateOrCreate(['key' => 'recaptcha_enabled'], ['value' => '0']);
        
        $rules = Recaptcha::rules();
        
        $this->assertEquals(['nullable'], $rules);
    }
}
