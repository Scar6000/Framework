<?php

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
