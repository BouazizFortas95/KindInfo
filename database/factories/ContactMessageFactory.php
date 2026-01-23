<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraphs(3, true),
            'is_read' => $this->faker->boolean(30),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
