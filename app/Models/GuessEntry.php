<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuessEntry extends Model
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
    public function bonusHunt(): BelongsTo {
        return $this->belongsTo(BonusHunt::class);
    }

    /**
     * @return BelongsTo
     */
    public function bonusBuy(): BelongsTo {
        return $this->belongsTo(BonusBuy::class);
    }

    /**
     * Relationship to the Winner model (if this entry won something).
     * @return HasOne
     */
    public function winner(): HasOne {
        return $this->hasOne(Winner::class);
    }

}
