<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>ورود به پنل مدیریت — {{ config('kian.brand.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="grid min-h-dvh place-items-center bg-ink-950 px-5 py-10 text-sand-100">

    <div class="pointer-events-none fixed inset-0 opacity-70" aria-hidden="true"
         style="background: radial-gradient(60% 50% at 50% 0%, rgba(180,85,45,.28), transparent 65%)"></div>

    <main class="relative w-full max-w-sm">
        <div class="flex flex-col items-center text-center">
            <x-brand-mark class="h-12 w-12" />
            <h1 class="mt-5 text-2xl font-extrabold text-sand-50">پنل مدیریت</h1>
            <p class="mt-1.5 text-meta text-sand-200/50">{{ config('kian.brand.legal_name') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}"
              class="mt-8 rounded-[var(--radius-panel)] border border-white/10 bg-white/[0.04] p-6 backdrop-blur-sm">
            @csrf

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3">
                    <p class="text-[0.9375rem] text-red-200">{{ $errors->first() }}</p>
                </div>
            @endif

            <div>
                <label for="email" class="mb-2 block text-meta font-semibold text-sand-200/80">ایمیل</label>
                <input id="email" name="email" type="email" required autofocus autocomplete="username"
                       value="{{ old('email') }}" dir="ltr"
                       class="w-full rounded-xl border border-white/12 bg-ink-950/60 px-4 py-3 text-sand-50 outline-none transition focus:border-clay-400">
            </div>

            <div class="mt-4">
                <label for="password" class="mb-2 block text-meta font-semibold text-sand-200/80">گذرواژه</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-white/12 bg-ink-950/60 px-4 py-3 text-sand-50 outline-none transition focus:border-clay-400">
            </div>

            <label class="mt-4 flex cursor-pointer items-center gap-2.5 text-meta text-sand-200/70">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-[var(--color-clay-500)]">
                مرا به خاطر بسپار
            </label>

            <button type="submit"
                    class="tap mt-6 w-full justify-center rounded-xl bg-clay-500 font-semibold text-white transition hover:bg-clay-600">
                ورود
            </button>
        </form>

        <p class="mt-5 text-center text-micro leading-relaxed text-sand-200/35">
            پس از پنج تلاش ناموفق، ورود برای پنج دقیقه قفل می‌شود.
        </p>
    </main>
</body>
</html>
