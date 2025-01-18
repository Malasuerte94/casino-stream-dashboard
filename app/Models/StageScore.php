<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageScore extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(BonusStage::class);
    }

    public function bracket(): BelongsTo
    {
        return $this->belongsTo(Bracket::class);
    }

    public function concurrent(): BelongsTo
    {
        return $this->belongsTo(BonusConcurrent::class, 'bonus_concurrent_id');
    }

}
