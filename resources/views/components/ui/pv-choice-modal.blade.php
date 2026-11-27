@php
    $filieres = collect($filieres ?? []);
    $scopeFilieres = $scopeFilieres ?? null;

    if (is_array($scopeFilieres) && $scopeFilieres !== []) {
        $allowedIds = $filieres->whereIn('code', $scopeFilieres)->pluck('id');
    } else {
        $allowedIds = null;
    }

    $promotions = collect($promotionsChoisissables ?? [])
        ->when($allowedIds !== null, fn ($items) => $items->whereIn('filiere_id', $allowedIds))
        ->values();

    $createRoute = \Illuminate\Support\Facades\Route::has(($roleKey ?? 'admin').'.pv.create')
        ? route(($roleKey ?? 'admin').'.pv.create')
        : route('admin.pv.create');
@endphp

<div class="admin-modal" id="pv-choice-modal" hidden role="dialog" aria-modal="true"
     aria-labelledby="pv-choice-title" aria-describedby="pv-choice-help">
    <div class="admin-modal__head">
        <h2 class="admin-modal__title" id="pv-choice-title">Nouveau procès-verbal</h2>
        <p class="admin-tiny admin-muted">Étape 1 sur 2 — choisissez la promotion concernée.</p>
    </div>

    <div class="admin-modal__body">
        <p class="admin-tiny admin-muted" id="pv-choice-help">
            Le PV est toujours annuel : sélectionnez la filière, puis l'année et le niveau. La référence sera attribuée automatiquement.
        </p>

        <div class="admin-form-grid">
            <div class="admin-field">
                <label class="admin-field__label" for="pv-choice-filiere">Filière</label>
                <select class="admin-select" id="pv-choice-filiere" data-pv-choice-filiere>
                    <option value="">Choisir une filière</option>
                    @foreach ($filieres as $filiere)
                        @if ($allowedIds === null || $allowedIds->contains((int) $filiere['id']))
                            <option value="{{ $filiere['id'] }}">{{ $filiere['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="pv-choice-promotion">Année et niveau</label>
                <select class="admin-select" id="pv-choice-promotion" data-pv-choice-promotion disabled>
                    <option value="">Choisir d'abord une filière</option>
                </select>
            </div>
        </div>

        <p class="admin-tiny admin-muted" data-pv-choice-summary aria-live="polite">Sélection incomplète.</p>
    </div>

    <div class="admin-modal__footer">
        <x-ui.btn variant="secondary" type="button" data-overlay-close>Annuler</x-ui.btn>
        <x-ui.btn variant="primary" type="button" data-pv-choice-submit data-base-url="{{ $createRoute }}" disabled>
            Continuer
        </x-ui.btn>
    </div>
</div>

@pushOnce('scripts', 'pv-choice-modal')
    <script>
        (function () {
            var modal = document.getElementById('pv-choice-modal');

            if (!modal) {
                return;
            }

            var filiereSelect = modal.querySelector('[data-pv-choice-filiere]');
            var promotionSelect = modal.querySelector('[data-pv-choice-promotion]');
            var submit = modal.querySelector('[data-pv-choice-submit]');
            var summary = modal.querySelector('[data-pv-choice-summary]');
            var promotions = @json($promotions->all());

            function clearPromotions(placeholder) {
                promotionSelect.innerHTML = '';
                var option = document.createElement('option');
                option.value = '';
                option.textContent = placeholder;
                promotionSelect.appendChild(option);
                promotionSelect.disabled = true;
            }

            function refresh() {
                var filiereId = filiereSelect.value;
                var available = promotions.filter(function (item) {
                    return String(item.filiere_id) === filiereId;
                });

                clearPromotions(filiereId === '' ? 'Choisir d’abord une filière' : 'Choisir une promotion');

                available.forEach(function (item) {
                    var option = document.createElement('option');
                    option.value = String(item.id);
                    option.textContent = item.label;
                    promotionSelect.appendChild(option);
                });

                if (available.length > 0) {
                    promotionSelect.disabled = false;
                }

                var selected = promotionSelect.value;
                var promotion = available.find(function (item) {
                    return String(item.id) === selected;
                });

                submit.disabled = !promotion;
                summary.textContent = promotion
                    ? 'Promotion sélectionnée : ' + promotion.label
                    : 'Sélection incomplète.';
            }

            filiereSelect.addEventListener('change', refresh);
            promotionSelect.addEventListener('change', refresh);

            submit.addEventListener('click', function () {
                if (submit.disabled || !promotionSelect.value) {
                    return;
                }

                var url = submit.getAttribute('data-base-url')
                    + '?filiere_id=' + encodeURIComponent(filiereSelect.value)
                    + '&promotion_id=' + encodeURIComponent(promotionSelect.value);

                window.location.assign(url);
            });

            refresh();
        })();
    </script>
@endpushOnce
