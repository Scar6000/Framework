<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B3394">
    <title>Extrait PV {{ $pv->reference }} — ENEAM</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <main class="admin-content" id="admin-content" tabindex="-1">
            <x-admin.page-header title="Extrait de procès-verbal" subtitle="Lien de partage temporaire et sécurisé." :breadcrumb="['ENEAM', 'PV', $pv->reference]" />

            <section class="admin-section" aria-labelledby="partage-title">
                <div class="admin-card">
                    <div class="admin-card__head">
                        <h2 class="admin-card__title" id="partage-title">{{ $pv->reference }}</h2>
                        <x-admin.status label="PV officiel" tone="blue" />
                    </div>
                    <div class="admin-card__body">
                        <dl class="admin-info-list">
                            <div class="admin-info-row">
                                <dt>Étudiant</dt>
                                <dd>{{ $ligne->etudiant?->nom }} {{ $ligne->etudiant?->prenoms }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Matricule</dt>
                                <dd>{{ $ligne->etudiant?->matricule ?? '—' }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Moyenne générale</dt>
                                <dd>{{ $ligne->moyenne_generale !== null ? number_format((float) $ligne->moyenne_generale, 2, ',', ' ').' / 20' : '—' }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Décision</dt>
                                <dd><x-admin.status :label="$ligne->decision?->label()" :tone="$ligne->decision?->value === 'admis' ? 'blue' : 'gold'" /></dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Promotion</dt>
                                <dd>{{ $pv->promotion?->filiere?->nom }} — {{ $pv->promotion?->semestre?->niveau?->code }} {{ $pv->promotion?->semestre?->code }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Année académique</dt>
                                <dd>{{ $pv->promotion?->annee?->libelle }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>
