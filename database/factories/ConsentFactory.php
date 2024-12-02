<?php

namespace Database\Factories;

use App\Models\Consent;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentFactory extends Factory
{
    protected $model = Consent::class;

    public function definition()
    {
        return [
            'company_id' => Company::factory()->create()->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->e164PhoneNumber,
            'language' => 'en',
            'verification_code' => str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'pending',
        ];
    }

    public function verified()
    {
        return $this->state(fn () => [
            'status' => 'verified',
            'verification_code' => '1234',
        ]);
    }

    public function consented()
    {
        return $this->state(fn () => [
            'status' => 'consented',
        ]);
    }

    public function declined()
    {
        return $this->state(fn () => [
            'status' => 'declined',
        ]);
    }
}
