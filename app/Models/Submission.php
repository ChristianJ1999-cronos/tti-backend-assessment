<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'instrument_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime'
    ];

    public function patient(): BelongsTo{
        return $this->belongsTo(Patient::class);
    }

    public function instrument(): BelongsTo{
        return $this->belongsTo(Instrument::class);
    }

    public function answers(): HasMany{
        return $this->hasMany(Answer::class);
    }

}
 