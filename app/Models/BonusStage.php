<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusStage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bonusBattle(): BelongsTo
    {
        return $this->belongsTo(BonusBattle::class);
    }

    public function brackets(): HasMany
    {
        return $this->hasMany(Bracket::class);
    }

    /**
     * Dynamic attribute to get the first unfinished bracket for the stage.
     *
     * @return HasMany|Model|null
     */
    public function getActiveBracketAttribute(): Model|HasMany|null
    {
        return $this->brackets()
            ->where('is_finished', false)
            ->orderBy('created_at', 'asc')
            ->first();
    }

}
