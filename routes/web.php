<?php

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
