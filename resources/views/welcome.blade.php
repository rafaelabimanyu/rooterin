<x-app-layout title="Jasa Saluran Mampet & Pipe Cleaning Premium">
    <x-slot name="semanticSchema">
        @php
            $faqEntities = [];
            if(isset($faqs) && is_iterable($faqs)) {
                foreach($faqs as $faq) {
                    $faqEntities[] = [
                        "@type" => "Question",
                        "name" => is_array($faq) ? ($faq['q'] ?? $faq['question'] ?? '') : ($faq->question ?? ''),
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => strip_tags(is_array($faq) ? ($faq['a'] ?? $faq['answer'] ?? '') : ($faq->answer ?? ''))
                        ]
                    ];
                }
            }
            $schema = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => $faqEntities
            ];
        @endphp
        <script type="application/ld+json">
            {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </x-slot>

    {{-- 1. Hero Section - Primary Entry Point --}}
    <x-sections.hero />

    {{-- 2. Trust Banner - Unique Selling Propositions --}}
    <x-sections.trust-banner />
    
    {{-- 3. Features Section - Problem Solver Explanation --}}
    <x-sections.features />

    {{-- 4. Mega CTA - Urgent Problem Engagement --}}
    <x-sections.cta-mega />

    {{-- 5. Services Section - Detailed Solutions Offering --}}
    <x-sections.services :services="$services" />

        {{-- 2.1 Industrial Partners - Social Proof --}}
    <x-sections.partners />

    {{-- 6. Gallery Section - Visual Proof of Completion --}}
    <x-sections.gallery :items="$projects" />

    {{-- 6.5. Testimonial Section - Trusted Reviews --}}
    <x-sections.testimonials :items="$testimonials" />

    {{-- 7. FAQ Section - Addressing Common Concerns --}}
    <x-sections.faq :faqs="$faqs" />

    {{-- 8. Coverage Section - Location Presence --}}
    <x-sections.coverage />

    {{-- Floating/Sticky Components --}}
    <x-sticky-footer />

</x-app-layout>
