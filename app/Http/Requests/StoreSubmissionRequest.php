<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AllQuestionsAnswered;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'instrument_id' => ['required', 'exists:instruments,id'],

            'answers' => ['required', 'array', 'min:1', new AllQuestionsAnswered($this->input('instrument_id'))],
            'answers.*.question_id' => ['required', 'exists:questions,id'],
        ];

        foreach($this->input('answers', []) as $index => $answer){
            $question = Question::find($answer['question_id'] ?? null);

            if(!$question){
                $rules["answers.$index.value"] = ['required'];
                continue;
            }

            $rules["answers.$index.value"] = match($question->response_type){
                'scale_1_5' => ['required', 'integer', 'min:1', 'max:5'],
                'yes_no' => ['required', 'boolean'],
                'free_text' => ['present', 'string', 'nullable'],
            };

        }

        return $rules;
    }
}
