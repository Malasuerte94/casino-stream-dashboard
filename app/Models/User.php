<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use InteractsWithMedia;

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->yt_code)) {
                $user->yt_code = Str::random(10);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'stake_username',
        'yt_code',
        'yt_name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }

    /**
     * @return HasOne
     */
    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    /**
     * @return HasMany
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * @return HasMany
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * @return HasMany
     */
    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class);
    }

    /**
     * @return HasMany
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    /**
     * @return HasMany
     */
    public function bannerAds(): HasMany
    {
        return $this->hasMany(BannerAd::class);
    }

    /**
     * @return HasMany
     */
    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    /**
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    /**
     * @return HasMany
     */
    public function guessEntries(): HasMany
    {
        return $this->hasMany(GuessEntry::class);
    }

    /**
     * @return HasMany
     */
    public function streamAccounts(): HasMany
    {
        return $this->hasMany(StreamAccount::class);
    }

    /**
     * @return boolean
     */
    public function isStreamer(): bool
    {
        return $this->role?->identifier === 'streamer';
    }

    /**
     * @return HasMany
     */
    public function refferals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * @return HasMany
     */
    public function bonusBattles(): HasMany
    {
        return $this->hasMany(BonusBattle::class);
    }

    /**
     * @return HasMany
     */
    public function bonusHunts(): HasMany
    {
        return $this->hasMany(BonusHunt::class);
    }

    /**
     * @return HasMany
     */
    public function bonusBuys(): HasMany
    {
        return $this->hasMany(BonusBuy::class);
    }

    /**
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * @return HasMany
     */
    public function liveStats(): HasMany
    {
        return $this->hasMany(LiveStat::class);
    }

    /**
     * @return HasMany
     */
    public function battleViewers(): HasMany
    {
        return $this->hasMany(BattleViewer::class);
    }

    /**
     * @return HasMany
     */
    public function battlePredictions(): HasMany
    {
        return $this->hasMany(BattlePrediction::class);
    }

    /**
     * Get the Discord webhook URL for the user's schedule announcer.
     *
     * @return string|null
     */
    public function getWebhookScheduleAttribute(): ?string
    {
        $setting = UserSetting::where('user_id', $this->id)
            ->where('name', 'discord_wbh_schedule')
            ->first();

        return $setting?->value;
    }

    /**
     * Get the Discord webhook URL for the user's schedule announcer.
     *
     * @return string|null
     */
    public function getWebhookHuntBuyBattleAttribute(): ?string
    {
        $setting = UserSetting::where('user_id', $this->id)
            ->where('name', 'discord_wbh_hunt_battle')
            ->first();

        return $setting?->value;
    }
}
