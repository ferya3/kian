<?php

namespace App\Admin\Resources;

use App\Models\Article;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class ArticleResource extends Resource
{
    public static string $model = Article::class;

    public static string $slug = 'articles';

    public static string $label = 'مقالات';

    public static string $singular = 'مقاله';

    public static string $icon = 'file';

    public static string $group = 'مرکز فنی';

    public static string $orderBy = 'published_at';

    public static function searchable(): array
    {
        return ['title', 'excerpt'];
    }

    public static function publicUrl(Model $record): ?string
    {
        return $record->published_at ? route('articles.show', $record) : null;
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان')->rules(['required'])->inList(true),
            Field::slug('slug')->rules(['required'])->half(),
            Field::select('category', 'دسته', [
                'technical' => 'فنی',
                'comparison' => 'مقایسه',
                'guide' => 'راهنما',
                'installation' => 'اجرا',
                'factory' => 'کارخانه',
            ])->rules(['required'])->inList()->half(),
            Field::textarea('excerpt', 'چکیده')->rules(['nullable', 'max:600'])
                ->hint('در کارت مقاله و توضیح سئو استفاده می‌شود.'),
            Field::longtext('body', 'متن مقاله')->rules(['nullable', 'max:40000'])
                ->hint('هر پاراگراف یک خط. برای تیتر، خط را با ### شروع کنید.'),
            Field::text('author', 'نویسنده')->rules(['nullable'])->third(),
            Field::number('reading_time', 'زمان مطالعه')->rules(['nullable', 'min:1', 'max:120'])->suffix('دقیقه')->third(),
            Field::date('published_at', 'تاریخ انتشار')->rules(['nullable'])
                ->hint('خالی یعنی پیش‌نویس — در سایت دیده نمی‌شود.')->inList(true)->third(),
        ];
    }
}
