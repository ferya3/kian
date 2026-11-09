<?php

use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\HandleLegacyUrls;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // پیش از مسیریابی: نشانی‌های سایتِ قدیم مسیری ندارند که به آن برسند
        $middleware->prepend(HandleLegacyUrls::class);

        // سرآیندهای امنیتی و CSP برای هر پاسخ — صفحه‌های خطا و ریدایرکت‌ها هم
        $middleware->append(SecurityHeaders::class);

        $middleware->alias([
            'admin' => EnsureAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
