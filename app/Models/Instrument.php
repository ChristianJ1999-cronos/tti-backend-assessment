<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrument extends Model
{
    /** @use HasFactory<\Database\Factories\InstrumentFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description'
    ];


    public function questions(): HasMany{
        return $this->hasMany(Question::class);
    }

    public function submissions(): HasMany{
        return $this->hasMany(Submission::class);
    }

}