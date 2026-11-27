/*
 * Gestion des PV de délibération — animations d'interface.
 *
 * API par attributs data-* :
 *   data-drawer-open="#identifiant" | data-target="identifiant" : ouvre un tiroir.
 *   data-drawer-close                                           : ferme le tiroir parent.
 *   data-ui-menu-trigger + aria-controls                        : menu de ligne positionné en fixed.
 *   data-viewer / data-viewer-track / data-viewer-prev /
 *   data-viewer-next / data-viewer-thumb / data-viewer-counter   : visionneuse multi-pages.
 *   data-viewer-zoom="-1|1" / data-viewer-fit                    : zoom de la visionneuse.
 *
 * API JavaScript : window.UIDrawer.open(id), window.UIDrawer.close(id).
 * Aucune dépendance. Seuls transform, opacité et couleurs sont animés.
 */
(function () {
    'use strict';

    var motionReduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    var activeDrawer = null;
    var activeMenu = null;
    var activeMenuTrigger = null;

    function getFocusable(container) {
        return Array.prototype.slice.call(container.querySelectorAll(focusableSelector)).filter(function (element) {
            return element.offsetParent !== null || element === document.activeElement;
        });
    }

    function trapFocus(event, container) {
        if (event.key !== 'Tab') {
            return;
        }

        var focusable = getFocusable(container);

        if (focusable.length === 0) {
            event.preventDefault();
            return;
        }

        var first = focusable[0];
        var last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    /* ----------------------------------------------------------------------
       Tiroir latéral
       ---------------------------------------------------------------------- */

    function lockScroll() {
        var content = document.getElementById('admin-content');

        if (content) {
            content.dataset.uiOverflow = content.style.overflow;
            content.style.overflow = 'hidden';
            return;
        }

        var width = window.innerWidth - document.documentElement.clientWidth;
        document.body.dataset.uiPaddingRight = document.body.style.paddingRight;
        document.body.dataset.uiOverflow = document.body.style.overflow;
        document.body.style.paddingRight = width + 'px';
        document.body.style.overflow = 'hidden';
    }

    function unlockScroll() {
        var content = document.getElementById('admin-content');

        if (content) {
            content.style.overflow = content.dataset.uiOverflow || '';
            delete content.dataset.uiOverflow;
            return;
        }

        document.body.style.paddingRight = document.body.dataset.uiPaddingRight || '';
        document.body.style.overflow = document.body.dataset.uiOverflow || '';
        delete document.body.dataset.uiPaddingRight;
        delete document.body.dataset.uiOverflow;
    }

    function openDrawer(idOrElement, trigger) {
        var drawer = typeof idOrElement === 'string' ? document.getElementById(idOrElement) : idOrElement;

        if (!drawer || !drawer.classList.contains('ui-drawer')) {
            return;
        }

        if (activeDrawer === drawer) {
            return;
        }

        if (activeDrawer) {
            closeDrawer(activeDrawer);
        }

        var overlay = document.createElement('div');
        overlay.className = 'ui-drawer-overlay';
        drawer.parentNode.insertBefore(overlay, drawer);
        overlay.addEventListener('click', function () {
            closeDrawer(drawer);
        });

        drawer.hidden = false;
        drawer.classList.remove('is-closing');
        drawer._uiOverlay = overlay;
        drawer._uiTrigger = trigger || document.activeElement;
        activeDrawer = drawer;

        lockScroll();

        if (!drawer._uiTrapBound) {
            drawer.addEventListener('keydown', function (event) {
                trapFocus(event, drawer);
            });
            drawer._uiTrapBound = true;
        }

        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                if (activeDrawer !== drawer) {
                    return;
                }
                overlay.classList.add('is-visible');
                drawer.classList.add('is-visible');

                var focusable = getFocusable(drawer);
                if (focusable.length > 0) {
                    focusable[0].focus();
                }
            });
        });
    }

    function closeDrawer(idOrElement) {
        var drawer = typeof idOrElement === 'string' ? document.getElementById(idOrElement) : idOrElement;

        if (!drawer || !drawer.classList.contains('ui-drawer') || drawer.hidden) {
            return;
        }

        var overlay = drawer._uiOverlay;
        var trigger = drawer._uiTrigger;
        var finished = false;

        function finish() {
            if (finished) {
                return;
            }
            finished = true;

            drawer.removeEventListener('transitionend', onTransitionEnd);
            drawer.hidden = true;
            drawer.classList.remove('is-visible', 'is-closing');
            drawer.style.willChange = '';

            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }

            drawer._uiOverlay = null;
            drawer._uiTrigger = null;

            if (activeDrawer === drawer) {
                activeDrawer = null;
                unlockScroll();
            }

            if (trigger && typeof trigger.focus === 'function') {
                trigger.focus();
            }
        }

        function onTransitionEnd(event) {
            if (event.target === drawer && event.propertyName === 'transform') {
                finish();
            }
        }

        drawer.classList.remove('is-visible');
        drawer.classList.add('is-closing');

        if (overlay) {
            overlay.classList.remove('is-visible');
            overlay.classList.add('is-closing');
        }

        if (motionReduced.matches) {
            finish();
            return;
        }

        drawer.addEventListener('transitionend', onTransitionEnd);
        window.setTimeout(finish, 320);
    }

    window.UIDrawer = {
        open: openDrawer,
        close: closeDrawer,
    };

    /* ----------------------------------------------------------------------
       Menus de ligne positionnés en fixed
       ---------------------------------------------------------------------- */

    function positionMenu(menu, trigger) {
        var rect = trigger.getBoundingClientRect();
        var width = menu.offsetWidth;
        var height = menu.offsetHeight;
        var top = rect.bottom + 8;

        if (top + height > window.innerHeight - 8) {
            top = Math.max(8, rect.top - height - 8);
        }

        menu.style.position = 'fixed';
        menu.style.top = Math.round(top) + 'px';
        menu.style.left = Math.round(Math.max(8, Math.min(window.innerWidth - width - 8, rect.right - width))) + 'px';
    }

    function closeMenu() {
        if (!activeMenu) {
            return;
        }

        activeMenu.hidden = true;

        if (activeMenuTrigger) {
            activeMenuTrigger.setAttribute('aria-expanded', 'false');
        }

        activeMenu = null;
        activeMenuTrigger = null;
    }

    function openMenu(trigger) {
        var menu = document.getElementById(trigger.getAttribute('aria-controls'));

        if (!menu) {
            return;
        }

        var isOpen = activeMenu === menu;
        closeMenu();

        if (isOpen) {
            return;
        }

        menu.hidden = false;
        positionMenu(menu, trigger);
        trigger.setAttribute('aria-expanded', 'true');
        activeMenu = menu;
        activeMenuTrigger = trigger;

        var items = getFocusable(menu);
        if (items.length > 0) {
            items[0].focus();
        }
    }

    /* ----------------------------------------------------------------------
       Visionneuse horizontale multi-pages
       ---------------------------------------------------------------------- */

    function setupViewer(viewer) {
        var track = viewer.querySelector('[data-viewer-track]');
        var pages = Array.prototype.slice.call(viewer.querySelectorAll('[data-viewer-page]'));
        var thumbs = Array.prototype.slice.call(viewer.querySelectorAll('[data-viewer-thumb]'));
        var counter = viewer.querySelector('[data-viewer-counter]');
        var previous = viewer.querySelector('[data-viewer-prev]');
        var next = viewer.querySelector('[data-viewer-next]');
        var fit = viewer.querySelector('[data-viewer-fit]');
        var zoomButtons = Array.prototype.slice.call(viewer.querySelectorAll('[data-viewer-zoom]'));
        var current = 0;
        var scale = 1;

        function activate(index) {
            if (index < 0 || index >= pages.length) {
                return;
            }

            current = index;
            pages.forEach(function (page, pageIndex) {
                page.classList.toggle('is-active', pageIndex === index);
            });
            thumbs.forEach(function (thumb, thumbIndex) {
                var isActive = thumbIndex === index;
                thumb.classList.toggle('is-active', isActive);
                if (isActive) {
                    thumb.setAttribute('aria-current', 'true');
                } else {
                    thumb.removeAttribute('aria-current');
                }
            });

            if (counter) {
                counter.textContent = 'Page ' + (index + 1) + ' / ' + pages.length;
            }
            if (previous) {
                previous.disabled = index === 0;
            }
            if (next) {
                next.disabled = index === pages.length - 1;
            }
        }

        function goTo(index) {
            var clamped = Math.max(0, Math.min(pages.length - 1, index));
            activate(clamped);

            if (!track || motionReduced.matches) {
                return;
            }

            var page = pages[clamped];
            var left = page.offsetLeft - track.offsetLeft - (track.clientWidth - page.clientWidth) / 2;
            track.scrollTo({
                left: Math.max(0, left),
                behavior: 'smooth',
            });
        }

        if (previous) {
            previous.addEventListener('click', function () {
                goTo(current - 1);
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                goTo(current + 1);
            });
        }

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                goTo(Number(thumb.getAttribute('data-index')) - 1);
            });
        });

        if (track) {
            var pending = false;
            track.addEventListener('scroll', function () {
                if (pending) {
                    return;
                }
                pending = true;
                window.requestAnimationFrame(function () {
                    var center = track.scrollLeft + track.clientWidth / 2;
                    var closest = 0;
                    var distance = Infinity;
                    pages.forEach(function (page, index) {
                        var pageCenter = page.offsetLeft - track.offsetLeft + page.clientWidth / 2;
                        var delta = Math.abs(pageCenter - center);
                        if (delta < distance) {
                            distance = delta;
                            closest = index;
                        }
                    });
                    activate(closest);
                    pending = false;
                });
            }, { passive: true });

            viewer.addEventListener('keydown', function (event) {
                if (event.target.matches('input, select, textarea')) {
                    return;
                }
                if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    goTo(current + 1);
                } else if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    goTo(current - 1);
                }
            });
        }

        function applyZoom() {
            viewer.style.setProperty('--viewer-scale', String(scale));
        }

        zoomButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var direction = Number(button.getAttribute('data-viewer-zoom'));
                scale = Math.max(0.8, Math.min(1.4, scale + direction * 0.1));
                applyZoom();
            });
        });

        if (fit) {
            fit.addEventListener('click', function () {
                scale = 1;
                applyZoom();
            });
        }

        if (window.IntersectionObserver && track) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && entry.intersectionRatio > 0.6) {
                        activate(pages.indexOf(entry.target));
                    }
                });
            }, { root: track, threshold: [0.6] });

            pages.forEach(function (page) {
                observer.observe(page);
            });
        }

        activate(0);
    }

    document.querySelectorAll('[data-viewer]').forEach(setupViewer);

    /* ----------------------------------------------------------------------
       Délégation des clics et touche Échap
       ---------------------------------------------------------------------- */

    document.addEventListener('click', function (event) {
        var drawerTrigger = event.target.closest('[data-drawer-open]');

        if (drawerTrigger) {
            event.preventDefault();
            var targetId = drawerTrigger.getAttribute('data-target') || drawerTrigger.getAttribute('data-drawer-open');
            openDrawer(targetId.replace(/^#/, ''), drawerTrigger);
            return;
        }

        var drawerClose = event.target.closest('[data-drawer-close]');

        if (drawerClose) {
            var drawer = drawerClose.closest('.ui-drawer');
            if (drawer) {
                event.preventDefault();
                closeDrawer(drawer);
            }
            return;
        }

        var menuTrigger = event.target.closest('[data-ui-menu-trigger]');

        if (menuTrigger) {
            event.preventDefault();
            event.stopPropagation();
            openMenu(menuTrigger);
            return;
        }

        if (activeMenu && !event.target.closest('.ui-menu')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (activeMenu) {
                var trigger = activeMenuTrigger;
                closeMenu();
                if (trigger) {
                    trigger.focus();
                }
                return;
            }

            if (activeDrawer) {
                closeDrawer(activeDrawer);
            }
            return;
        }

        if (activeMenu && (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Home' || event.key === 'End')) {
            var items = getFocusable(activeMenu);

            if (items.length === 0) {
                return;
            }

            var index = items.indexOf(document.activeElement);
            var nextIndex = index;

            if (event.key === 'ArrowDown') {
                nextIndex = (index + 1) % items.length;
            } else if (event.key === 'ArrowUp') {
                nextIndex = (index - 1 + items.length) % items.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else {
                nextIndex = items.length - 1;
            }

            event.preventDefault();
            items[nextIndex].focus();
        }
    });

    window.addEventListener('scroll', function () {
        if (activeMenu) {
            closeMenu();
        }
    }, true);

    window.addEventListener('resize', function () {
        if (activeMenu && activeMenuTrigger) {
            positionMenu(activeMenu, activeMenuTrigger);
        }
    });
})();
