@props([
    'faqs' => [], // Collection of FAQ models
])

@if($faqs->count() > 0)

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $index => $faq)
        {
            "@type": "Question",
            "name": "{{ $faq->questions }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ strip_tags($faq->answers) }}"
            }
        }@if($index < $faqs->count() - 1),@endif
        @endforeach
    ]
}
</script>

@endif