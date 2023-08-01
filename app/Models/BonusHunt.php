<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusHunt extends Model
{
    use HasFactory;

    protected $guarded = [
    'id',
    ];

    public function bonusHuntGame()
    {
        return $this->hasMany(BonusHuntGame::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }
}
