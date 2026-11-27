<?php

<<<<<<< HEAD
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PvController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::redirect('/login', '/connexion');
Route::get('/connexion', [LoginController::class, 'create'])->name('login');
Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');

Route::get('/acces-refuse', fn () => view('errors.403'))->name('acces-refuse');

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');
    Route::put('/mon-profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profil.password.update');
    Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');

    // Tableau de bord propre à chaque rôle.
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', DashboardController::class)->name('admin.dashboard');
        Route::view('/admin/utilisateurs', 'admin.users.index')->name('admin.users.index');
        Route::view('/admin/roles-permissions', 'admin.roles.index')->name('admin.roles.index');
        Route::view('/admin/referentiels', 'admin.referentiels.index')->name('admin.referentiels.index');
        Route::view('/admin/plan-numerisation', 'admin.plan.index')->name('admin.plan.index');
        Route::get('/admin/recherche', [ConsultationController::class, 'index'])->name('admin.recherche.index');
        Route::get('/admin/etudiants/{matricule}', [ConsultationController::class, 'show'])->name('admin.etudiants.show');
        Route::view('/admin/journal-audit', 'admin.journal.index')->name('admin.journal.index');
        Route::view('/admin/exports-partages', 'admin.exports.index')->name('admin.exports.index');
        Route::view('/admin/parametres', 'admin.parametres.index')->name('admin.parametres.index');
        Route::view('/admin/profil', 'shared.profil')->name('admin.profil');
    });

    Route::middleware('role:scolarite')->group(function () {
        Route::get('/agent', DashboardController::class)->name('agent.dashboard');
        Route::view('/agent/profil', 'shared.profil')->name('agent.profil');
    });

    Route::middleware('role:chef')->group(function () {
        Route::get('/chef', DashboardController::class)->name('chef.dashboard');
        Route::view('/chef/profil', 'shared.profil')->name('chef.profil');
    });

    // Registre : lecture pour tous, mais périmètre départemental pour le chef.
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/pv', [PvController::class, 'index'])->name('admin.pv.index');
    });
    Route::middleware('role:scolarite')->group(function () {
        Route::get('/agent/pv', [PvController::class, 'index'])->name('agent.pv.index');
    });
    Route::middleware('role:chef')->group(function () {
        Route::get('/chef/pv', [PvController::class, 'index'])->name('chef.pv.index');
    });

    // Écriture sur les PV : administrateur et agent uniquement.
    Route::middleware('role:admin,scolarite')->group(function () {
        Route::get('/admin/pv/nouveau', [PvController::class, 'create'])->name('admin.pv.create');
        Route::post('/admin/pv', [PvController::class, 'store'])->name('admin.pv.store');
        Route::get('/admin/pv/{pv:reference}/modifier', [PvController::class, 'edit'])->name('admin.pv.edit');
        Route::put('/admin/pv/{pv:reference}', [PvController::class, 'update'])->name('admin.pv.update');
        Route::get('/admin/pv/import-en-masse', [PvController::class, 'import'])->name('admin.pv.import');

        Route::get('/agent/pv/nouveau', [PvController::class, 'create'])->name('agent.pv.create');
        Route::post('/agent/pv', [PvController::class, 'store'])->name('agent.pv.store');
        Route::get('/agent/pv/{pv:reference}/modifier', [PvController::class, 'edit'])->name('agent.pv.edit');
        Route::put('/agent/pv/{pv:reference}', [PvController::class, 'update'])->name('agent.pv.update');
        Route::get('/agent/pv/import-en-masse', [PvController::class, 'import'])->name('agent.pv.import');
    });

    // Recherche et fiches étudiant : lecture seule pour le chef, périmétrée.
    Route::middleware('role:scolarite')->group(function () {
        Route::get('/agent/recherche', [ConsultationController::class, 'index'])->name('agent.recherche.index');
        Route::get('/agent/etudiants/{matricule}', [ConsultationController::class, 'show'])->name('agent.etudiants.show');
    });
    Route::middleware('role:chef')->group(function () {
        Route::get('/chef/recherche', [ConsultationController::class, 'index'])->name('chef.recherche.index');
        Route::get('/chef/etudiants/{matricule}', [ConsultationController::class, 'show'])->name('chef.etudiants.show');
    });
});
=======
use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PartageController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PvController;
use App\Http\Controllers\PvFileController;
use App\Http\Controllers\PvManquantController;
use App\Http\Controllers\PvWorkflowController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Lien de partage signé (public, protégé par la signature)
Route::get('/partage/{pv}', [PartageController::class, 'show'])->middleware('signed')->name('partage.show');

