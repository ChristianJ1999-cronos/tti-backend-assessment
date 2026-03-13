<?php

use App\Models\Instrument;
use App\Models\Patient;
use App\Models\Submission;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('Create a patient that passes.', function() {
    $response = $this->postJson('/api/patients', [
        'name' => 'Zeus Olympian',
        'date_of_birth' => '1900-06-10',
        'mrn' => 'MRN-001'
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'id', 'name', 'date_of_birth', 'mrn', 'created_at'
    ]);
});


test('create an instrument with questions that passes', function(){
    $response = $this->postJson('/api/instruments', [
        'title' => 'Pain Assessment',
        'description' => 'Daily pain tracking',
        'questions' => [
            ['prompt' => 'Rate your pain', 'response_type' => 'scale_1_5', 'order' => 1],
            ['prompt' => 'Are you experiencing any nasuea?',  'response_type' => 'yes_no', 'order' => 2],
            ['prompt' => 'How has your appetite been?', 'response_type' => 'free_text', 'order' => 3],
        ]
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'id', 'title', 'description', 'questions'
    ]);
});

test('submit a completed instrument for a patient', function() {
        $patient = Patient::factory()->create();
        $instrument = Instrument::factory()->create();

        $questions = $instrument->questions;

        $answers = $questions->map(function($question) {
        $value = match($question->response_type) {
                'scale_1_5' => 3,
                'yes_no' => true,
                'free_text' => 'Feeling okay',
            };
            return ['question_id' => $question->id, 'value' => $value];
        })->toArray();

        $response = $this->postJson("/api/patients/{$patient->id}/submissions", [
            'instrument_id' => $instrument->id,
            'answers' => $answers
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'patient_id', 'instrument_id', 'submitted_at', 'answers']);
});

test('list all submissions for a patient', function() {
        $patient = Patient::factory()->create();
        $instrument = Instrument::factory()->create();

        Submission::factory()->count(3)->create([
            'patient_id' => $patient->id,
            'instrument_id' => $instrument->id,
        ]);

        $response = $this->getJson("/api/patients/{$patient->id}/submissions");
        // dd($response->json());
        $response->assertStatus(200);
        $response->assertJsonCount(3);
});

test('grabbing a single submission with answers.', function() {
        $patient = Patient::factory()->create();
        $instrument = Instrument::factory()->create();

        $submission = Submission::factory()->create([
            'patient_id' => $patient->id,
            'instrument_id' => $instrument->id,
        ]);

        $response = $this->getJson("/api/patients/{$patient->id}/submissions/{$submission->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'patient_id', 'instrument_id', 'submitted_at']);
});

test('get added summary for all patient information.', function() {
        $patient = Patient::factory()->create();
        $instrument = Instrument::factory()->create();
        $questions = $instrument->questions;
        $submission = Submission::factory()->create([
            'patient_id' => $patient->id,
            'instrument_id' => $instrument->id,
        ]);

        $questions->each(function($question) use ($submission) {
            $value = match($question->response_type) {
                'scale_1_5' => 3,
                'yes_no' => true,
                'free_text' => 'Feeling okay',
            };
            $submission->answers()->create([
                'question_id' => $question->id,
                'value' => $value
            ]);
        });

        $response = $this->getJson("/api/patients/{$patient->id}/summary?instrument_id={$instrument->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['total_submissions', 'date_range', 'questions']]);
});

