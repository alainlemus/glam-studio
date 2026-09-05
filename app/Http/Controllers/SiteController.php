<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\Client;
use App\Models\MarketingCampaign;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Models\Stylist;
use App\Models\Testimonial;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function home(): Response
    {
        $branches = Branch::with('city')->active()->get();
        $serviceCategories = ServiceCategory::with(['services' => fn ($q) => $q->active()->limit(6)])
            ->active()
            ->orderBy('sort_order')
            ->get();
        $featuredServices = Service::active()->limit(8)->get();
        $campaigns = MarketingCampaign::where('status', 'active')->get();
        $cities = City::active()->withCount('branches')->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->get();

        return Inertia::render('site/Home', [
            'branches' => $branches,
            'serviceCategories' => $serviceCategories,
            'featuredServices' => $featuredServices,
            'campaigns' => $campaigns,
            'cities' => $cities,
            'testimonials' => $testimonials,
            'seo' => Seo::make(
                'Glam Studio · Salón de Belleza y Spa en México',
                'Cortes, coloración, uñas, spa y más en Glam Studio. Encuentra tu sucursal más cercana en varias ciudades de México y agenda tu cita en línea hoy mismo.',
            ),
            'jsonLd' => Seo::organization(),
        ]);
    }

    public function branches(): Response
    {
        $branches = Branch::with('city')
            ->active()
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($b) => $b->city->name);

        return Inertia::render('site/Branches', [
            'groupedBranches' => $branches,
            'seo' => Seo::make(
                'Nuestras Sucursales',
                'Encuentra el salón Glam Studio más cercano a ti. Consulta dirección, horarios y teléfono de cada sucursal y agenda tu cita en línea.',
            ),
        ]);
    }

    public function showBranch(string $slug): Response
    {
        $branch = Branch::with('city')->where('slug', $slug)->active()->firstOrFail();

        $stylists = Stylist::with('user')
            ->where('branch_id', $branch->id)
            ->active()
            ->get();

        return Inertia::render('site/BranchDetail', [
            'branch' => $branch,
            'stylists' => $stylists,
            'seo' => Seo::make(
                $branch->name,
                "Visítanos en {$branch->address}. Conoce horarios, teléfono y estilistas disponibles en {$branch->name}. Agenda tu cita en línea.",
                ['image' => $branch->image],
            ),
            'jsonLd' => Seo::hairSalon($branch),
        ]);
    }

    public function services(): Response
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->active()])
            ->active()
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('site/Services', [
            'categories' => $categories,
            'seo' => Seo::make(
                'Servicios de Belleza y Estética',
                'Descubre nuestros servicios de corte, coloración, tratamientos capilares, uñas, maquillaje y más. Consulta precios y duración, y agenda en línea.',
            ),
        ]);
    }

    public function about(): Response
    {
        $stats = [
            'branches' => Branch::active()->count(),
            'stylists' => Stylist::active()->count(),
            'services' => Service::active()->count(),
            'clients' => Client::active()->count(),
        ];
        $cities = City::withCount('branches')->active()->get();

        return Inertia::render('site/About', [
            'stats' => $stats,
            'cities' => $cities,
            'seo' => Seo::make(
                'Nosotros',
                'Conoce la historia de Glam Studio, nuestro equipo de estilistas y por qué somos un salón de belleza de confianza en México.',
            ),
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
            'seo' => Seo::make(
                'Promociones y Descuentos',
                'Aprovecha nuestras promociones vigentes en servicios y tratamientos de belleza. Descuentos por tiempo limitado en Glam Studio.',
            ),
        ]);
    }

    public function contact(): Response
    {
        $branches = Branch::with('city')->active()->get();

        return Inertia::render('site/Contact', [
            'branches' => $branches,
            'seo' => Seo::make(
                'Contacto',
                '¿Tienes dudas o quieres agendar tu cita? Contáctanos por teléfono, WhatsApp o visita alguna de nuestras sucursales Glam Studio.',
            ),
        ]);
    }

    public function products(): Response
    {
        $categories = ProductCategory::with(['products' => fn ($q) => $q->active()])
            ->active()
            ->get();

        return Inertia::render('site/Products', [
            'categories' => $categories,
            'seo' => Seo::make(
                'Productos de Belleza Profesionales',
                'Shampoos, tratamientos, tintes y productos profesionales para el cuidado de tu cabello y piel, disponibles en nuestras sucursales Glam Studio.',
            ),
        ]);
    }

    public function privacyPolicy(): Response
    {
        $settings = SiteSetting::current();

        return Inertia::render('site/PrivacyPolicy', [
            'content' => $settings->privacy_policy,
            'updatedAt' => $settings->privacy_policy_updated_at,
            'seo' => Seo::make(
                'Aviso de Privacidad',
                'Conoce cómo Glam Studio recaba, usa y protege tus datos personales conforme a la legislación aplicable.',
            ),
        ]);
    }
}
