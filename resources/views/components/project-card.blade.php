@props(['project', 'featured' => false])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-[var(--radius-panel)] bg-sand-50 transition-transform duration-500 ease-[var(--ease-out-expo)] hover:-translate-y-1']) }}>
    <div class="relative overflow-hidden {{ $featured ? 'aspect-[16/10]' : 'aspect-[4/3]' }}">
        <div class="absolute inset-0 transition-transform duration-[900ms] ease-[var(--ease-out-expo)] group-hover:scale-105">
            <x-project-cover :project="$project" />
        </div>

        <div class="absolute inset-0 bg-gradient-to-t from-ink-950/75 via-ink-950/10 to-transparent"></div>

        <span class="eyebrow absolute right-4 top-4 rounded-full bg-sand-50/90 px-3 py-1 text-micro text-ink-700 backdrop-blur">
            {{ $project->category?->name }}
        </span>

        <div class="absolute inset-x-0 bottom-0 p-5 lg:p-6">
            <h3 class="{{ $featured ? 'text-h3' : 'text-xl' }} font-extrabold text-sand-50">
                <a href="{{ route('projects.show', $project) }}" class="after:absolute after:inset-0 focus:outline-none">
                    {{ $project->title }}
                </a>
            </h3>
            <p class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-meta text-sand-200/75">
                <span>{{ $project->city }}</span>
                <span class="h-1 w-1 rounded-full bg-sand-200/40"></span>
                <span class="tech">{{ \App\Support\Jalali::digits($project->year) }}</span>
                @if($project->area_sqm)
                    <span class="h-1 w-1 rounded-full bg-sand-200/40"></span>
                    <span class="tech">{{ \App\Support\Jalali::digits(number_format($project->area_sqm)) }} m²</span>
                @endif
            </p>
        </div>
    </div>

    @if($featured)
        <div class="flex flex-1 flex-col p-5 lg:p-6">
            <p class="line-clamp-2 leading-relaxed text-ink-500">{{ $project->summary }}</p>
            @if($project->relationLoaded('products') && $project->products->isNotEmpty())
                <ul class="mt-4 flex flex-wrap gap-1.5">
                    @foreach($project->products->take(3) as $product)
                        <li class="rounded-full border border-sand-300 px-2.5 py-1 text-micro text-ink-500">{{ $product->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</article>
