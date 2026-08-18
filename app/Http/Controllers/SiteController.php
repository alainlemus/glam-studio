<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\MarketingCampaign;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function home(): Response
    {
        $branches = Branch::with('city')->active()->get();
        $serviceCategories = ServiceCategory::with(['services' => fn($q) => $q->active()->limit(6)])
            ->active()
            ->orderBy('sort_order')
            ->get();
        $featuredServices = Service::active()->limit(8)->get();
        $campaigns = MarketingCampaign::where('status', 'active')->get();
        $cities = City::active()->withCount('branches')->get();

        return Inertia::render('site/Home', [
            'branches' => $branches,
            'serviceCategories' => $serviceCategories,
            'featuredServices' => $featuredServices,
            'campaigns' => $campaigns,
            'cities' => $cities,
        ]);
    }

    public function branches(): Response
    {
        $branches = Branch::with('city')
            ->active()
            ->orderBy('name')
            ->get()
            ->groupBy(fn($b) => $b->city->name);

        return Inertia::render('site/Branches', [
            'groupedBranches' => $branches,
        ]);
    }

    public function showBranch(string $slug): Response
    {
        $branch = Branch::with('city')->where('slug', $slug)->active()->firstOrFail();

        $stylists = \App\Models\Stylist::with('user')
            ->where('branch_id', $branch->id)
            ->active()
            ->get();

        return Inertia::render('site/BranchDetail', [
            'branch' => $branch,
            'stylists' => $stylists,
        ]);
    }

    public function services(): Response
    {
        $categories = ServiceCategory::with(['services' => fn($q) => $q->active()])
            ->active()
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('site/Services', [
            'categories' => $categories,
        ]);
    }

    public function about(): Response
    {
        $stats = [
            'branches' => Branch::active()->count(),
            'stylists' => \App\Models\Stylist::active()->count(),
            'services' => Service::active()->count(),
            'clients' => \App\Models\Client::active()->count(),
        ];
        $cities = City::withCount('branches')->active()->get();

        return Inertia::render('site/About', [
            'stats' => $stats,
            'cities' => $cities,
        ]);
    }

    public function promotions(): Response
    {
        $campaigns = MarketingCampaign::with('service')
            ->whereIn('status', ['active', 'scheduled'])
            ->orderBy('start_date', 'desc')
            ->get();

        return Inertia::render('site/Promotions', [
            'campaigns' => $campaigns,
        ]);
    }

    public function contact(): Response
    {
        $branches = Branch::with('city')->active()->get();

        return Inertia::render('site/Contact', [
            'branches' => $branches,
        ]);
    }

    public function products(): Response
    {
        $categories = ProductCategory::with(['products' => fn($q) => $q->active()])
            ->active()
            ->get();

        return Inertia::render('site/Products', [
            'categories' => $categories,
        ]);
    }
}