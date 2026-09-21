<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'مدیر کل',
        'editor' => 'ویرایشگر محتوا',
        'vendor' => 'فروشنده',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'vendor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /** مدیر کل: دسترسی به کاربران، تنظیمات و گزارش فعالیت. */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * فروشنده: همان پنل، ولی فقط عرضه‌ها و سفارش‌های خودش.
     *
     * نقش به‌تنهایی کافی نیست و vendor_id هم باید باشد. کاربری با نقش
     * فروشنده و بدون فروشگاه، اگر «فروشنده» شمرده می‌شد، فهرستی بدون قید
     * می‌دید — یعنی عرضه‌های همه.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor' && $this->vendor_id !== null;
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
