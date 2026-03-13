<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Answer;
use App\Models\Instrument;
use App\Models\Patient;
use App\Models\Submission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function store(StorePatientRequest $request): JsonResponse{
        $data = $request->validated();
        try{
            $patient = Patient::create(
                $data
            ); 
        }catch(UniqueConstraintViolationException $e){ //second safety net besides the form request making it unique
            return response()->json([
                'message' => 'Patient failed in creating'
            ], 422);
        }

        return response()->json(new PatientResource($patient), 201);
    }

    public function storeSubmission(StoreSubmissionRequest $request, $patientId): JsonResponse{
        $data = $request->validated();

        $submission = DB::transaction(function() use ($data, $patientId) {
            $submission = Submission::create([
                'patient_id' => $patientId,
                'instrument_id' => $data['instrument_id'],
                'submitted_at' => now()
            ]);

            foreach($data['answers'] as $answer){
                $submission->answers()->create([
                    'question_id' => $answer['question_id'],
                    'value' => $answer['value']
                ]);
            }
            return $submission;
        });
        return response()->json(new SubmissionResource($submission->load('answers')), 201);
    }

    public function index($patientId): JsonResponse{
        $submissions = Submission::where('patient_id', $patientId)->orderByDesc('submitted_at')->paginate(10);
        return response()->json(SubmissionResource::collection($submissions));
    }

    public function show($patientId, $submissionId): JsonResponse{
        $submission = Submission::where('id', $submissionId)->where('patient_id', $patientId)->firstOrFail(); //fetch single record or return a 404
        return response()->json(new SubmissionResource($submission->load('answers')));
    }

    public function summary($patientId): JsonResponse{
        $instrumentId = request('instrument_id');
        $submission = Submission::where('patient_id', $patientId)->where('instrument_id', $instrumentId);
        $submissionCount = (clone $submission)->count();
        $submissionDateRange = (clone $submission)->selectRaw('min(submitted_at) as earliest, max(submitted_at) as latest')->first();
        $submissionIds = (clone $submission)->pluck('id');

        $instrument = Instrument::with('questions')->findOrFail($instrumentId);

        $questions = $instrument->questions->map(function($question) use ($submissionIds){
            $answers = Answer::where('question_id', $question->id)->whereIn('submission_id', $submissionIds)->pluck('value');

            $aggregation = match($question->response_type){
                'scale_1_5' => ['average' => round($answers->avg(), 2)],
                'yes_no' => ['yes_percentage' => round($answers->filter(fn($v) => $v == '1' || $v == 'true')->count() / max($answers->count(), 1) * 100, 2)],
                'free_text' => ['non_empty_count' => $answers->filter(fn($v) => trim($v) !== '')->count()]
            };

            return array_merge([
                'prompt' => $question->prompt,
                'response_type' => $question->response_type,
            ], $aggregation);
        });


        return response()->json([
            'data' => [
                'total_submissions' => $submissionCount,
                'date_range' => [
                    'earliest' => $submissionDateRange->earliest ? Carbon::parse($submissionDateRange->earliest)->toDateString() : null,
                    'latest' => $submissionDateRange->latest ? Carbon::parse($submissionDateRange->latest)->toDateString() : null,
                ],
                'questions' => $questions
            ]
        ]);
    }

}
 