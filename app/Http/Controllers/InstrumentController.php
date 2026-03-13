<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstrumentRequest;
use App\Http\Resources\InstrumentResource;
use App\Models\Instrument;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InstrumentController extends Controller
{
    
    public function store(StoreInstrumentRequest $request): JsonResponse{
        $data = $request->validated();
        try{
            $instrument = Instrument::create(
                $data
            );

            $instrument->questions()->createMany($data['questions']);
        }catch(UniqueConstraintViolationException $e){
            return response()->json([
                'message' => 'Instrument creation failed.'
            ], 422);
        }
        return response()->json(new InstrumentResource($instrument->load('questions')), 201);
    }
     
}
 