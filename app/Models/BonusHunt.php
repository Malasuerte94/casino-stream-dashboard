<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusHunt extends Model
{
    use HasFactory;

    protected $guarded = [
    'id',
    ];

    /**
     * @return HasMany
     */
    public function bonusHuntGames(): HasMany
    {
        return $this->hasMany(BonusHuntGame::class);
    }

    /**
     * @return BelongsTo
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    /**
     * @return HasMany
     */
    public function guessEntries(): HasMany
    {
        return $this->hasMany(GuessEntry::class);
    }
}
