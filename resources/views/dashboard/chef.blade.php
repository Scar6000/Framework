@extends('layouts.admin')

@section('title', 'Espace chef de département')

@section('page-title', 'Espace chef de département')
@section('page-subtitle', 'Suivi et validation des procès-verbaux de votre filière')

@section('content')
    @php
        $utilisateur = auth()->user();
        $prenom = collect(preg_split('/\s+/', trim($utilisateur?->prenoms ?? '')) ?: [])->first() ?: 'Chef';
        $nomFiliere = $filiere?->nom ?? $utilisateur?->filiere?->nom ?? 'votre filière';
        $codeFiliere = $filiere?->code ?? $utilisateur?->filiere?->code;
        $aujourdhui = \Illuminate\Support\Carbon::now()->locale('fr')->translatedFormat('l d F Y');
        $resume = $resume_pv ?? [
            'attendus' => 0,
            'complets' => 0,
            'en_cours' => 0,
            'manquants' => 0,
            'en_retard' => 0,
            'taux_numerisation' => 0,
        ];

        $cartons = [
            ['label' => 'PV à valider', 'value' => (string) $pv_a_valider->count(), 'meta' => 'Soumis à votre attention', 'icon' => 'clock', 'tone' => 'gold'],
            ['label' => 'PV numérisés', 'value' => $resume['complets'].' / '.$resume['attendus'], 'meta' => $resume['taux_numerisation'].' % de couverture', 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'PV manquants', 'value' => (string) $resume['manquants'], 'meta' => $resume['en_retard'].' en retard', 'icon' => 'alert', 'tone' => 'gold'],
            ['label' => 'PV validés', 'value' => (string) $resume['complets'], 'meta' => 'Année '.$annee_en_cours?->libelle, 'icon' => 'shield', 'tone' => 'blue'],
            ['label' => 'Promotions suivies', 'value' => (string) $resume['attendus'], 'meta' => 'Filière '.$nomFiliere, 'icon' => 'graduation', 'tone' => 'blue'],
        ];
    @endphp

    <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
        <span>Espace chef de département</span>
        <x-admin.icon name="chevron-right" />
        <span>Tableau de bord</span>
    </nav>

    <section class="admin-banner" aria-labelledby="chef-banner-title">
        <span class="admin-banner__surface" aria-hidden="true"></span>

        <div class="admin-banner__content">
            <span class="admin-banner__eyebrow">
                <x-admin.icon name="shield" />
                Chef de département @if ($codeFiliere) · {{ $codeFiliere }} @endif
            </span>
            <h1 class="admin-banner__title" id="chef-banner-title">Bonjour {{ $prenom }}, bienvenue dans votre espace.</h1>
            <p class="admin-banner__text">
                Nous sommes le {{ $aujourdhui }}. Vous supervisez les procès-verbaux de la filière
                <strong>{{ $nomFiliere }}</strong> pour l'année académique {{ $annee_en_cours?->libelle ?? 'en cours' }}.
            </p>
            <a class="admin-button admin-button--gold admin-button--small" href="{{ route('pv.index', ['statut' => 'soumis']) }}">
                Examiner les PV soumis
                <x-admin.icon name="arrow-right" />
            </a>
        </div>

        <svg class="admin-banner__art" viewBox="0 0 300 190" role="img" aria-label="Illustration : validation sécurisée des procès-verbaux">
            <defs>
                <linearGradient id="chef-banner-paper" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#FFFFFF"></stop>
                    <stop offset="100%" stop-color="#E8EFFF"></stop>
                </linearGradient>
            </defs>
            <ellipse cx="168" cy="177" rx="112" ry="10" fill="#08236B" opacity="0.16"></ellipse>
            <path d="M214 28 C238 34 250 58 242 80 C236 97 216 103 206 119 C198 100 200 78 208 62 C215 48 216 38 214 28 Z" fill="#DCE7FF" opacity="0.75"></path>
            <path d="M211 34 C205 62 205 84 214 105" fill="none" stroke="#FFFFFF" stroke-width="2" opacity="0.7"></path>
            <g transform="translate(52 44)">
                <rect x="0" y="0" width="118" height="86" rx="10" fill="url(#chef-banner-paper)" stroke="#DCE7FF" stroke-width="2"></rect>
                <rect x="12" y="13" width="48" height="7" rx="3.5" fill="#0B3394"></rect>
                <rect x="12" y="28" width="92" height="4" rx="2" fill="#DCE7FF"></rect>
                <rect x="12" y="39" width="72" height="4" rx="2" fill="#DCE7FF"></rect>
                <path d="M12 54H102M12 66H102" stroke="#DCE7FF" stroke-width="3" stroke-linecap="round"></path>
                <circle cx="20" cy="61" r="3" fill="#2F6BFF"></circle>
                <circle cx="20" cy="73" r="3" fill="#F5B91E"></circle>
            </g>
            <g transform="translate(142 92)">
                <rect x="0" y="0" width="72" height="54" rx="7" fill="#08236B"></rect>
                <rect x="6" y="7" width="60" height="40" rx="4" fill="#E8EFFF"></rect>
                <path d="M14 18H38M14 27H54M14 36H44" stroke="#2F6BFF" stroke-width="3" stroke-linecap="round"></path>
                <circle cx="52" cy="18" r="4" fill="#F5B91E"></circle>
            </g>
            <g transform="translate(112 48)">
                <circle cx="28" cy="18" r="17" fill="#0B3394"></circle>
                <path d="M6 72 C6 49 16 39 28 39 C40 39 50 49 50 72" fill="#2F6BFF"></path>
                <path d="M8 58 -6 74M48 58 66 76" stroke="#0B3394" stroke-width="9" stroke-linecap="round"></path>
            </g>
            <g transform="translate(24 106)">
                <path d="M0 -22 L19 -14 V3 C19 16 11 24 0 29 C-11 24 -19 16 -19 3 V-14 Z" fill="#F5B91E"></path>
                <path d="m-8 3 6 6 11-12" fill="none" stroke="#08236B" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
            <circle cx="278" cy="120" r="7" fill="#FFD45C" opacity="0.9"></circle>
            <circle cx="30" cy="42" r="5" fill="#FFD45C" opacity="0.85"></circle>
            <path d="M266 56 L270 66 L280 70 L270 74 L266 84 L262 74 L252 70 L262 66 Z" fill="#FFD45C" opacity="0.9"></path>
        </svg>
    </section>

    <section class="admin-section" aria-labelledby="chef-indicators-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-indicators-title">Indicateurs de votre filière</h2>
                <p class="admin-section__hint">Situation de la numérisation @if ($codeFiliere) — {{ $codeFiliere }} @endif.</p>
            </div>
            <div class="admin-section__actions">
                <a class="admin-button admin-button--small" href="{{ route('statistiques.index') }}">Voir les statistiques</a>
            </div>
        </div>

        <div class="admin-stat-grid">
            @foreach ($cartons as $carton)
                <article class="admin-stat-card">
                    <span class="admin-stat-card__tile{{ $carton['tone'] === 'gold' ? ' admin-stat-card__tile--gold' : '' }}">
                        <x-admin.icon name="{{ $carton['icon'] }}" />
                    </span>
                    <div>
                        <span class="admin-stat-card__value">{{ $carton['value'] }}</span>
                        <span class="admin-stat-card__label">{{ $carton['label'] }}</span>
                    </div>
                    <span class="admin-stat-card__meta">{{ $carton['meta'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-validation-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-validation-title">PV en attente de validation</h2>
                <p class="admin-section__hint">Les procès-verbaux soumis dans votre filière et non encore traités.</p>
            </div>
            <div class="admin-section__actions">
                <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('pv.index') }}">Registre complet</a>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>PV soumis à validation dans la filière {{ $nomFiliere }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col">Session</th>
                            <th scope="col">Date de délibération</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pv_a_valider as $pv)
                            <tr>
                                <td><a class="admin-table__link" href="{{ route('pv.show', $pv) }}">{{ $pv->reference }}</a></td>
                                <td>
                                    <span class="admin-table__primary">{{ $pv->promotion?->semestre?->niveau?->code ?? '—' }} {{ $pv->promotion?->semestre?->code }}</span>
                                    <span class="admin-table__subtext">{{ $nomFiliere }}</span>
                                </td>
                                <td>{{ $pv->session?->label() }}</td>
                                <td>{{ $pv->date_deliberation?->format('d/m/Y') ?? '—' }}</td>
                                <td><x-admin.status :label="$pv->statut?->label()" tone="gold" /></td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="admin-button admin-button--small" href="{{ route('pv.show', $pv) }}">
                                            <x-admin.icon name="eye" /> Examiner
                                        </a>
                                        <form method="POST" action="{{ route('pv.valider', $pv) }}">
                                            @csrf
                                            <button class="admin-button admin-button--gold admin-button--small" type="submit">
                                                <x-admin.icon name="check" /> Valider
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <span class="admin-empty">Aucun PV en attente de validation dans votre filière.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-suivi-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-suivi-title">Suivi de la numérisation</h2>
                <p class="admin-section__hint">Promotions à régulariser pour l'année {{ $annee_en_cours?->libelle ?? 'en cours' }}.</p>
            </div>
            <div class="admin-section__actions">
                <a class="admin-button admin-button--small" href="{{ route('pv-manquants.index') }}">Voir le plan complet</a>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-progress-list">
                @forelse ($promotions_a_signaler as $etat)
                    <div class="admin-progress-item">
                        <span class="admin-progress-item__initials">
                            {{ $etat['promotion']->filiere->code ?? 'PV' }}
                        </span>
                        <div class="admin-progress-item__text">
                            <span class="admin-progress-item__name">
                                {{ $etat['promotion']->filiere->nom }} — {{ $etat['promotion']->semestre->niveau->code }} {{ $etat['promotion']->semestre->code }}
                            </span>
                            <span class="admin-progress-item__meta">
                                {{ $etat['etat_label'] }} @if ($etat['en_retard']) · En retard @endif
                                @if ($etat['pv_normale']) · {{ $etat['pv_normale']->reference }} @endif
                            </span>
                            <x-admin.progress :value="$etat['etat'] === 'complet' ? 100 : ($etat['etat'] === 'en_cours' ? 60 : 15)" :label="$etat['etat_label']" :tone="$etat['en_retard'] ? 'gold' : 'blue'" />
                        </div>
                        <span class="admin-progress-item__value">{{ $etat['etat_label'] }}</span>
                    </div>
                @empty
                    <span class="admin-empty">Toutes les promotions attendues de votre filière disposent d'un PV.</span>
                @endforelse
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-reussite-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-reussite-title">Taux de réussite</h2>
                <p class="admin-section__hint">Sessions normales des PV officiels de votre filière.</p>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-progress-list">
                @forelse ($taux_reussite as $row)
                    <div class="admin-progress-item">
                        <span class="admin-progress-item__initials">{{ $row['filiere'] }}</span>
                        <div class="admin-progress-item__text">
                            <span class="admin-progress-item__name">{{ $row['annee'] }} — {{ $row['filiere'] }}</span>
                            <span class="admin-progress-item__meta">{{ $row['admis'] }} admis sur {{ $row['total'] }} résultats</span>
                            <x-admin.progress :value="$row['taux_reussite']" :label="'Taux de réussite '.$row['annee']" tone="gold" />
                        </div>
                        <span class="admin-progress-item__value">{{ $row['taux_reussite'] }} %</span>
                    </div>
                @empty
                    <span class="admin-empty">Aucun résultat officiel disponible pour votre filière.</span>
                @endforelse
            </div>
        </div>
    </section>
@endsection
