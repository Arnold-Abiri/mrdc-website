<?php

namespace Database\Factories;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Enquiry> */
class EnquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::uuid(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'category' => 'general',
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => 'new',
            'submitted_at' => now(),
        ];
    }
}
