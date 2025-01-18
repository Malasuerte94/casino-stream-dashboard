<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bracket extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(BonusStage::class, 'bonus_stage_id');
    }

    public function participantA(): BelongsTo
    {
        return $this->belongsTo(BonusConcurrent::class, 'participant_a_id');
    }

    public function participantB(): BelongsTo
    {
        return $this->belongsTo(BonusConcurrent::class, 'participant_b_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StageScore::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(BonusConcurrent::class, 'winner_id');
    }
}
