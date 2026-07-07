@props([
    'items' => [], // 面包屑数组 [['name' => '首页', 'url' => '/'], ...]
])

@if(count($items) > 0)
<script type="application/ld+json">
{!! json_encode(\App\Services\SchemaService::breadcrumb($items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif