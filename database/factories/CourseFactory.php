<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'thumbnail' => $this->faker->imageUrl(640, 480, 'education'),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'active' => true,
            'category_id' => Category::factory(),
        ];
    }

    /**
     * Indicate that the course has lessons.
     */
    public function hasLessons($count = 1)
    {
        return $this->has(\App\Models\Lesson::factory()->count($count));
    }
}
