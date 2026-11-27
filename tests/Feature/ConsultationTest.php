<?php

use App\Models\User;
use Database\Seeders\TestAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(TestAccountsSeeder::class);
});

test('the administrator search reads students and PVs from the database', function () {
    /** @var User $admin */
    $admin = User::where('email', 'admin@eneam.bj')->firstOrFail();

    $this->actingAs($admin)->get(route('admin.recherche.index'))
        ->assertOk()
        ->assertSee('Aminata Soglo')
        ->assertSee('Mamadou Diallo')
        ->assertSee('PV-2026-IG-L3-014')
        ->assertDontSee('Adjoa K. Koffi');
});

test('the filters narrow the real results', function () {
    /** @var User $admin */
    $admin = User::where('email', 'admin@eneam.bj')->firstOrFail();

    $this->actingAs($admin)->get(route('admin.recherche.index', ['filiere' => 'BF']))
        ->assertOk()
        ->assertSee('Mamadou Diallo')
        ->assertDontSee('Aminata Soglo');
});

test('the department head only sees students and PVs from his department', function () {
    /** @var User $chef */
    $chef = User::where('email', 'chef.ig@eneam.bj')->firstOrFail();

    $this->actingAs($chef)->get(route('chef.recherche.index'))
        ->assertOk()
        ->assertSee('Aminata Soglo')
        ->assertSee('Yannick Assogba')
        ->assertDontSee('Mamadou Diallo');

    $this->actingAs($chef)->get(route('chef.recherche.index', ['filiere' => 'BF']))
        ->assertOk()
        ->assertDontSee('Mamadou Diallo');

    $this->actingAs($chef)->get(route('chef.etudiants.show', ['matricule' => '2026-BF-011']))
        ->assertNotFound();
});

test('a student file shows the real annual parcours', function () {
    /** @var User $agent */
    $agent = User::where('email', 'scolarite@eneam.bj')->firstOrFail();

    $this->actingAs($agent)->get(route('agent.etudiants.show', ['matricule' => '2026-IG-018']))
        ->assertOk()
        ->assertSee('Aminata Soglo')
        ->assertSee('PV-2026-IG-L3-014')
        ->assertSee('14,25');
});
