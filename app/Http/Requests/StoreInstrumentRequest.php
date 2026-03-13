<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstrumentRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],

            'questions' => ['required', 'array', 'min:1'],

            'questions.*.prompt' => ['required', 'string'],
            'questions.*.response_type' => ['required', 'string', Rule::in(['scale_1_5', 'yes_no', 'free_text'])],
            'questions.*.order' => ['required', 'integer'],
        ];
    }
}
