<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prompt' => $prompt = $this->faker->randomElement([ 
                'How are your energy levels today?', 
                'Are you able to eat?', 
                'How is your pain today?'
            ]),
            'response_type' => match($prompt){
                'How are your energy levels today?' => 'scale_1_5', 
                'Are you able to eat?' => 'yes_no', 
                'How is your pain today?' => 'free_text'
            },
            'order' => match($prompt){
                'How are your energy levels today?' => 3, 
                'Are you able to eat?' => 2, 
                'How is your pain today?' => 1
            },
        ];
    }
}
 