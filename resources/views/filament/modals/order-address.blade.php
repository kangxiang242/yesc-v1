<div class="space-y-3 p-2">
    @if ($order->delivery_type > 0)
        <div>
            <label class="text-xs text-gray-500">超商門市</label>
            <div class="text-sm font-medium mt-1">{{ $order->shop_name ?? '未知門市' }}</div>
            <div class="text-sm text-gray-500">編號：{{ $order->shop_no ?? '' }}</div>
        </div>
        @if ($order->address)
            <div class="border-t pt-3">
                <label class="text-xs text-gray-500">門市地址</label>
                <div class="text-sm mt-1">{{ $order->address }}</div>
            </div>
        @endif
    @else
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="text-xs text-gray-500">城市</label>
                <div class="text-sm font-medium">{{ $order->city }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">區</label>
                <div class="text-sm font-medium">{{ $order->county }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">路段</label>
                <div class="text-sm font-medium">{{ $order->street }}</div>
            </div>
        </div>
        <div class="border-t pt-3">
            <label class="text-xs text-gray-500">詳細地址</label>
            <div class="text-sm mt-1">{{ $order->address ?? '無' }}</div>
        </div>
        <div class="text-xs text-gray-400 mt-1">
            完整地址：{{ $order->city }}{{ $order->county }}{{ $order->street }}{{ $order->address }}
        </div>
    @endif
</div>
