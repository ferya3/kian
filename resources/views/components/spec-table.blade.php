@props(['product', 'dense' => false])

<div class="overflow-hidden rounded-2xl border border-sand-300">
    <table class="w-full text-right">
        <caption class="sr-only">جدول مشخصات فنی {{ $product->name }}</caption>
        <thead class="bg-sand-200/70">
            <tr>
                <th scope="col" class="px-5 py-3 text-[0.8125rem] font-bold text-ink-600">ویژگی</th>
                <th scope="col" class="px-5 py-3 text-left text-[0.8125rem] font-bold text-ink-600">مقدار</th>
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
                        <span class="text-[0.75rem] font-normal text-ink-400">{{ $spec['unit'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
