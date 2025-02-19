<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relationship to Bonus Hunt.
     * @return BelongsTo
     */
    public function bonusHunt(): BelongsTo {
        return $this->belongsTo(BonusHunt::class);
    }

    /**
     * Relationship to Bonus Buy.
     * @return BelongsTo
     */
    public function bonusBuy(): BelongsTo {
        return $this->belongsTo(BonusBuy::class);
    }

    /**
     * Relationship to the closest result winner (guess entry).
     * @return BelongsTo
     */
    public function closestResult(): BelongsTo {
        return $this->belongsTo(GuessEntry::class, 'win_closest_result');
    }

    /**
     * Relationship to the closest biggest multiplier winner (guess entry).
     * @return BelongsTo
     */
    public function closestBiggestMulti(): BelongsTo {
        return $this->belongsTo(GuessEntry::class, 'win_closest_biggest_multi');
    }

    /**
     * Relationship to the closest lowest multiplier winner (guess entry).
     * @return BelongsTo
     */
    public function closestLowestMulti(): BelongsTo {
        return $this->belongsTo(GuessEntry::class, 'win_closest_lowest_multi');
    }

    /**
     * Relationship to the exact biggest multiplier game winner (guess entry).
     * @return BelongsTo
     */
    public function exactBiggestMultiGame(): BelongsTo {
        return $this->belongsTo(GuessEntry::class, 'win_exact_biggest_multi_game');
    }

    /**
     * Relationship to the exact lowest multiplier game winner (guess entry).
     * @return BelongsTo
     */
    public function exactLowestMultiGame(): BelongsTo {
        return $this->belongsTo(GuessEntry::class, 'win_exact_lowest_multi_game');
    }
}
