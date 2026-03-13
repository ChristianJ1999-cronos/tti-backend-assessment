<?php

namespace Database\Factories;

use App\Models\Instrument;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Instrument>
 */
class InstrumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->randomElement([
                'Daily health assessment',
                'Weekly health assessment',
                'Monthly health assessment',
                'Yearly health assessment'
            ]),
            'description' => match($title) {
                'Daily health assessment' => 'Track how your symptoms are doing every day',
                'Weekly health assessment' => 'Track how your symptoms are doing every week',
                'Monthly health assessment' => 'Monthly summary of symptoms and quality of life',
                'Yearly health assessment' => 'Yearly review of overall health'
            },
        ];
    }

    public function configure(): static{
        return $this->afterCreating(function (Instrument $instrument){
            Question::create(['instrument_id' => $instrument->id, 'prompt' => 'How are your energy levels today?', 'response_type' => 'scale_1_5', 'order' => 1]);
            Question::create(['instrument_id' => $instrument->id, 'prompt' => 'Are you able to eat?', 'response_type' => 'yes_no', 'order' => 2]);
            Question::create(['instrument_id' => $instrument->id, 'prompt' => 'How is your pain today?', 'response_type' => 'free_text', 'order' => 3]);
        });
    }

}