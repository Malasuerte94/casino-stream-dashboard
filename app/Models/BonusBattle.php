<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusBattle extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function concurrents(): HasMany
    {
        return $this->hasMany(BonusConcurrent::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(BonusStage::class);
    }

    /**
     * Get the last active stage for the bonus battle.
     *
     * @return HasMany|Model|object
     */
    public function getLastActiveStageAttribute()
    {
        return $this->stages()
            ->where('active', true)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Get the last ended stage for the bonus battle.
     *
     * @return HasMany|Model|object
     */
    public function getLastEndedStageAttribute()
    {
        return $this->stages()
            ->where('active', false)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
