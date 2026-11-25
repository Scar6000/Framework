<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TODO: route d'aperçu temporaire de la page de connexion ; à remplacer par les routes d'authentification.
Route::view('/login', 'auth.login');

/*
 * TODO: routes d'aperçu temporaires de l'espace administrateur.
 * À remplacer par les routes contrôleurs lors du branchement du back-end.
 */
Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
Route::view('/admin/utilisateurs', 'admin.users.index')->name('admin.users.index');
Route::view('/admin/roles-permissions', 'admin.roles.index')->name('admin.roles.index');
Route::view('/admin/referentiels', 'admin.referentiels.index')->name('admin.referentiels.index');
Route::view('/admin/plan-numerisation', 'admin.plan.index')->name('admin.plan.index');
Route::view('/admin/pv', 'admin.pv.index')->name('admin.pv.index');
Route::view('/admin/pv/nouveau', 'shared.pv.create', ['roleKey' => 'admin'])->name('admin.pv.create');
Route::view('/admin/pv/{reference}/modifier', 'shared.pv.edit', ['roleKey' => 'admin'])->name('admin.pv.edit');
Route::view('/admin/pv/import-en-masse', 'shared.pv.import', ['roleKey' => 'admin'])->name('admin.pv.import');
Route::view('/admin/recherche', 'admin.recherche.index')->name('admin.recherche.index');
Route::view('/admin/etudiants/{matricule}', 'admin.etudiants.show')->name('admin.etudiants.show');
Route::view('/admin/journal-audit', 'admin.journal.index')->name('admin.journal.index');
Route::view('/admin/exports-partages', 'admin.exports.index')->name('admin.exports.index');
Route::view('/admin/parametres', 'admin.parametres.index')->name('admin.parametres.index');
Route::view('/admin/profil', 'shared.profil', ['roleKey' => 'admin'])->name('admin.profil');

/*
 * TODO: aperçus de l'espace agent de scolarité ; à protéger par les rôles réels.
 * Les vues sont partagées avec l'administrateur, seul le menu change.
 */
Route::view('/agent', 'agent.dashboard', ['roleKey' => 'agent'])->name('agent.dashboard');
Route::view('/agent/pv', 'shared.pv.index', ['roleKey' => 'agent'])->name('agent.pv.index');
Route::view('/agent/pv/nouveau', 'shared.pv.create', ['roleKey' => 'agent'])->name('agent.pv.create');
Route::view('/agent/pv/{reference}/modifier', 'shared.pv.edit', ['roleKey' => 'agent'])->name('agent.pv.edit');
Route::view('/agent/pv/import-en-masse', 'shared.pv.import', ['roleKey' => 'agent'])->name('agent.pv.import');
Route::view('/agent/recherche', 'shared.recherche.index', ['roleKey' => 'agent'])->name('agent.recherche.index');
Route::view('/agent/etudiants/{matricule}', 'shared.recherche.etudiant', ['roleKey' => 'agent'])->name('agent.etudiants.show');
Route::view('/agent/profil', 'shared.profil', ['roleKey' => 'agent'])->name('agent.profil');

/*
 * TODO: aperçus de l'espace chef de département ; périmètre lecture seule.
 */
Route::view('/chef', 'chef.dashboard', ['roleKey' => 'chef'])->name('chef.dashboard');
Route::view('/chef/pv', 'shared.pv.index', ['roleKey' => 'chef'])->name('chef.pv.index');
Route::view('/chef/recherche', 'shared.recherche.index', ['roleKey' => 'chef'])->name('chef.recherche.index');
Route::view('/chef/etudiants/{matricule}', 'shared.recherche.etudiant', ['roleKey' => 'chef'])->name('chef.etudiants.show');
Route::view('/chef/profil', 'shared.profil', ['roleKey' => 'chef'])->name('chef.profil');

// TODO: à rendre via le middleware d'autorisation lors du branchement du back-end.
Route::view('/acces-refuse', 'errors.403')->name('acces-refuse');
