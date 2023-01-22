<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusBuyGame extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function bonusBuy()
    {
        return $this->belongsTo(BonusBuy::class);
    }
}
