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
Route::view('/admin/recherche', 'admin.recherche.index')->name('admin.recherche.index');
Route::view('/admin/etudiants/{matricule}', 'admin.etudiants.show')->name('admin.etudiants.show');
Route::view('/admin/journal-audit', 'admin.journal.index')->name('admin.journal.index');
Route::view('/admin/exports-partages', 'admin.exports.index')->name('admin.exports.index');
Route::view('/admin/parametres', 'admin.parametres.index')->name('admin.parametres.index');
