<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_name' => $this->faker->name(),
            'client_company' => $this->faker->company(),
            'client_avatar' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'is_visible' => true,
            'order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Testimonial $testimonial) {
            $locales = ['en', 'fr', 'ar'];
            foreach ($locales as $locale) {
                $testimonial->translations()->create([
                    'locale' => $locale,
                    'content' => "[$locale] " . $this->faker->paragraph(),
                    'client_title' => "[$locale] " . $this->faker->jobTitle(),
                ]);
            }
        });
    }
}
