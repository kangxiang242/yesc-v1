<div class="space-y-3 p-2">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500">IP 地址</label>
            <div class="text-sm font-medium font-mono">{{ $order->ip }}</div>
        </div>
        <div>
            <label class="text-xs text-gray-500">IP 國家</label>
            <div class="text-sm font-medium">{{ $order->ipcountry ?? '未知' }}</div>
        </div>
    </div>

    @if ($order->user_agent)
        <div class="border-t pt-3">
            <label class="text-xs text-gray-500">User Agent</label>
            <div class="text-xs bg-gray-50 rounded p-2 mt-1 break-all font-mono">{{ $order->user_agent }}</div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-500">設備</label>
                <div class="text-sm font-medium">{{ \App\Handlers\DeviceTypeHandlers::getDevice($order->user_agent) }}</div>
            </div>
            <div>
                <label class="text-xs text-gray-500">瀏覽器</label>
                <div class="text-sm font-medium">{{ \App\Handlers\DeviceTypeHandlers::getBrowser($order->user_agent) ?: '未知' }}</div>
            </div>
        </div>
    @endif
</div>
