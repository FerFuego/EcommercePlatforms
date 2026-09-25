<?php

namespace Tests\Unit;

use App\Rules\PhoneNumber;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    private function validate(mixed $phone): bool
    {
        $validator = Validator::make(
            ['phone' => $phone],
            ['phone' => ['required', new PhoneNumber]]
        );

        return $validator->passes();
    }

    /** @test */
    public function it_accepts_valid_argentine_phone_numbers()
    {
        $this->assertTrue($this->validate('+54 9 11 1234-5678'));
        $this->assertTrue($this->validate('5491112345678'));
        $this->assertTrue($this->validate('+54 3537 123456'));
        $this->assertTrue($this->validate('3537123456'));
        $this->assertTrue($this->validate('1123456789'));
        $this->assertTrue($this->validate('011 15 1234-5678'));
        $this->assertTrue($this->validate('011 1234-5678'));
    }

    /** @test */
    public function it_accepts_valid_international_numbers()
    {
        $this->assertTrue($this->validate('+1234567890'));
        $this->assertTrue($this->validate('+34 612 345 678'));
    }

    /** @test */
    public function it_rejects_invalid_phone_numbers()
    {
        // Letters and symbols
        $this->assertFalse($this->validate('hola12345678'));
        $this->assertFalse($this->validate('asdfghjklz'));
        $this->assertFalse($this->validate('undefined'));

        // Too short
        $this->assertFalse($this->validate('12345'));
        $this->assertFalse($this->validate('+54 9 11 12'));

        // All identical digits
        $this->assertFalse($this->validate('0000000000'));
        $this->assertFalse($this->validate('1111111111'));
        $this->assertFalse($this->validate('+54 9 11 1111-1111'));

        // Empty
        $this->assertFalse($this->validate(''));
        $this->assertFalse($this->validate(null));
    }
}
