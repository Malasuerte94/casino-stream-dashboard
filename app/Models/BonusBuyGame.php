<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BonusBuyGame extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function bonusBuy(): BelongsTo
    {
        return $this->belongsTo(BonusBuy::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
