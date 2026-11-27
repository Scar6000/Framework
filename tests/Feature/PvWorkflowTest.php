<?php

use App\Models\Pv;
use App\Models\User;
use Database\Seeders\TestAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(TestAccountsSeeder::class);
});

test('credentials open the dashboard matching the role', function () {
    $this->post(route('login.store'), ['email' => 'admin@eneam.bj', 'password' => 'Admin@2026'])
        ->assertRedirect(route('admin.dashboard'));

    $this->post(route('login.store'), ['email' => 'scolarite@eneam.bj', 'password' => 'Scolarite@2026'])
        ->assertRedirect(route('agent.dashboard'));

    $this->post(route('login.store'), ['email' => 'chef.ig@eneam.bj', 'password' => 'ChefIg@2026'])
        ->assertRedirect(route('chef.dashboard'));
});

test('the new PV form requires the role and promotion chooser and shows the prefilled banner', function () {
    /** @var User $admin */
    $admin = User::where('email', 'admin@eneam.bj')->firstOrFail();

    $this->actingAs($admin)->get(route('admin.pv.create'))
        ->assertRedirect(route('admin.pv.index').'#pv-choice-modal');

    [$filiereId, $promotionId] = promotionIds('IG', 'L1');

    $this->actingAs($admin)->get(route('admin.pv.create', [
        'filiere_id' => $filiereId,
        'promotion_id' => $promotionId,
    ]))
        ->assertOk()
        ->assertSee('Nouveau procès-verbal')
        ->assertSee('PV-2026-IG-L1-001')
        ->assertSee('Informatique de gestion');
});

test('an agent stores a draft PV with a real upload and writes an audit log', function () {
    /** @var User $agent */
    $agent = User::where('email', 'scolarite@eneam.bj')->firstOrFail();
    [$filiereId, $promotionId] = promotionIds('IG', 'L1');

    $this->actingAs($agent)->post(route('agent.pv.store'), [
        'action' => 'draft',
        'filiere_id' => $filiereId,
        'promotion_id' => $promotionId,
        'session' => 'Normale',
        'date_deliberation' => '2026-07-20',
        'president_jury' => 'Dr Test Jury',
        'fichier' => UploadedFile::fake()->create('pv-l1.pdf', 100, 'application/pdf'),
    ])->assertRedirect(route('agent.pv.index'));

    $this->assertDatabaseHas('pvs', [
        'reference' => 'PV-2026-IG-L1-001',
        'statut' => 'brouillon',
        'created_by' => $agent->id,
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'entite' => 'pv',
        'action' => 'creation',
        'user_id' => $agent->id,
    ]);
});

test('an archived PV can only be edited by an administrator with a mandatory motive', function () {
    /** @var User $admin */
    $admin = User::where('email', 'admin@eneam.bj')->firstOrFail();
    /** @var User $agent */
    $agent = User::where('email', 'scolarite@eneam.bj')->firstOrFail();
    /** @var Pv $archive */
    $archive = Pv::where('reference', 'PV-2026-IG-L3-014')->firstOrFail();

    $this->actingAs($agent)->get(route('agent.pv.edit', $archive->reference))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.pv.edit', $archive->reference))->assertOk();

    $payload = [
        'action' => 'draft',
        'date_deliberation' => '2026-07-15',
        'president_jury' => 'Pr Aurélien Kossi',
        'membres_jury' => ['Dr Nadine Sossou'],
    ];

    $this->actingAs($admin)->put(route('admin.pv.update', $archive->reference), $payload)
        ->assertSessionHasErrors('motif');

    $this->actingAs($admin)->put(route('admin.pv.update', $archive->reference), $payload + [
        'motif' => 'Correction de la date de délibération',
    ])->assertRedirect(route('admin.pv.index'));

    $this->assertDatabaseHas('pvs', [
        'reference' => $archive->reference,
        'date_deliberation' => '2026-07-15 00:00:00',
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'entite_id' => $archive->id,
        'action' => 'modification',
        'description' => 'Motif : Correction de la date de délibération — '.$archive->reference,
    ]);
});

/**
 * @return array{int, int}
 */
function promotionIds(string $filiereCode, string $niveauCode): array
{
    $filiereId = (int) DB::table('filieres')->where('code', $filiereCode)->value('id');
    $promotionId = (int) DB::table('promotions')
        ->join('filieres', 'filieres.id', '=', 'promotions.filiere_id')
        ->join('semestres', 'semestres.id', '=', 'promotions.semestre_id')
        ->join('niveaux', 'niveaux.id', '=', 'semestres.niveau_id')
        ->join('annees_academiques', 'annees_academiques.id', '=', 'promotions.annee_academique_id')
        ->where('filieres.code', $filiereCode)
        ->where('niveaux.code', $niveauCode)
        ->where('annees_academiques.libelle', '2025-2026')
        ->value('promotions.id');

    return [$filiereId, $promotionId];
}
