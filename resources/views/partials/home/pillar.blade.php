{{--
    یک ویژگی در ستون‌های کناری «چرا سفال؟».

    ستون‌ها حالا نصف عرض قبلی‌اند، پس کارت فشرده‌تر است: آیکون کوچک‌تر، عنوان و
    برچسب انگلیسی زیر هم به‌جای کنار هم، و عدد در یک ردیف با فاصله‌ی کمتر.
--}}
<li data-reveal
    class="group grid grid-cols-[auto_1fr] gap-4 border-b border-white/[0.08] py-6">
    <span class="grid h-11 w-11 place-items-center rounded-xl border border-white/10 bg-white/[0.05] text-clay-400 transition-colors duration-500 group-hover:border-clay-500/40 group-hover:bg-clay-500/15 group-hover:text-clay-300">
        <x-icon :name="$pillar['icon']" size="20" />
    </span>

    <div class="min-w-0">
        <h3 class="text-h3 font-bold leading-snug text-sand-50">{{ $pillar['title'] }}</h3>
        <p class="tech mt-1 text-micro uppercase tracking-[0.16em] text-sand-200/35">{{ $pillar['en'] }}</p>

        <p class="mt-3 leading-relaxed text-sand-200/65">{{ $pillar['text'] }}</p>

        <p class="mt-3.5 flex flex-wrap items-baseline gap-x-2">
            <span class="tech text-xl font-extrabold text-clay-300">{{ $pillar['metric'] }}</span>
            <span class="text-meta text-sand-200/45">{{ $pillar['metricLabel'] }}</span>
        </p>
    </div>
</li>
