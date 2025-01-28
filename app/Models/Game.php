<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function bonusHuntGames(): HasMany
    {
        return $this->hasMany(BonusHuntGame::class);
    }

    public function bonusBuyGames(): HasMany
    {
        return $this->hasMany(BonusBuyGame::class);
    }

    public function bonusConcurrents(): HasMany
    {
        return $this->hasMany(BonusConcurrent::class);
    }
}
