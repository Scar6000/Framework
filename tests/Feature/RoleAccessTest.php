<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an administrator only opens the admin workspace', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('admin-indicators-title');

    $this->actingAs($admin)->get(route('agent.dashboard'))->assertForbidden();
    $this->actingAs($admin)->get(route('chef.dashboard'))->assertForbidden();
});

test('an agent only opens the scolarite workspace', function () {
    $agent = User::factory()->agent()->create();

    $this->actingAs($agent)
        ->get(route('agent.dashboard'))
        ->assertOk()
        ->assertSee('agent-indicators-title');

    $this->actingAs($agent)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($agent)->get(route('chef.dashboard'))->assertForbidden();
});

test('a department head only opens the chef workspace', function () {
    $chef = User::factory()->chef()->create();

    $this->actingAs($chef)
        ->get(route('chef.dashboard'))
        ->assertOk()
        ->assertSee('chef-indicators-title');

    $this->actingAs($chef)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($chef)->get(route('agent.dashboard'))->assertForbidden();
});
