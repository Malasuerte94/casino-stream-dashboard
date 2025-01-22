<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusBuy extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function bonusBuyGames(): HasMany
    {
        return $this->hasMany(BonusBuyGame::class);
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
