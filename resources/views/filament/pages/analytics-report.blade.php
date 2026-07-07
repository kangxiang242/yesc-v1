<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-500 text-white rounded-xl p-4 shadow-sm">
            <h3 class="text-2xl font-bold">{{ $stats['web_pv'] }}</h3>
            <p class="text-sm opacity-80">WEB 瀏覽量</p>
        </div>
        <div class="bg-green-500 text-white rounded-xl p-4 shadow-sm">
            <h3 class="text-2xl font-bold">{{ $stats['mobile_pv'] }}</h3>
            <p class="text-sm opacity-80">手機瀏覽量</p>
        </div>
        <div class="bg-amber-500 text-white rounded-xl p-4 shadow-sm">
            <h3 class="text-2xl font-bold">{{ $stats['order_submits'] }}</h3>
            <p class="text-sm opacity-80">訂單提交</p>
        </div>
        <div class="bg-red-500 text-white rounded-xl p-4 shadow-sm">
            <h3 class="text-2xl font-bold">{{ $stats['page_leaves'] }}</h3>
            <p class="text-sm opacity-80">離開事件</p>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
