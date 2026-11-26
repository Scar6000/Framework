<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutPv;
use App\Models\AnneeAcademique;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Pv;
use App\Models\Semestre;
use App\Models\User;
use App\Services\PromotionGenerator;
use App\Services\PvManquantsService;
use Database\Seeders\AnneeAcademiqueSeeder;
use Database\Seeders\FiliereSeeder;
use Database\Seeders\NiveauSemestreSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PvBackendTest extends TestCase
{
    use RefreshDatabase;

    private const MESSAGE_COMPTE_DESACTIVE = 'Votre compte est désactivé. Pour demander sa réactivation, veuillez contacter l’administrateur de l’application.';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        foreach ([FiliereSeeder::class, NiveauSemestreSeeder::class, AnneeAcademiqueSeeder::class] as $seeder) {
            $this->seed($seeder);
        }
    }

    // ---------- helpers ----------
    private function user(Role $role, ?string $filiere = null, bool $actif = true, bool $changerMdp = false): User
    {
        return User::create([
            'nom' => 'Test', 'prenoms' => 'User', 'email' => uniqid().'@t.test',
            'password' => 'password123', 'role' => $role,
            'filiere_id' => $filiere ? Filiere::where('code', $filiere)->value('id') : null,
            'is_active' => $actif, 'must_change_password' => $changerMdp,
        ]);
    }

    private function promotion(string $filiere, string $semestre = 'S1'): Promotion
    {
        return Promotion::create([
            'annee_academique_id' => AnneeAcademique::enCours()->id,
            'filiere_id' => Filiere::where('code', $filiere)->value('id'),
            'semestre_id' => Semestre::where('code', $semestre)->value('id'),
        ]);
    }

    private function pv(Promotion $p, StatutPv $statut, User $createur): Pv
    {
        return Pv::create([
            'reference' => 'REF-'.uniqid(), 'promotion_id' => $p->id, 'session' => 'normale',
            'date_deliberation' => now()->subDays(3)->toDateString(), 'president_jury' => 'Pr X',
            'fichier_path' => 'pv/x.pdf', 'fichier_nom_original' => 'x.pdf', 'fichier_mime' => 'application/pdf',
            'fichier_taille' => 10, 'statut' => $statut, 'created_by' => $createur->id,
        ]);
    }

    private function ligne(Pv $pv, string $matricule = 'A1', string $decision = 'admis'): void
    {
        $e = Etudiant::ouCreer($matricule, 'DOE', 'John');
        $pv->lignes()->create(['etudiant_id' => $e->id, 'moyenne_generale' => 12, 'decision' => $decision]);
    }

    // ---------- accès ----------
    public function test_invite_est_bloque(): void
    {
        $this->getJson('/pv')->assertUnauthorized();
    }

    public function test_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        $u = $this->user(Role::Scolarite, null, false);

        $this->postJson('/connexion', ['email' => $u->email, 'password' => 'password123'])
            ->assertUnprocessable()
            ->assertInvalid(['email' => self::MESSAGE_COMPTE_DESACTIVE]);
    }

    public function test_compte_desactive_affiche_un_message_dedie(): void
    {
        $u = $this->user(Role::Scolarite, null, false);

        $response = $this->post('/connexion', ['email' => $u->email, 'password' => 'password123']);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('compte_desactive', self::MESSAGE_COMPTE_DESACTIVE);
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(self::MESSAGE_COMPTE_DESACTIVE, false);
    }

    public function test_compte_desactive_en_session_est_deconnecte(): void
    {
        $u = $this->user(Role::Scolarite);
        $u->update(['is_active' => false]);

        $response = $this->actingAs($u)->get(route('dashboard'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('compte_desactive', self::MESSAGE_COMPTE_DESACTIVE);
        $this->assertGuest();
    }

    public function test_mot_de_passe_temporaire_doit_etre_change(): void
    {
        $u = $this->user(Role::Scolarite, null, true, true);

        $this->actingAs($u)->getJson('/pv')->assertForbidden();
    }

    // ---------- isolation par filière ----------
    public function test_le_chef_ne_voit_que_sa_filiere(): void
    {
        $agent = $this->user(Role::Scolarite);
        $ig = $this->pv($this->promotion('IG'), StatutPv::Valide, $agent);
        $gco = $this->pv($this->promotion('GCO'), StatutPv::Valide, $agent);
        $chef = $this->user(Role::Chef, 'IG');

        $r = $this->actingAs($chef)->getJson('/pv')->assertOk();
        $this->assertCount(1, $r->json('pvs.data'));

        $this->actingAs($chef)->getJson("/pv/{$ig->id}")->assertOk();
        $this->actingAs($chef)->getJson("/pv/{$gco->id}")->assertForbidden();
        $this->actingAs($chef)->getJson("/pv/{$gco->id}/scan")->assertForbidden();
    }

    public function test_le_chef_ne_voit_pas_les_brouillons(): void
    {
        $agent = $this->user(Role::Scolarite);
        $brouillon = $this->pv($this->promotion('IG'), StatutPv::Brouillon, $agent);

        $this->actingAs($this->user(Role::Chef, 'IG'))->getJson("/pv/{$brouillon->id}")->assertForbidden();
    }

    public function test_le_chef_ne_voit_pas_les_etudiants_des_autres_filieres(): void
    {
        $agent = $this->user(Role::Scolarite);
        $pvGco = $this->pv($this->promotion('GCO'), StatutPv::Valide, $agent);
        $this->ligne($pvGco, 'GCO1');
        $etudiant = Etudiant::where('matricule', 'GCO1')->first();

        $this->actingAs($this->user(Role::Chef, 'IG'))->getJson("/etudiants/{$etudiant->id}")->assertNotFound();
        $this->actingAs($this->user(Role::Chef, 'GCO'))->getJson("/etudiants/{$etudiant->id}")->assertOk();
    }

    public function test_le_tableau_de_bord_chef_affiche_un_message_personnalise_avec_sa_filiere(): void
    {
        $chef = $this->user(Role::Chef, 'IG');

        $this->actingAs($chef)->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.chef')
            ->assertSee('Bonjour User', false)
            ->assertSee('Chef de département', false)
            ->assertSee('Informatique de Gestion', false);
    }

    public function test_le_registre_et_la_fiche_pv_sont_rendus_en_html_pour_le_chef(): void
    {
        $agent = $this->user(Role::Scolarite);
        $pv = $this->pv($this->promotion('IG'), StatutPv::Soumis, $agent);
        $chef = $this->user(Role::Chef, 'IG');

        $this->actingAs($chef)->get(route('pv.index'))
            ->assertOk()
            ->assertViewIs('pv.index')
            ->assertSee($pv->reference, false);

        $this->actingAs($chef)->get(route('pv.show', $pv))
            ->assertOk()
            ->assertViewIs('pv.show')
            ->assertSee($pv->reference, false)
            ->assertSee('Valider le PV', false);
    }

    public function test_les_pages_consultation_du_chef_sont_rendues_en_html(): void
    {
        $chef = $this->user(Role::Chef, 'IG');

        foreach (['recherche.index', 'pv-manquants.index', 'etudiants.index', 'statistiques.index', 'profil.show', 'notifications.index', 'password.change'] as $route) {
            $this->actingAs($chef)->get(route($route))->assertOk();
        }
    }

    // ---------- workflow d'un PV ----------
    public function test_circuit_complet_creation_soumission_validation(): void
    {
        $agent = $this->user(Role::Scolarite);
        $chef = $this->user(Role::Chef, 'IG');
        $promo = $this->promotion('IG', 'S2');

        $res = $this->actingAs($agent)->withHeaders(['Accept' => 'application/json'])->post('/pv', [
            'promotion_id' => $promo->id, 'session' => 'normale',
            'date_deliberation' => now()->subDay()->toDateString(), 'president_jury' => 'Pr X',
            'fichier' => UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf'),
        ])->assertCreated();
        $id = $res->json('data.pv.id');

        // pas de résultats => refus
        $this->actingAs($agent)->postJson("/pv/{$id}/soumettre")->assertStatus(422);

        $this->actingAs($agent)->postJson("/pv/{$id}/resultats", [
            'matricule' => 'IG001', 'nom' => 'DOE', 'prenoms' => 'John',
            'moyenne_generale' => 13.5, 'decision' => 'admis',
        ])->assertCreated();

        $this->actingAs($agent)->postJson("/pv/{$id}/soumettre")->assertOk();

        // l'agent ne valide pas ; un chef d'une autre filière non plus
        $this->actingAs($agent)->postJson("/pv/{$id}/valider")->assertForbidden();
        $this->actingAs($this->user(Role::Chef, 'GCO'))->postJson("/pv/{$id}/valider")->assertForbidden();

        // rejet : motif obligatoire
        $this->actingAs($chef)->postJson("/pv/{$id}/rejeter", [])->assertStatus(422);

        $this->actingAs($chef)->postJson("/pv/{$id}/valider")->assertOk();
        $this->assertSame(StatutPv::Valide, Pv::find($id)->statut);

        // un PV validé n'est plus modifiable par l'agent
        $this->actingAs($agent)->postJson("/pv/{$id}/resultats", [
            'matricule' => 'IG002', 'nom' => 'ROE', 'prenoms' => 'Jane',
            'moyenne_generale' => 10, 'decision' => 'ajourne',
        ])->assertForbidden();
    }

    // ---------- promotions et PV manquants ----------
    public function test_generation_des_promotions_par_filiere_et_semestre(): void
    {
        $annee = AnneeAcademique::enCours();
        $gen = app(PromotionGenerator::class);

        $r = $gen->generer($annee);
        // 8 filières actives x 6 semestres = 48
        $this->assertSame(48, $r['creees']);

        $this->assertSame(1, Promotion::whereHas('filiere', fn ($q) => $q->where('code', 'IG'))
            ->whereHas('semestre', fn ($q) => $q->where('code', 'S5'))->count());
        $this->assertSame(1, Promotion::whereHas('filiere', fn ($q) => $q->where('code', 'GCO'))
            ->whereHas('semestre', fn ($q) => $q->where('code', 'S5'))->count());

        $this->assertSame(0, $gen->generer($annee)['creees']); // idempotent
    }

    public function test_detection_des_pv_manquants(): void
    {
        $annee = AnneeAcademique::enCours();
        app(PromotionGenerator::class)->generer($annee);
        $service = app(PvManquantsService::class);

        $resume = $service->resume($annee);
        $this->assertSame(48, $resume['attendus']);
        $this->assertSame(48, $resume['manquants']);

        $agent = $this->user(Role::Scolarite);
        $promo = Promotion::where('annee_academique_id', $annee->id)
            ->whereHas('filiere', fn ($q) => $q->where('code', 'IG'))
            ->whereHas('semestre', fn ($q) => $q->where('code', 'S1'))->first();
        $pv = $this->pv($promo, StatutPv::Valide, $agent);
        $this->ligne($pv, 'R1', 'rattrapage');

        $resume = $service->resume($annee);
        $this->assertSame(1, $resume['complets']);
        $this->assertSame(47, $resume['manquants']);
        $this->assertSame(1, $resume['rattrapages_manquants']); // rattrapage attendu mais absent

        // périmètre d'un chef : 6 promotions pour IG
        $igId = Filiere::where('code', 'IG')->value('id');
        $this->assertSame(6, $service->resume($annee, $igId)['attendus']);
    }

    // ---------- utilisateurs ----------
    public function test_creation_de_comptes_par_l_admin(): void
    {
        $admin = $this->user(Role::Admin);
        $ig = Filiere::where('code', 'IG')->value('id');
        $base = ['nom' => 'A', 'prenoms' => 'B'];

        // un chef doit avoir une filière
        $this->actingAs($admin)->postJson('/admin/utilisateurs', $base + ['email' => 'c1@t.test', 'role' => 'chef'])
            ->assertStatus(422)->assertJsonValidationErrors('filiere_id');

        $r = $this->actingAs($admin)->postJson('/admin/utilisateurs', $base + ['email' => 'c1@t.test', 'role' => 'chef', 'filiere_id' => $ig])
            ->assertCreated();
        $this->assertNotEmpty($r->json('data.mot_de_passe_temporaire'));

        // un seul chef actif par filière
        $this->actingAs($admin)->postJson('/admin/utilisateurs', $base + ['email' => 'c2@t.test', 'role' => 'chef', 'filiere_id' => $ig])
            ->assertStatus(422)->assertJsonValidationErrors('filiere_id');

        // un agent n'a pas de filière
        $this->actingAs($admin)->postJson('/admin/utilisateurs', $base + ['email' => 'a1@t.test', 'role' => 'scolarite', 'filiere_id' => $ig])
            ->assertStatus(422);

        // seul l'admin gère les comptes
        $this->actingAs($this->user(Role::Scolarite))->getJson('/admin/utilisateurs')
            ->assertForbidden()
            ->assertJson(['message' => 'Accès refusé']);
        $chef = User::where('email', 'c1@t.test')->firstOrFail();
        $chef->update(['must_change_password' => false]);

        $this->actingAs($chef)->getJson('/admin/utilisateurs')
            ->assertForbidden()
            ->assertJson(['message' => 'Accès refusé']);
    }

    public function test_l_admin_ne_peut_pas_se_desactiver(): void
    {
        $admin = $this->user(Role::Admin);

        $this->actingAs($admin)->patchJson("/admin/utilisateurs/{$admin->id}/activation")->assertStatus(422);
    }

    // ---------- import ----------
    public function test_import_csv_tout_ou_rien_puis_valides_seulement(): void
    {
        $agent = $this->user(Role::Scolarite);
        $pv = $this->pv($this->promotion('IG'), StatutPv::Brouillon, $agent);

        $csv = "matricule,nom,prenoms,moyenne_generale,decision\nA1,DOE,John,12.5,admis\nA2,ROE,Jane,25,admis\n";
        $envoyer = fn (array $extra = []) => $this->actingAs($agent)->withHeaders(['Accept' => 'application/json'])
            ->post("/pv/{$pv->id}/resultats/import", ['fichier' => UploadedFile::fake()->createWithContent('res.csv', $csv)] + $extra);

        $r = $envoyer()->assertStatus(422);
        $this->assertSame(0, $pv->lignes()->count());
        $this->assertSame(3, $r->json('rapport.erreurs.0.ligne'));

        $envoyer(['mode' => 'valides_seulement'])->assertOk();
        $this->assertSame(1, $pv->lignes()->count());
    }
}
