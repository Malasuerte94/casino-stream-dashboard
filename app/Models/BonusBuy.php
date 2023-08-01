<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusBuy extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function bonusBuyGame()
    {
        return $this->hasMany(BonusBuyGame::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

}
