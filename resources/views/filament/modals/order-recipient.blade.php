<div class="space-y-3 p-2">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500">姓名</label>
            <div class="text-sm font-medium">{{ $order->name }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">電話</label>
            <div class="text-sm font-medium">{{ $order->phone }}</div>
        </div>
        <div class="col-span-2">
            <label class="text-xs text-gray-500">電子郵箱</label>
            <div class="text-sm font-medium">{{ $order->email }}</div>
        </div>
    </div>

    <div class="border-t pt-3">
        <label class="text-xs text-gray-500">配送方式</label>
        <div class="text-sm font-medium mt-1">
            {{ \App\Models\Order::DELIVERY_TYPE_TXT[$order->delivery_type] ?? '宅配到府' }}
        </div>
    </div>

    @if ($order->delivery_type > 0)
        <div class="border-t pt-3">
            <label class="text-xs text-gray-500">超商信息</label>
            <div class="text-sm mt-1">
                <div>門市名稱：{{ $order->shop_name ?? '未知' }}</div>
                <div>門市編號：{{ $order->shop_no ?? '未知' }}</div>
                @if ($order->shop_type)
                    <div>超商類型：{{ \App\Models\Order::SHOP_TYPE_TXT[$order->shop_type] ?? '未知' }}</div>
                @endif
            </div>
        </div>
    @endif

    @if ($order->delivery_time)
        <div class="border-t pt-3">
            <label class="text-xs text-gray-500">配送時段</label>
            <div class="text-sm mt-1">{{ \App\Models\Order::DELIVERY_TIME[$order->delivery_time] ?? $order->delivery_time }}</div>
        </div>
    @endif
</div>
