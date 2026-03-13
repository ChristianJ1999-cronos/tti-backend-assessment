<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AnswerResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return[
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'instrument_id' => $this->instrument_id,
            'submitted_at' => $this->submitted_at,
            'created_at' => $this->created_at,

            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
