<x-filament-widgets::widget>
    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">頁面訪問排行 (前10個)</h3>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg px-3 py-1">
                    {{ collect($this->getFilters())->get($this->filter) }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                    @foreach ($this->getFilters() as $key => $label)
                        <button wire:click="$set('filter', '{{ $key }}')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 whitespace-nowrap">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-10">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">URL</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-20">訪問次數</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($logs as $i => $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900 truncate" title="{{ $log->url }}">{{ $log->url }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900 text-right font-medium">{{ $log->num }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-sm text-gray-400 text-center">暫無數據</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>