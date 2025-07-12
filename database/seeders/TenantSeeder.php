<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tenant::create([
            'nome' => 'Empresa Demo',
            'dominio' => 'demo.sia.local',
            'ativo' => true,
        ]);

        \App\Models\Tenant::create([
            'nome' => 'IFTO - Instituto Federal',
            'dominio' => 'ifto.sia.local',
            'ativo' => true,
        ]);
    }
}
