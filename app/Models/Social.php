<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Social
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_token
 * @property string $refresh_token
 */
class Social extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
