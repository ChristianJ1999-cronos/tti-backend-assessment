<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'instrument_id' => $this->instrument_id,
            'prompt' => $this->prompt,
            'response_type' => $this->response_type,
            'order' => $this->order,
            'created_at' => $this->created_at,
        ];
    }
}
