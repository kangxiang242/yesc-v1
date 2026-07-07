<div class="space-y-2 p-2">
    @forelse ($order->products as $item)
        <div class="border rounded-lg p-3 {{ $loop->first ? '' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <strong class="text-sm">{{ $item->product_name }}</strong>
                    @if (!empty($item->is_added))
                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">加購</span>
                    @endif
                </div>
                <a href="/product/{{ $item->product_id }}" target="_blank" class="text-xs text-primary-600 hover:underline">查看商品</a>
            </div>
            <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                <div>
                    <span class="text-gray-500">數量：</span>
                    <span class="font-medium">{{ $item->number }}</span>
                </div>
                <div>
                    <span class="text-gray-500">單價：</span>
                    <span class="font-medium">NT$ {{ number_format($item->unit_price) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">小計：</span>
                    <span class="font-medium">NT$ {{ number_format($item->total_price) }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-gray-500 text-sm">無商品記錄</div>
    @endforelse
</div>
