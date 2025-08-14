<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id' => User::where('role', 'learner')->inRandomOrder()->first()->id,
            'course_id' => Course::inRandomOrder()->first()->id,
            'rating' => $rating = fake()->numberBetween(1, 5),
           'review' => fake()->paragraph(),
            'status' => $rating < 3 ? 'Reported' : 'Normal',
        ];
    }
}
