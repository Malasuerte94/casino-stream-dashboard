<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattlePrediction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function bonusBattle(): BelongsTo {
        return $this->belongsTo(BonusBattle::class);
    }

    public function gameWinner(): BelongsTo {
        return $this->belongsTo(BonusConcurrent::class, 'game_winner_id');
    }

    public function gameLow(): BelongsTo {
        return $this->belongsTo(BonusConcurrent::class, 'game_lowest_multi_id');
    }

    public function gameHigh(): BelongsTo {
        return $this->belongsTo(BonusConcurrent::class, 'game_biggest_multi_id');
    }
}
