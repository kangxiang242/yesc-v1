<div class="space-y-3 p-2">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500">訂單號</label>
            <div class="text-sm font-medium">{{ $order->no }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">內部訂單號</label>
            <div class="text-sm font-medium">{{ $order->inside_no }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">訂單總價</label>
            <div class="text-sm font-medium">NT$ {{ number_format($order->total_price) }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">運費</label>
            <div class="text-sm font-medium">NT$ {{ number_format($order->freight) }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">商品金額</label>
            <div class="text-sm font-medium">NT$ {{ number_format($order->product_price) }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">訂單狀態</label>
            <div class="text-sm font-medium">{{ \App\Models\Order::STATUS_TXT[$order->status] ?? $order->status }}</div>
        </div>
    </div>
    @if ($order->remarks)
        <div>
            <label class="text-xs text-gray-500">客戶備注</label>
            <div class="text-sm bg-gray-50 rounded p-2 mt-1">{{ $order->remarks }}</div>
        </div>
    @endif
    @if ($order->admin_remarks)
        <div>
            <label class="text-xs text-gray-500">管理員備注</label>
            <div class="text-sm bg-yellow-50 rounded p-2 mt-1">{{ $order->admin_remarks }}</div>
        </div>
    @endif
    <div>
        <label class="text-xs text-gray-500">下單時間</label>
        <div class="text-sm">{{ $order->created_at }}</div>
    </div>
    @if ($order->is_test)
        <div class="mt-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">測試訂單</span>
        </div>
    @endif
</div>
