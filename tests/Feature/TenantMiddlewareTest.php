<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_is_resolved_by_domain(): void
    {
        $tenant = Tenant::factory()->create(['dominio' => 'demo.sia.local']);

        $response = $this->get('http://demo.sia.local/login');

        $response->assertStatus(200);
        $this->assertEquals($tenant->id, app('tenant')->id);
    }
}
