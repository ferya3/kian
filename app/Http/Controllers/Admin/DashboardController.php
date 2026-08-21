<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\Product;
use App\Models\Project;
use App\Support\Jalali;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'tiles' => [
                [
                    'label' => 'درخواست‌های جدید',
                    'value' => ContactMessage::where('status', 'new')->count(),
                    'total' => ContactMessage::count(),
                    'route' => route('admin.resource.index', 'messages'),
                    'icon' => 'mail',
                    'urgent' => ContactMessage::where('status', 'new')->exists(),
                ],
                [
                    'label' => 'محصولات فعال',
                    'value' => Product::where('is_active', true)->count(),
                    'total' => Product::count(),
                    'route' => route('admin.resource.index', 'products'),
                    'icon' => 'layers',
                ],
                [
                    'label' => 'پروژه‌ها',
                    'value' => Project::count(),
                    'total' => Project::count(),
                    'route' => route('admin.resource.index', 'projects'),
                    'icon' => 'blueprint',
                ],
                [
                    'label' => 'فایل‌های فنی',
                    'value' => Document::whereNotNull('file_path')->count(),
                    'total' => Document::count(),
                    'route' => route('admin.resource.index', 'documents'),
                    'icon' => 'file',
                ],
            ],
            'messages' => ContactMessage::query()
                ->with('product')
                ->latest()
                ->take(6)
                ->get(),
            'activity' => ActivityLog::query()
                ->whereIn('event', ['created', 'updated', 'deleted'])
                ->latest()
                ->take(8)
                ->get(),
            'warnings' => $this->warnings(),
        ]);
    }

    /**
     * مواردی که مدیر باید بداند — نه به‌عنوان خطا، به‌عنوان کار باقی‌مانده.
     */
    protected function warnings(): array
    {
        $warnings = [];

        $missingFiles = Document::whereNull('file_path')->count();
        if ($missingFiles) {
            $warnings[] = [
                'text' => Jalali::digits($missingFiles).' فایل فنی هنوز آپلود نشده — دکمه‌ی دانلودشان کاربر را به فرم درخواست می‌برد.',
                'route' => route('admin.resource.index', 'documents'),
                'cta' => 'مشاهده فهرست',
            ];
        }

        $drafts = Article::whereNull('published_at')->count();
        if ($drafts) {
            $warnings[] = [
                'text' => Jalali::digits($drafts).' مقاله در حالت پیش‌نویس است و در سایت دیده نمی‌شود.',
                'route' => route('admin.resource.index', 'articles'),
                'cta' => 'مشاهده مقالات',
            ];
        }

        $inactive = Product::where('is_active', false)->count();
        if ($inactive) {
            $warnings[] = [
                'text' => Jalali::digits($inactive).' محصول غیرفعال است و در کاتالوگ نمایش داده نمی‌شود.',
                'route' => route('admin.resource.index', 'products'),
                'cta' => 'مشاهده محصولات',
            ];
        }

        return $warnings;
    }
}