// ---------- Invités ----------
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
});

// ---------- Connectés ----------
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');

    // Changement de mot de passe (accessible même avec un mot de passe temporaire)
    Route::get('/mot-de-passe', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('/mot-de-passe', [PasswordController::class, 'update'])->name('password.change.update');

    Route::middleware(['actif', 'mdp.change'])->group(function () {
        Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');

        Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/tout-lire', [NotificationController::class, 'toutLire'])->name('notifications.read-all');
        Route::post('/notifications/{id}/lire', [NotificationController::class, 'lire'])->name('notifications.read');

        // Consultation (tous les rôles ; le chef est limité à sa filière)
        Route::get('/recherche', [RechercheController::class, 'index'])->name('recherche.index');
        Route::get('/pv-manquants', [PvManquantController::class, 'index'])->name('pv-manquants.index');
        Route::get('/etudiants', [EtudiantController::class, 'index'])->name('etudiants.index');
        Route::get('/etudiants/{etudiant}', [EtudiantController::class, 'show'])->name('etudiants.show');

        // ----- PV : création / modification (admin + scolarité) — AVANT /pv/{pv} -----
        Route::middleware('role:admin,scolarite')->group(function () {
            Route::get('/pv/creer', [PvController::class, 'create'])->name('pv.create');
            Route::post('/pv', [PvController::class, 'store'])->name('pv.store');
            Route::get('/pv/{pv}/modifier', [PvController::class, 'edit'])->name('pv.edit');
            Route::put('/pv/{pv}', [PvController::class, 'update'])->name('pv.update');

            Route::post('/pv/{pv}/soumettre', [PvWorkflowController::class, 'soumettre'])->name('pv.soumettre');

            Route::post('/pv/{pv}/resultats', [ResultatController::class, 'store'])->name('resultats.store');
            Route::put('/pv/{pv}/resultats/{ligne}', [ResultatController::class, 'update'])->name('resultats.update');
            Route::delete('/pv/{pv}/resultats/{ligne}', [ResultatController::class, 'destroy'])->name('resultats.destroy');
            Route::post('/pv/{pv}/resultats/import', [ResultatController::class, 'import'])->name('resultats.import');
            Route::get('/resultats/modele', [ResultatController::class, 'modele'])->name('resultats.modele');
        });

        // ----- PV : consultation (tous) -----
        Route::get('/pv', [PvController::class, 'index'])->name('pv.index');
        Route::get('/pv/{pv}', [PvController::class, 'show'])->name('pv.show');
        Route::get('/pv/{pv}/scan', [PvFileController::class, 'show'])->name('pv.scan');
        Route::get('/pv/{pv}/telecharger', [PvFileController::class, 'telecharger'])->name('pv.telecharger');

        // ----- Validation / partage / stats / exports (admin + chef) -----
        Route::middleware('role:admin,chef')->group(function () {
            Route::post('/pv/{pv}/valider', [PvWorkflowController::class, 'valider'])->name('pv.valider');
            Route::post('/pv/{pv}/rejeter', [PvWorkflowController::class, 'rejeter'])->name('pv.rejeter');
            Route::post('/pv/{pv}/partage', [PartageController::class, 'creer'])->name('pv.partage');

            Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

            Route::get('/exports/pv', [ExportController::class, 'pv'])->name('exports.pv');
            Route::get('/exports/pv/{pv}/resultats', [ExportController::class, 'resultats'])->name('exports.resultats');
            Route::get('/exports/statistiques', [ExportController::class, 'statistiques'])->name('exports.statistiques');
            Route::get('/exports/pv-manquants', [ExportController::class, 'pvManquants'])->name('exports.pv-manquants');
        });

        // ----- Administration (admin uniquement) -----
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::post('/pv/{pv}/archiver', [PvWorkflowController::class, 'archiver'])->name('pv.archiver');
            Route::delete('/pv/{pv}', [PvController::class, 'destroy'])->name('pv.destroy');

            // Utilisateurs
            Route::get('/utilisateurs', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/utilisateurs/creer', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/utilisateurs', [UserController::class, 'store'])->name('admin.users.store');
            Route::get('/utilisateurs/{user}', [UserController::class, 'show'])->name('admin.users.show');
            Route::get('/utilisateurs/{user}/modifier', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::patch('/utilisateurs/{user}/activation', [UserController::class, 'activation'])->name('admin.users.activation');
            Route::post('/utilisateurs/{user}/reinitialiser-mot-de-passe', [UserController::class, 'reinitialiserMotDePasse'])->name('admin.users.reset-password');

            // Référentiels (1 page, 5 onglets)
            Route::get('/referentiels', [ReferentielController::class, 'index'])->name('admin.referentiels.index');
            Route::post('/referentiels/{type}', [ReferentielController::class, 'store'])->name('admin.referentiels.store');
            Route::put('/referentiels/{type}/{id}', [ReferentielController::class, 'update'])->name('admin.referentiels.update');
            Route::delete('/referentiels/{type}/{id}', [ReferentielController::class, 'destroy'])->name('admin.referentiels.destroy');

            // Années, promotions
            Route::patch('/annees/{annee}/en-cours', [AnneeAcademiqueController::class, 'enCours'])->name('admin.annees.en-cours');
            Route::post('/annees/{annee}/generer-promotions', [AnneeAcademiqueController::class, 'genererPromotions'])->name('admin.annees.generer-promotions');
            Route::get('/promotions', [PromotionController::class, 'index'])->name('admin.promotions.index');
            Route::patch('/promotions/{promotion}', [PromotionController::class, 'update'])->name('admin.promotions.update');

            // Journal (lecture seule)
            Route::get('/journal', [JournalController::class, 'index'])->name('admin.journal.index');
            Route::get('/journal/{log}', [JournalController::class, 'show'])->name('admin.journal.show');
        });
    });
});

// TODO: route d'aperçu temporaire de la page de connexion ; à remplacer par les routes d'authentification.
Route::view('/login', 'auth.login');

/*
 * TODO: routes d'aperçu temporaires de l'espace administrateur.
 * À remplacer par les routes contrôleurs lors du branchement du back-end.
 */
Route::middleware(['auth', 'actif', 'mdp.change', 'role:admin'])->group(function () {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/admin/roles-permissions', 'admin.roles.index')->name('admin.roles.index');
    Route::view('/admin/plan-numerisation', 'admin.plan.index')->name('admin.plan.index');
    Route::view('/admin/pv', 'admin.pv.index')->name('admin.pv.index');
    Route::view('/admin/recherche', 'admin.recherche.index')->name('admin.recherche.index');
    Route::view('/admin/etudiants/{matricule}', 'admin.etudiants.show')->name('admin.etudiants.show');
    Route::view('/admin/journal-audit', 'admin.journal.index')->name('admin.journal.audit');
    Route::view('/admin/exports-partages', 'admin.exports.index')->name('admin.exports.index');
    Route::view('/admin/parametres', 'admin.parametres.index')->name('admin.parametres.index');
});
>>>>>>> main
