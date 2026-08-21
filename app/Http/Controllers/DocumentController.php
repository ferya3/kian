<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download(Document $document)
    {
        $document->increment('download_count');

        // فایل واقعی روی دیسک public قرار می‌گیرد؛ تا وقتی آپلود نشده،
        // کاربر به صفحه‌ی درخواست فایل هدایت می‌شود تا لینک خالی نبیند.
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download(
                $document->file_path,
                $document->slug.'.'.$document->format
            );
        }

        return redirect()
            ->route('contact', ['type' => 'technical', 'document' => $document->slug])
            ->with('notice', "فایل «{$document->title}» به‌درخواست ارسال می‌شود. لطفاً فرم زیر را تکمیل کنید تا واحد فنی آن را برای شما بفرستد.");
    }
}
