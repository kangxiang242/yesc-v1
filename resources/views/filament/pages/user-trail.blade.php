<x-filament-panels::page>
    @if($selectedIp)
        <div class="mb-4">
            <a href="{{ \App\Filament\Pages\UserTrail::getUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                ← 返回列表
            </a>
        </div>

        <div class="mb-6 p-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600">{{ $trailSummary }}</p>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
