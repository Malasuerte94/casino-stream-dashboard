<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BonusConcurrent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function battle(): BelongsTo
    {
        return $this->belongsTo(BonusBattle::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StageScore::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

}
