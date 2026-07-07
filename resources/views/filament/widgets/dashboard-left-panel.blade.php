<x-filament-widgets::widget>
    <div class="card bg-primary text-white mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white" style="width: 70px; height: 70px;">
                    <span class="fw-bold text-primary" style="font-size: 28px;">A</span>
                </div>
            </div>
            <h1 class="text-white mb-3" style="font-weight: 200; font-size: 2rem;">ADMIN</h1>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ url('/mgx7k9p2/products') }}" class="btn btn-sm text-white" style="border: 1px solid rgba(255,255,255,0.4); border-radius: 20px; padding: 4px 18px; font-size: 12px; text-decoration: none;">產品列表</a>
                <a href="{{ url('/mgx7k9p2/orders') }}" class="btn btn-sm text-white" style="border: 1px solid rgba(255,255,255,0.4); border-radius: 20px; padding: 4px 18px; font-size: 12px; text-decoration: none;">訂單列表</a>
                <a href="{{ url('/mgx7k9p2/articles') }}" class="btn btn-sm text-white" style="border: 1px solid rgba(255,255,255,0.4); border-radius: 20px; padding: 4px 18px; font-size: 12px; text-decoration: none;">文章列表</a>
                <a href="{{ url('/mgx7k9p2/messages') }}" class="btn btn-sm text-white" style="border: 1px solid rgba(255,255,255,0.4); border-radius: 20px; padding: 4px 18px; font-size: 12px; text-decoration: none;">留言信箱</a>
                <a href="{{ url('/mgx7k9p2/access-logs') }}" class="btn btn-sm text-white" style="border: 1px solid rgba(255,255,255,0.4); border-radius: 20px; padding: 4px 18px; font-size: 12px; text-decoration: none;">訪問記錄</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h3 class="card-title mb-3" style="font-size: 1.1rem;">Environment</h3>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr><td style="width: 140px;">URL</td><td><a href="{{ $appUrl }}" target="_blank">{{ $appUrl }}</a></td></tr>
                        <tr><td>PHP version</td><td>{{ $phpVersion }}</td></tr>
                        <tr><td>Laravel version</td><td>{{ $laravelVersion }}</td></tr>
                        <tr><td>Server</td><td>{{ php_uname('s') }} / {{ php_uname('r') }}</td></tr>
                        <tr><td>Cache driver</td><td>{{ $cacheDriver }}</td></tr>
                        <tr><td>Session driver</td><td>{{ $sessionDriver }}</td></tr>
                        <tr><td>Queue driver</td><td>{{ $queueDriver }}</td></tr>
                        <tr><td>Timezone</td><td>{{ $timezone }}</td></tr>
                        <tr><td>Locale</td><td>{{ $locale }}</td></tr>
                        <tr><td>Env</td><td>{{ $env }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>