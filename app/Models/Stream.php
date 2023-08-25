<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stream extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function bonusBuys(): HasMany
    {
        return $this->hasMany(BonusBuy::class);
    }

    public function bonusHunts(): HasMany
    {
        return $this->hasMany(BonusHunt::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
