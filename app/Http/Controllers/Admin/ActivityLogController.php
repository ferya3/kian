<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request)
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->string('event')->toString(), fn ($q, $e) => $q->where('event', $e))
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('user_name', 'like', "%{$term}%")
                ->orWhere('subject_label', 'like', "%{$term}%")))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.activity', [
            'logs' => $logs,
            'events' => [
                'created' => 'ایجاد',
                'updated' => 'ویرایش',
                'deleted' => 'حذف',
                'login' => 'ورود',
                'login_failed' => 'ورود ناموفق',
                'logout' => 'خروج',
            ],
        ]);
    }
}
