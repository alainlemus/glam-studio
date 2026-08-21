<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name' => 'María González',
                'quote' => 'El mejor salón que he visitado. El resultado superó mis expectativas, ¡100% recomendado!',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Renata Ríos',
                'quote' => 'Excelente atención desde que llegas hasta que sales. Mi balayage quedó exactamente como lo pedí.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Paula Salinas',
                'quote' => 'Agendar por WhatsApp fue súper fácil y me confirmaron al instante. Volveré sin duda.',
                'rating' => 4,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::firstOrCreate(
                ['client_name' => $testimonial['client_name']],
                $testimonial
            );
        }
    }
}
