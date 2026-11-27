@php
    $resultKey = $resultKey ?? 'resultats';
    $roleKey = $roleKey ?? 'admin';
    $pvReference = $pv['reference'] ?? '';
    $resultats = $resultats ?? [];
    $decisionTones = [
        'Admis' => 'blue',
        'Rattrapage' => 'gold',
        'Ajourné' => 'red',
    ];
@endphp

<div class="ui-form-stack" data-pv-result-scope="{{ $resultKey }}">
    <div class="ui-notice">
        <x-admin.icon name="info" />
        <span>Les résultats détaillés de ce PV ne sont pas encore raccordés à cette consultation.</span>
    </div>

    <div class="admin-card admin-card--flush">
        <div class="admin-card__head">
            <h3 class="admin-card__title">Résultats du PV {{ $pvReference }}</h3>
        </div>

        <div class="admin-card__body ui-form-stack">
            <div class="admin-filters">
                <div class="admin-field admin-field--grow">
                    <label class="admin-field__label" for="pv-results-search-{{ $resultKey }}">Recherche rapide</label>
                    <input class="admin-input" id="pv-results-search-{{ $resultKey }}" type="search"
                           placeholder="Matricule ou nom" data-pv-result-search
                           aria-label="Rechercher un résultat dans le PV">
                </div>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Résultats de délibération du PV {{ $pvReference }}</caption>
                    <colgroup>
                        <col class="col-ref">
                        <col>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="col-ref">Matricule</th>
                            <th scope="col">Nom et prénoms</th>
                            <th scope="col">Moyenne annuelle</th>
                            <th scope="col">Crédits obtenus</th>
                            <th scope="col">Décision</th>
                        </tr>
                    </thead>
                    <tbody data-pv-result-body>
                        @forelse ($resultats as $resultat)
                            <tr data-pv-result-row
                                data-pv-result-text="{{ $resultat['matricule'].' '.$resultat['nom'].' '.$resultat['decision'] }}">
                                <td title="{{ $resultat['matricule'] }}">{{ $resultat['matricule'] }}</td>
                                <td title="{{ $resultat['nom'] }}">{{ $resultat['nom'] }}</td>
                                <td>{{ $resultat['moyenne'] }}</td>
                                <td>{{ $resultat['credits'] }}</td>
                                <td>
                                    <x-ui.badge :label="$resultat['decision']"
                                                :tone="$decisionTones[$resultat['decision']] ?? 'neutral'" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="admin-muted">Aucune ligne de résultat disponible pour ce PV.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts', 'shared-pv-results')
    <script>
        (function () {
            document.querySelectorAll('[data-pv-result-scope]').forEach(function (scope) {
                var search = scope.querySelector('[data-pv-result-search]');
                var body = scope.querySelector('[data-pv-result-body]');

                if (!search || !body) {
                    return;
                }

                search.addEventListener('input', function () {
                    var term = search.value.trim().toLowerCase();

                    body.querySelectorAll('[data-pv-result-row]').forEach(function (row) {
                        var text = (row.getAttribute('data-pv-result-text') || '').toLowerCase();
                        row.hidden = term !== '' && text.indexOf(term) === -1;
                    });
                });
            });
        })();
    </script>
@endpushOnce
