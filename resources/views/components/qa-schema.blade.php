@if(!empty($faqs) && count($faqs))
@php
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => collect($faqs)->map(function ($faq) {
            return [
                "@type" => "Question",
                "name" => strip_tags($faq->title),
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($faq->content),
                ],
            ];
        })->values()->toArray(),
    ];
@endphp
@push('schema')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif
