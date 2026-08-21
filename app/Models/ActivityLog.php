<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ثبت یک رویداد.
     *
     * عمداً فقط *نام* فیلدهای تغییریافته ذخیره می‌شود، نه مقدارشان: گزارش
     * تغییرات نباید خودش به مخزن داده‌ی حساس تبدیل شود.
     */
    public static function record(string $event, ?Model $subject = null, array $changedKeys = [], ?string $label = null): void
    {
        $user = auth()->user();

        static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'مهمان',
            'event' => $event,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $label,
            'changes' => $changedKeys ?: null,
            'ip' => request()->ip(),
        ]);
    }

    public function eventLabel(): string
    {
        return [
            'created' => 'ایجاد',
            'updated' => 'ویرایش',
            'deleted' => 'حذف',
            'login' => 'ورود',
            'login_failed' => 'ورود ناموفق',
            'logout' => 'خروج',
        ][$this->event] ?? $this->event;
    }
}
