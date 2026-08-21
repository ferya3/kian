<section class="bg-sand-50 py-20 lg:py-28" aria-labelledby="projects-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Reference projects"
                title="پروژه‌های اجراشده"
                lead="از برج اداری بیست‌ودو طبقه تا هزار و دویست واحد مسکن ملی — هر پروژه یک مسئله‌ی متفاوت داشت."
                id="projects-heading" class="lg:max-w-2xl" />

            <div data-reveal>
                <x-cta :href="route('projects.index')" variant="ghost">همه پروژه‌ها</x-cta>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-12" data-reveal-stagger="120">
            <div class="lg:col-span-7" data-reveal>
                <x-project-card :project="$projects->first()" featured class="h-full" />
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
                @foreach($projects->skip(1)->take(2) as $project)
                    <x-project-card :project="$project" data-reveal />
                @endforeach
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="100">
            @foreach($projects->skip(3)->take(3) as $project)
                <x-project-card :project="$project" data-reveal />
            @endforeach
        </div>
    </div>
</section>
