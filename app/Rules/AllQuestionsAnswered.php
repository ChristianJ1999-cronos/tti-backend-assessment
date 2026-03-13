<?php

namespace App\Rules;

use App\Models\Question;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class AllQuestionsAnswered implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function __construct(private int $instrumentId){}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $question_ids = collect($value)->pluck('question_id'); //getting all question_id from answers array
        $compare_ids = Question::where('instrument_id', $this->instrumentId)->pluck('id');
        $ifDiff = $compare_ids->diff($question_ids);

        if($ifDiff->isNotEmpty()){
            $fail('All questions must be answered.');
        }

    }
}
