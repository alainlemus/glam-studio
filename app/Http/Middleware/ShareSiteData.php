<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareSiteData
{
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share('site', [
            'name' => config('app.name', 'Salones Belleza'),
            'branches' => fn () => Branch::with('city')->active()->get(['id', 'name', 'slug', 'address', 'phone', 'whatsapp', 'city_id']),
            'settings' => fn () => SiteSetting::current(),
        ]);

        return $next($request);
    }
}