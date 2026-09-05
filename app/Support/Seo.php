<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\SiteSetting;

class Seo
{
    /**
     * Build the `seo` prop shared with the Blade root view (title, description,
     * social preview data). Rendered server-side so it's visible to bots that
     * don't execute JavaScript (WhatsApp, Twitter, etc.) and to Google.
     *
     * `title` is the raw page title (e.g. "Servicios"); the " - {app.name}"
     * suffix is applied once, consistently, in app.blade.php — matching the
     * same suffix `resources/js/app.ts` applies client/SSR-side to `<title>` —
     * so it's never duplicated regardless of whether SSR rendering succeeds.
     *
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public static function make(string $title, string $description, array $extra = []): array
    {
        return array_merge([
            'title' => $title,
            'description' => $description,
        ], $extra);
    }

    /**
     * Organization schema for the brand as a whole (shown on the homepage).
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $settings = SiteSetting::current();
        $sameAs = array_values(array_filter([
            $settings->instagram_url,
            $settings->facebook_url,
            $settings->tiktok_url,
        ]));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->site_name ?: config('app.name'),
            'description' => $settings->footer_description,
            'url' => url('/'),
            'logo' => $settings->logo_url,
            'sameAs' => $sameAs ?: null,
        ]);
    }

    /**
     * HairSalon (LocalBusiness) schema for a single branch, for local SEO
     * (Google Maps / "near me" search).
     *
     * @return array<string, mixed>
     */
    public static function hairSalon(Branch $branch): array
    {
        $days = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $openingHours = collect($branch->opening_days ?? [])
            ->filter(fn ($day) => isset($days[$day]))
            ->map(fn ($day) => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => "https://schema.org/{$days[$day]}",
                'opens' => substr((string) $branch->opening_time, 0, 5),
                'closes' => substr((string) $branch->closing_time, 0, 5),
            ])
            ->values()
            ->all();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'HairSalon',
            'name' => $branch->name,
            'description' => $branch->description,
            'url' => url("/sucursales/{$branch->slug}"),
            'telephone' => $branch->phone,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $branch->address,
                'addressLocality' => $branch->city?->name,
                'addressRegion' => $branch->city?->state,
                'addressCountry' => 'MX',
            ],
            'geo' => ($branch->latitude && $branch->longitude) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $branch->latitude,
                'longitude' => (float) $branch->longitude,
            ] : null,
            'openingHoursSpecification' => $openingHours ?: null,
            'image' => $branch->image,
        ]);
    }
}
