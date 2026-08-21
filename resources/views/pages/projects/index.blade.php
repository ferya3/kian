<x-layouts.app>
    <x-page-hero
        eyebrow="Reference projects"
        title="پروژه‌های اجراشده"
        lead="هر پروژه یک مسئله‌ی متفاوت داشت: یکی گواهی انرژی می‌خواست، یکی سرعت اجرا، یکی کاهش بار مرده. اینجا نوشته‌ایم چه چیزی و چرا انتخاب شد." />

    <section class="bg-sand-100 pb-20" x-data="filterable('{{ request('type') ?: 'all' }}')">
        <div class="container-page">
            <div class="sticky z-30 -mx-5 border-b border-sand-300 bg-sand-100/95 px-5 py-4 backdrop-blur-md lg:-mx-12 lg:px-12"
                 style="top: calc(var(--header-h) + var(--safe-top))">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                    <div class="flex flex-wrap gap-1.5" role="group" aria-label="فیلتر نوع پروژه">
                        <button type="button" @click="setFilter('all')"
                                :class="filter === 'all' ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-600 hover:bg-sand-300'"
                                class="tap rounded-full px-4 py-2 text-[0.875rem] font-semibold transition">
                            همه
                            <span class="tech mr-1 text-micro opacity-60">{{ \App\Support\Jalali::digits($projects->count()) }}</span>
                        </button>
                        @foreach($categories as $category)
                            <button type="button" @click="setFilter('{{ $category->slug }}')"
                                    :class="filter === '{{ $category->slug }}' ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-600 hover:bg-sand-300'"
                                    class="tap rounded-full px-4 py-2 text-[0.875rem] font-semibold transition">
                                {{ $category->name }}
                                <span class="tech mr-1 text-micro opacity-60">{{ \App\Support\Jalali::digits($category->projects_count) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="90">
                @foreach($projects as $project)
                    <div data-item x-show="matches('{{ $project->category?->slug }}')" x-transition.opacity.duration.300ms>
                        <x-project-card :project="$project" featured data-reveal class="h-full" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
