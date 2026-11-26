<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#061B5E">
    <meta name="description" content="Connexion à l'application de gestion et de recherche des PV de délibération de l'ENEAM.">
    <title>Connexion — Gestion des PV de délibération | ENEAM</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">
    {{-- Décor de fond : formes organiques et bulles floues --}}
    <div class="auth-backdrop" aria-hidden="true">
        <span class="auth-backdrop__shape auth-backdrop__shape--one"></span>
        <span class="auth-backdrop__shape auth-backdrop__shape--two"></span>
        <span class="auth-backdrop__bubble auth-backdrop__bubble--one"></span>
        <span class="auth-backdrop__bubble auth-backdrop__bubble--two"></span>
        <span class="auth-backdrop__bubble auth-backdrop__bubble--three"></span>
        <span class="auth-backdrop__bubble auth-backdrop__bubble--four"></span>
        <span class="auth-backdrop__bubble auth-backdrop__bubble--five"></span>
    </div>

    <main class="auth-page">
        <section class="auth-card" aria-labelledby="auth-title">
            {{-- Séparation courbe en S entre le panneau clair et le panneau sombre --}}
            <svg class="auth-card__divider" viewBox="0 0 1200 800" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                <defs>
                    <linearGradient id="auth-panel-gradient" x1="1" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#0B3394"></stop>
                        <stop offset="100%" stop-color="#0A2A7A"></stop>
                    </linearGradient>
                </defs>
                <path class="auth-card__divider-fill" d="M 668 0 C 606 116 590 210 642 302 C 694 394 688 470 620 558 C 556 640 566 722 632 800 L 1200 800 L 1200 0 Z"></path>
                <path class="auth-card__divider-highlight" d="M 668 0 C 606 116 590 210 642 302 C 694 394 688 470 620 558 C 556 640 566 722 632 800"></path>
            </svg>

            {{-- Panneau clair : identité visuelle et illustration --}}
            <div class="auth-showcase">
                <div class="auth-brand">
                    <span class="auth-brand__badge">
                        <img class="auth-brand__logo" src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" width="112" height="112">
                    </span>
                    <div class="auth-brand__text">
                        <span class="auth-brand__name">ENEAM</span>
                        <span class="auth-brand__tagline">École Nationale d'Économie Appliquée et de Management</span>
                    </div>
                </div>

                <div class="auth-illustration">
                    <span class="auth-illustration__glow" aria-hidden="true"></span>
                    <span class="auth-illustration__bubble auth-illustration__bubble--one" aria-hidden="true"></span>
                    <span class="auth-illustration__bubble auth-illustration__bubble--two" aria-hidden="true"></span>
                    <span class="auth-illustration__bubble auth-illustration__bubble--three" aria-hidden="true"></span>
                    <span class="auth-illustration__bubble auth-illustration__bubble--four" aria-hidden="true"></span>

                    <svg class="auth-illustration__art" viewBox="0 0 520 420" role="img" aria-label="Illustration : PV numérisés, archivés et sécurisés">
                        <defs>
                            <linearGradient id="auth-paper" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FFFFFF"></stop>
                                <stop offset="100%" stop-color="#EEF3FF"></stop>
                            </linearGradient>
                            <filter id="auth-soft-shadow" x="-30%" y="-30%" width="160%" height="170%">
                                <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#A9C3FF" flood-opacity="0.16"></feDropShadow>
                            </filter>
                        </defs>

                        {{-- Ombre portée au sol --}}
                        <ellipse cx="260" cy="386" rx="168" ry="20" fill="#A9C3FF" opacity="0.07"></ellipse>

                        {{-- Feuille arrière --}}
                        <g transform="rotate(6 268 180)" filter="url(#auth-soft-shadow)">
                            <rect x="160" y="92" width="216" height="182" rx="16" fill="#EEF3FF" stroke="#DCE7FF" stroke-width="2"></rect>
                            <rect x="182" y="122" width="110" height="9" rx="4.5" fill="#A9C3FF"></rect>
                            <rect x="182" y="146" width="164" height="6" rx="3" fill="#DCE7FF"></rect>
                            <rect x="182" y="164" width="140" height="6" rx="3" fill="#DCE7FF"></rect>
                        </g>

                        {{-- Feuille avant : fiche de résultats stylisée --}}
                        <g transform="rotate(-4 260 176)" filter="url(#auth-soft-shadow)">
                            <rect x="142" y="72" width="232" height="204" rx="18" fill="url(#auth-paper)" stroke="#A9C3FF" stroke-width="2"></rect>
                            <rect x="164" y="98" width="104" height="11" rx="5.5" fill="#0B3394"></rect>
                            <rect x="164" y="122" width="188" height="7" rx="3.5" fill="#DCE7FF"></rect>
                            <rect x="164" y="150" width="54" height="6" rx="3" fill="#A9C3FF"></rect>
                            <rect x="232" y="150" width="54" height="6" rx="3" fill="#A9C3FF"></rect>
                            <rect x="300" y="150" width="52" height="6" rx="3" fill="#A9C3FF"></rect>
                            <path d="M164 170H352M164 196H352M164 222H352M164 248H352" stroke="#DCE7FF" stroke-width="3" stroke-linecap="round"></path>
                            <path d="M282 150V254" stroke="#DCE7FF" stroke-width="3" stroke-linecap="round"></path>
                            <circle cx="180" cy="183" r="4" fill="#2F6BFF"></circle>
                            <circle cx="250" cy="209" r="4" fill="#2F6BFF"></circle>
                            <circle cx="316" cy="235" r="4" fill="#2F6BFF"></circle>
                        </g>

                        {{-- Cachet de validation --}}
                        <g transform="translate(390 120)" filter="url(#auth-soft-shadow)">
                            <circle r="43" fill="#F5B91E"></circle>
                            <circle r="34" fill="none" stroke="#FFFFFF" stroke-width="2" opacity="0.55"></circle>
                            <path d="M-16 1 L-5 12 L17 -11" fill="none" stroke="#08236B" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>

                        {{-- Bouclier de sécurité flottant --}}
                        <g transform="translate(112 132)">
                            <path d="M0 -30 L26 -20 V2 C26 20 15 31 0 37 C-15 31 -26 20 -26 2 V-20 Z" fill="#0B3394" opacity="0.92"></path>
                            <path d="M-11 1 L-3 10 L13 -9" fill="none" stroke="#DCE7FF" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>

                        {{-- Boîte d'archives --}}
                        <g filter="url(#auth-soft-shadow)">
                            <path d="M124 290 H340 L322 374 A12 12 0 0 1 310 384 H154 A12 12 0 0 1 142 374 Z" fill="#A9C3FF"></path>
                            <path d="M116 274 H348 A10 10 0 0 1 358 284 V296 H106 V284 A10 10 0 0 1 116 274 Z" fill="#DCE7FF"></path>
                            <path d="M212 296 H252 V326 A12 12 0 0 1 240 338 H224 A12 12 0 0 1 212 326 Z" fill="#2F6BFF" opacity="0.8"></path>
                            <path d="M164 330 H198" stroke="#2F6BFF" stroke-width="6" stroke-linecap="round" opacity="0.7"></path>
                            <path d="M266 330 H300" stroke="#2F6BFF" stroke-width="6" stroke-linecap="round" opacity="0.7"></path>
                        </g>

                        {{-- Loupe de recherche --}}
                        <g transform="translate(386 308)" filter="url(#auth-soft-shadow)">
                            <circle r="44" fill="#F7F9FF" fill-opacity="0.82" stroke="#0B3394" stroke-opacity="0.6" stroke-width="5"></circle>
                            <path d="M31 31 L59 59" stroke="#F5B91E" stroke-opacity="0.9" stroke-width="13" stroke-linecap="round"></path>
                            <path d="M-14 -6 A18 18 0 0 1 4 -18" fill="none" stroke="#FFFFFF" stroke-width="5" stroke-linecap="round"></path>
                        </g>

                        {{-- Éléments flottants --}}
                        <path d="M436 66 L442 80 L456 86 L442 92 L436 106 L430 92 L416 86 L430 80 Z" fill="#F5B91E" opacity="0.8"></path>
                        <circle cx="72" cy="248" r="9" fill="#FFD45C" opacity="0.75"></circle>
                        <circle cx="466" cy="216" r="6" fill="#F5B91E" opacity="0.7"></circle>
                        <path d="M76 74 C88 62 104 66 108 80 C94 82 82 88 76 100 C68 92 68 82 76 74 Z" fill="#A9C3FF" opacity="0.8"></path>
                    </svg>
                </div>

                {{-- Illustration secondaire en bas à gauche --}}
                <div class="auth-showcase__footer">
                    <svg class="auth-showcase__footer-art" viewBox="0 0 72 56" aria-hidden="true" focusable="false">
                        <rect x="12" y="20" width="50" height="30" rx="6" fill="#DCE7FF"></rect>
                        <path d="M12 26 A6 6 0 0 1 18 20 H56 A6 6 0 0 1 62 26 V30 H12 Z" fill="#A9C3FF"></path>
                        <rect x="6" y="34" width="48" height="18" rx="5" fill="#F7F9FF" stroke="#A9C3FF" stroke-width="2"></path>
                        <path d="M14 42 H30" stroke="#2F6BFF" stroke-width="3" stroke-linecap="round"></path>
                    </svg>
                    <div>
                        <p>© 2026 ENEAM. Tous droits réservés.</p>
                        <p>Application de gestion des PV de délibération</p>
                    </div>
                </div>
            </div>

            {{-- Panneau sombre : formulaire de connexion --}}
            <div class="auth-form-panel">
                {{-- Lockup conservé sur mobile et tablette, où le panneau clair est masqué --}}
                <div class="auth-form-panel__mobile-logo">
                    <div class="auth-brand auth-brand--center">
                        <span class="auth-brand__badge">
                            <img class="auth-brand__logo" src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" width="92" height="92">
                        </span>
                        <div class="auth-brand__text">
                            <span class="auth-brand__name">ENEAM</span>
                            <span class="auth-brand__tagline">École Nationale d'Économie Appliquée et de Management</span>
                        </div>
                    </div>
                </div>

                <div class="auth-form-panel__content">
                    <header class="auth-header">
                        <h1 class="auth-header__title" id="auth-title">Connexion</h1>
                        <p class="auth-header__subtitle">Gestion des PV de délibération</p>
                    </header>

                    @if (session('status'))
                        <div class="auth-alert auth-alert--success" role="status">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 3 3 5-6"></path>
                            </svg>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    @if (session('compte_desactive'))
                        <div class="auth-alert auth-alert--error" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v6"></path>
                                <path d="M12 16.5h.01"></path>
                            </svg>
                            <p>{{ session('compte_desactive') }}</p>
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <div class="auth-alert auth-alert--error" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v6"></path>
                                <path d="M12 16.5h.01"></path>
                            </svg>
                            <p>Identifiants incorrects. Vérifiez vos informations puis réessayez.</p>
                        </div>
                    @endif

                    @unless (session('compte_desactive'))
                        <form class="auth-form" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#' }}">
                        @csrf

                        <div class="auth-field @error('email') auth-field--invalid @enderror">
                            <label class="auth-field__label" for="email">Adresse e-mail</label>
                            <div class="auth-field__control">
                                <span class="auth-field__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                        <rect x="3" y="5" width="18" height="14" rx="3"></rect>
                                        <path d="m4 7 8 6 8-6"></path>
                                    </svg>
                                </span>
                                <input class="auth-field__input" id="email" name="email" type="email" inputmode="email" autocomplete="username" placeholder="Saisissez votre adresse e-mail" value="{{ old('email') }}" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            </div>
                            @error('email')
                                <p class="auth-field__error" id="email-error">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 8v5"></path>
                                        <path d="M12 16.5h.01"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="auth-field auth-field--password @error('password') auth-field--invalid @enderror">
                            <label class="auth-field__label" for="password">Mot de passe</label>
                            <div class="auth-field__control">
                                <span class="auth-field__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                        <rect x="4" y="10" width="16" height="11" rx="3"></rect>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                        <path d="M12 14.5v2"></path>
                                    </svg>
                                </span>
                                <input class="auth-field__input" id="password" name="password" type="password" autocomplete="current-password" placeholder="Saisissez votre mot de passe" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                                <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Afficher le mot de passe" aria-pressed="false" aria-controls="password">
                                    <svg class="auth-password-toggle__icon auth-password-toggle__icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="auth-password-toggle__icon auth-password-toggle__icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="M4 4l16 16"></path>
                                        <path d="M9.6 5.9A9.9 9.9 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a16 16 0 0 1-3.2 3.9"></path>
                                        <path d="M6.3 7.9A16.4 16.4 0 0 0 2.5 12S6 18.5 12 18.5a9.7 9.7 0 0 0 3-.5"></path>
                                        <path d="M9.9 10a3 3 0 0 0 4.2 4.2"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="auth-field__error" id="password-error">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 8v5"></path>
                                        <path d="M12 16.5h.01"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="auth-form__options">
                            <label class="auth-checkbox" for="remember">
                                <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span class="auth-checkbox__box" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                        <path d="m5 12 5 5L19 7"></path>
                                    </svg>
                                </span>
                                <span class="auth-checkbox__label">Se souvenir de moi</span>
                            </label>

                            {{-- TODO: activer le lien lorsque la route password.request sera enregistrée --}}
                            <a class="auth-link auth-form__forgot" href="{{ \Illuminate\Support\Facades\Route::has('password.request') ? route('password.request') : '#' }}">Mot de passe oublié ?</a>
                        </div>

                        <button class="auth-submit" type="submit" data-auth-submit>
                            <span class="auth-submit__content">
                                <span>Se connecter</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                    <path d="M5 12h14"></path>
                                    <path d="m13 6 6 6-6 6"></path>
                                </svg>
                            </span>
                            <span class="auth-submit__spinner" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" focusable="false">
                                    <circle cx="12" cy="12" r="9" opacity="0.25"></circle>
                                    <path d="M21 12a9 9 0 0 0-9-9"></path>
                                </svg>
                            </span>
                        </button>

                        <p class="auth-security">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <path d="M12 3 20 6v6c0 5-3.4 8.3-8 9.8C7.4 20.3 4 17 4 12V6Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            <span>Accès réservé au personnel autorisé. Les connexions sont enregistrées.</span>
                        </p>
                        </form>
                    @else
                        <p class="auth-security"><a class="auth-link" href="{{ route('login') }}">Retour à la connexion</a></p>
                    @endunless
                </div>
            </div>
        </section>
    </main>

    <script>
        (function () {
            'use strict';

            // Affichage / masquage du mot de passe.
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var input = document.getElementById(button.getAttribute('aria-controls'));
                    if (!input) {
                        return;
                    }

                    var isVisible = input.type === 'text';
                    input.type = isVisible ? 'password' : 'text';
                    button.setAttribute('aria-pressed', String(!isVisible));
                    button.setAttribute('aria-label', isVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
                });
            });

            // État de chargement du bouton : la soumission du formulaire n'est pas interrompue.
            document.querySelectorAll('[data-auth-submit]').forEach(function (button) {
                var form = button.closest('form');
                if (!form) {
                    return;
                }

                function resetSubmitState() {
                    button.classList.remove('is-loading');
                    button.disabled = false;
                    button.removeAttribute('aria-busy');
                }

                form.addEventListener('submit', function (event) {
                    if (button.classList.contains('is-loading')) {
                        return;
                    }

                    // Tant que la route n'est pas branchée, on évite un bouton bloqué sur "#".
                    var isPlaceholderAction = form.getAttribute('action') === '#';
                    if (isPlaceholderAction) {
                        event.preventDefault();
                    }

                    button.classList.add('is-loading');
                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');

                    if (isPlaceholderAction) {
                        window.setTimeout(resetSubmitState, 900);
                    }
                });

                // Le retour navigateur doit rendre le bouton à nouveau utilisable.
                window.addEventListener('pageshow', resetSubmitState);
            });
        })();
    </script>
</body>
</html>
