@props([
    'product' => null, // Product model
])

@if($product)
<script type="application/ld+json">
{!! json_encode(\App\Services\SchemaService::product($product), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif