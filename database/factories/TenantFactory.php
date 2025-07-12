<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company,
            'dominio' => $this->faker->domainName,
            'ativo' => true,
        ];
    }
}
