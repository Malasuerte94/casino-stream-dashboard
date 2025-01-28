<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BonusHuntGame extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function bonusHunt(): BelongsTo
    {
        return $this->belongsTo(BonusHunt::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
