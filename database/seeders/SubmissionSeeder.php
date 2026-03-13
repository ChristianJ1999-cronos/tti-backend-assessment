<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Instrument;
use App\Models\Patient;
use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $instrument = Instrument::first();

        foreach($patients as $patient){
            $submission = Submission::create([
                'patient_id' => $patient->id,
                'instrument_id' => $instrument->id,
                'submitted_at' => now()
            ]);


            foreach($instrument->questions as $question){
                $answer = match($question->response_type){
                    'scale_1_5' => rand(1,5),
                    'yes_no' => (bool) rand(0,1),
                    'free_text' => match($instrument->title){
                        'Daily health assessment' => 'My pain has gotten better today.',
                        'Weekly health assessment' => 'My pain has not gotten worse this week.',
                        'Monthly health assessment' => 'My pain has gotten slightly better this month.',
                        'Yearly health assessment' => 'My pain has gotten better throughout the year.'
                    }
                };

                Answer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $question->id,
                    'value' => $answer
                ]);
            }
        }
    }
}