<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattlePredictionWinner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relationship to Bonus Battle
     * @return BelongsTo
     */
    public function bonusBattle(): BelongsTo {
        return $this->belongsTo(BonusBattle::class);
    }

    /**
     * Relationship to the game winner.
     * @return BelongsTo
     */
    public function gameWinner(): BelongsTo {
        return $this->belongsTo(BattlePrediction::class, 'game_winner_bp_id');
    }

    /**
     * Relationship to the exact highest game.
     * @return BelongsTo
     */
    public function gameLowest(): BelongsTo {
        return $this->belongsTo(BattlePrediction::class, 'game_lowest_bp_id');
    }

    /**
     * Relationship to the exact lowest game.
     * @return BelongsTo
     */
    public function gameHighest(): BelongsTo {
        return $this->belongsTo(BattlePrediction::class, 'game_highest_bp_id');
    }
}
