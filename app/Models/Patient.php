<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'date_of_birth',
        'mrn'
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d'
    ];

    public function submissions(): HasMany{
        return $this->hasMany(Submission::class);
    }

}
