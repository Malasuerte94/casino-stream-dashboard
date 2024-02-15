<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusBuyGame extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function bonusBuy(): BelongsTo
    {
        return $this->belongsTo(BonusBuy::class);
    }
}
