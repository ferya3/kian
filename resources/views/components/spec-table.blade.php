@props(['product', 'dense' => false])

<div class="overflow-hidden rounded-2xl border border-sand-300">
    <table class="w-full text-right">
        <caption class="sr-only">{{ __('site.spec.caption', ['name' => $product->name]) }}</caption>
        <thead class="bg-sand-200/70">
            <tr>
                <th scope="col" class="px-5 py-3 text-meta font-bold text-ink-600">{{ __('site.spec.feature') }}</th>
                <th scope="col" class="px-5 py-3 text-left text-meta font-bold text-ink-600">{{ __('site.spec.value') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-sand-200 bg-sand-50">
            @foreach($product->specSheet() as $spec)
                <tr class="transition hover:bg-sand-100">
                    <th scope="row" class="px-5 {{ $dense ? 'py-2.5' : 'py-3.5' }} text-right text-[0.9375rem] font-normal text-ink-500">
                        {{ $spec['label'] }}
                    </th>
                    <td class="px-5 {{ $dense ? 'py-2.5' : 'py-3.5' }} text-left font-bold text-ink-900">
                        <x-num :value="$spec['value']" />
                        <span class="text-micro font-normal text-ink-400">{{ $spec['unit'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
