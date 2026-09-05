<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $staticUrls = [
            ['loc' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/servicios'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('/productos'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('/sucursales'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('/promociones'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/nosotros'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => url('/contacto'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => url('/agendar'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => url('/aviso-de-privacidad'), 'changefreq' => 'yearly', 'priority' => '0.2'],
        ];

        $branchUrls = Branch::active()->get(['slug', 'updated_at'])->map(fn (Branch $branch) => [
            'loc' => url("/sucursales/{$branch->slug}"),
            'lastmod' => $branch->updated_at?->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ]);

        $urls = collect($staticUrls)->concat($branchUrls);

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /settings',
            'Allow: /',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
