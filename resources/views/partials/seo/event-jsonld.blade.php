@php
    $eventStructuredData = [
        '@type' => 'Event',
        '@id' => $url.'#event',
        'name' => $name,
        'description' => $description,
        'url' => $url,
        'mainEntityOfPage' => $url,
        'image' => [$image],
        'startDate' => $startDate,
        'endDate' => $endDate,
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => $attendanceMode ?? 'https://schema.org/MixedEventAttendanceMode',
        'isAccessibleForFree' => (float) ($price ?? 0) <= 0,
        'organizer' => [
            '@type' => 'Organization',
            'name' => 'IUT Computer Society',
            'url' => url('/'),
            'logo' => asset('assets/logo-white.png'),
        ],
        'location' => [
            '@type' => 'Place',
            'name' => $locationName,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Gazipur',
                'addressCountry' => 'BD',
            ],
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => $registrationUrl ?? $url,
            'price' => $price ?? 0,
            'priceCurrency' => 'BDT',
            'availability' => ($isAvailable ?? true)
                ? 'https://schema.org/InStock'
                : 'https://schema.org/SoldOut',
            'validFrom' => $validFrom ?? '2026-06-18T00:00:00+06:00',
        ],
    ];

    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $eventStructuredData,
            [
                '@type' => 'BreadcrumbList',
                '@id' => $url.'#breadcrumb',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'IUT 12th ICT FEST 2026',
                        'item' => url('/'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $name,
                        'item' => $url,
                    ],
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
