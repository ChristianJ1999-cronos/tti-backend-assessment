<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth->format('Y-m-d'),
            'mrn' => $this->mrn,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];

    }
}
