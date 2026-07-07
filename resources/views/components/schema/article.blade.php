@props([
    'article' => null, // Article model
])

@if($article)
<script type="application/ld+json">
{!! json_encode(\App\Services\SchemaService::article($article), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif