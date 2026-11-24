# Back-end – Gestion des PV de délibération (ENEAM)

## 1. Installation (dans le projet Laravel du groupe)

```bash
# 1) copier le contenu de ce dossier PAR-DESSUS le projet (voir arborescence plus bas)
# 2) dépendances
composer require maatwebsite/excel barryvdh/laravel-dompdf
# 3) .env : base MySQL + (optionnel) ADMIN_EMAIL / ADMIN_PASSWORD
# 4) base de données
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoSeeder      # (optionnel) comptes et PV de démo, mot de passe « password »
# 5) tests
php artisan test
```

Premier admin : `admin@eneam.test` / `ChangeMe@2026` (changement de mot de passe obligatoire à la 1re connexion).

## 2. Fichiers fournis

```
bootstrap/app.php                  (REMPLACE l'existant : alias des middlewares)
config/pv.php
routes/web.php                     (REMPLACE l'existant)
app/Enums/*  app/Models/*  app/Models/Concerns/Auditable.php
app/Policies/PvPolicy.php          app/Support/Referentiels.php
app/Services/*  app/Imports/*  app/Exports/*  app/Notifications/*
app/Http/Controllers/*  app/Http/Middleware/*  app/Http/Requests/*
database/migrations/*  database/seeders/*
resources/views/exports/table.blade.php
tests/Feature/PvBackendTest.php
```

> `app/Models/User.php` et `app/Http/Controllers/Controller.php` remplacent ceux de Laravel.
> Les migrations de ce dossier **remplacent** celles du responsable base de données (mêmes tables que son PDF).
> Ne gardez pas deux versions des mêmes tables.

## 3. Principe : le back marche avant le front

Chaque contrôleur appelle `render('nom.de.vue', $données)` :
- si la vue Blade existe et que la requête n'est pas en JSON → la vue est affichée ;
- sinon → les mêmes données sont renvoyées en **JSON**.
Le front peut donc créer ses vues une par une ; en attendant, testez avec `Accept: application/json`.

## 4. Règles importantes
- 3 rôles : `admin`, `scolarite`, `chef` (pas d'enseignant). Comptes créés uniquement par l'admin.
- Un chef est lié à une filière : il ne voit que les PV **non brouillon** de sa filière (liste, détail, scan, recherche, étudiants, stats, exports).
- Scans stockés en privé et servis par `/pv/{pv}/scan` (droits vérifiés).
- Journal : création/modif/suppression automatiques (trait `Auditable`) + consultations, connexions, exports.
