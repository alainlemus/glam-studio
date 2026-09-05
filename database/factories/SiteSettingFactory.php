<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_name' => 'Glam Studio',
            'tagline' => 'Beauty & More',
            'footer_description' => fake()->sentence(),
        ];
    }
}
