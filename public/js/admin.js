/*
 * Gestion des PV de délibération — interactions de l'espace administrateur.
 * JavaScript vanilla, sans dépendance.
 */
(function () {
    'use strict';

    var focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

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
       Sidebar mobile
       ---------------------------------------------------------------------- */

    var sidebar = document.getElementById('admin-sidebar');
    var sidebarOpenButton = document.querySelector('[data-sidebar-open]');
    var sidebarCloseButton = document.querySelector('[data-sidebar-close]');
    var sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');

    function openSidebar() {
        if (!sidebar) {
            return;
        }

        sidebar.classList.add('is-open');
        if (sidebarBackdrop) {
            sidebarBackdrop.hidden = false;
        }
        if (sidebarOpenButton) {
            sidebarOpenButton.setAttribute('aria-expanded', 'true');
        }
        if (sidebarCloseButton) {
            sidebarCloseButton.focus();
        }
    }

    function closeSidebar() {
        if (!sidebar) {
            return;
        }

        sidebar.classList.remove('is-open');
        if (sidebarBackdrop) {
            sidebarBackdrop.hidden = true;
        }
        if (sidebarOpenButton) {
            sidebarOpenButton.setAttribute('aria-expanded', 'false');
            sidebarOpenButton.focus();
        }
    }

    if (sidebarOpenButton) {
        sidebarOpenButton.addEventListener('click', openSidebar);
    }

    if (sidebarCloseButton) {
        sidebarCloseButton.addEventListener('click', closeSidebar);
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', closeSidebar);
    }

    if (sidebar) {
        sidebar.addEventListener('keydown', function (event) {
            if (sidebar.classList.contains('is-open')) {
                trapFocus(event, sidebar);
            }
        });
    }

    /* ----------------------------------------------------------------------
       Menus déroulants
       ---------------------------------------------------------------------- */

    var dropdownTriggers = Array.prototype.slice.call(document.querySelectorAll('[data-dropdown]'));

    function positionDropdown(menu, trigger) {
        if (!menu.closest('.admin-table-wrap')) {
            menu.style.position = '';
            menu.style.top = '';
            menu.style.left = '';
            menu.style.right = '';
            return;
        }

        var rect = trigger.getBoundingClientRect();
        var left = Math.max(12, Math.min(window.innerWidth - menu.offsetWidth - 12, rect.right - menu.offsetWidth));

        menu.style.position = 'fixed';
        menu.style.right = 'auto';
        menu.style.top = rect.bottom + 10 + 'px';
        menu.style.left = left + 'px';
    }

    function closeDropdown(trigger) {
        var menu = document.getElementById(trigger.getAttribute('aria-controls'));

        if (menu) {
            menu.hidden = true;
        }
        trigger.setAttribute('aria-expanded', 'false');
    }

    function closeAllDropdowns(except) {
        dropdownTriggers.forEach(function (trigger) {
            if (trigger !== except) {
                closeDropdown(trigger);
            }
        });
    }

    dropdownTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();

            var menu = document.getElementById(trigger.getAttribute('aria-controls'));
            var isOpen = trigger.getAttribute('aria-expanded') === 'true';

            closeAllDropdowns(trigger);

            if (menu) {
                menu.hidden = isOpen;

                if (!isOpen) {
                    positionDropdown(menu, trigger);
                }
            }
            trigger.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('[data-dropdown]')) {
            closeAllDropdowns();
        }
    });

    window.addEventListener('resize', function () {
        closeAllDropdowns();
    });

    document.addEventListener('scroll', function () {
        closeAllDropdowns();
    }, true);

    /* ----------------------------------------------------------------------
       Onglets
       ---------------------------------------------------------------------- */

    var tabLists = Array.prototype.slice.call(document.querySelectorAll('[role="tablist"]'));

    tabLists.forEach(function (tabList) {
        var tabs = Array.prototype.slice.call(tabList.querySelectorAll('[data-tab]'));

        function activateTab(tab) {
            tabs.forEach(function (item) {
                var panel = document.querySelector('[data-tab-panel="' + item.getAttribute('data-tab') + '"]');
                var isActive = item === tab;

                item.setAttribute('aria-selected', String(isActive));
                item.setAttribute('tabindex', isActive ? '0' : '-1');

                if (panel) {
                    panel.hidden = !isActive;
                }
            });
            tab.focus();
        }

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () {
                activateTab(tab);
            });

            tab.addEventListener('keydown', function (event) {
                var nextIndex = null;

                if (event.key === 'ArrowRight') {
                    nextIndex = (index + 1) % tabs.length;
                } else if (event.key === 'ArrowLeft') {
                    nextIndex = (index - 1 + tabs.length) % tabs.length;
                } else if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = tabs.length - 1;
                }

                if (nextIndex !== null) {
                    event.preventDefault();
                    activateTab(tabs[nextIndex]);
                }
            });
        });
    });

    /* ----------------------------------------------------------------------
       Tiroirs et fenêtres modales
       ---------------------------------------------------------------------- */

    var activeOverlay = null;
    var activeOverlayTarget = null;
    var previousFocus = null;

    function closeOverlay() {
        if (!activeOverlay) {
            return;
        }

        activeOverlay.hidden = true;
        if (activeOverlayTarget) {
            activeOverlayTarget.hidden = true;
        }

        activeOverlay = null;
        activeOverlayTarget = null;

        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus();
        }
        previousFocus = null;
    }

    function openOverlay(targetId) {
        var target = document.getElementById(targetId);

        if (!target) {
            return;
        }

        closeOverlay();

        var overlay = document.createElement('div');
        overlay.className = 'admin-overlay';
        overlay.hidden = false;

        target.parentNode.insertBefore(overlay, target);
        target.hidden = false;

        activeOverlay = overlay;
        activeOverlayTarget = target;
        previousFocus = document.activeElement;

        overlay.addEventListener('click', closeOverlay);

        var focusable = getFocusable(target);
        if (focusable.length > 0) {
            focusable[0].focus();
        }

        target.addEventListener('keydown', function (event) {
            trapFocus(event, target);
        });
    }

    document.querySelectorAll('[data-drawer-open], [data-modal-open]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            var targetId = trigger.getAttribute('data-target') || trigger.getAttribute('aria-controls');
            openOverlay(targetId);
        });
    });

    document.querySelectorAll('[data-overlay-close]').forEach(function (button) {
        button.addEventListener('click', closeOverlay);
    });

    if (window.location.hash) {
        var hashedTarget = document.querySelector(window.location.hash);

        if (hashedTarget && (hashedTarget.classList.contains('admin-drawer') || hashedTarget.classList.contains('admin-modal'))) {
            openOverlay(hashedTarget.id);
        }
    }

    /* ----------------------------------------------------------------------
       Infobulles du graphique et du plan de numérisation
       ---------------------------------------------------------------------- */

    var tooltip = document.createElement('div');
    tooltip.className = 'admin-chart-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    tooltip.hidden = true;
    document.body.appendChild(tooltip);

    function showTooltip(event) {
        var element = event.currentTarget;
        var text = element.getAttribute('data-tooltip');

        if (!text) {
            return;
        }

        tooltip.textContent = text;
        tooltip.hidden = false;

        var rect = element.getBoundingClientRect();
        var left = Math.min(window.innerWidth - tooltip.offsetWidth - 12, Math.max(12, rect.left + rect.width / 2 - tooltip.offsetWidth / 2));
        var top = rect.top - tooltip.offsetHeight - 10;

        if (top < 8) {
            top = rect.bottom + 10;
        }

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    }

    function hideTooltip() {
        tooltip.hidden = true;
    }

    document.querySelectorAll('[data-tooltip]').forEach(function (element) {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mousemove', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
        element.addEventListener('focus', showTooltip);
        element.addEventListener('blur', hideTooltip);
    });

    /* ----------------------------------------------------------------------
       Lignes dépliables du journal
       ---------------------------------------------------------------------- */

    document.querySelectorAll('[data-details-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var detailRow = document.getElementById(button.getAttribute('aria-controls'));
            var isOpen = button.getAttribute('aria-expanded') === 'true';

            if (detailRow) {
                detailRow.hidden = !isOpen;
            }
            button.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    /* ----------------------------------------------------------------------
       Pastilles de semestre et cases à cocher groupées
       ---------------------------------------------------------------------- */

    document.querySelectorAll('.admin-semester-row').forEach(function (group) {
        group.addEventListener('click', function (event) {
            var button = event.target.closest('.admin-semester');

            if (!button) {
                return;
            }

            group.querySelectorAll('.admin-semester').forEach(function (item) {
                item.classList.toggle('is-active', item === button);
                item.setAttribute('aria-pressed', String(item === button));
            });
        });
    });

    document.querySelectorAll('[data-select-all]').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var container = document.getElementById(checkbox.getAttribute('data-select-all'));

            if (container) {
                container.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(function (item) {
                    item.checked = checkbox.checked;
                });
            }
        });
    });

    /* ----------------------------------------------------------------------
       Touche Échap : fermeture du niveau le plus récent
       ---------------------------------------------------------------------- */

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        if (activeOverlay) {
            closeOverlay();
            return;
        }

        var openTrigger = dropdownTriggers.find(function (trigger) {
            return trigger.getAttribute('aria-expanded') === 'true';
        });

        if (openTrigger) {
            closeAllDropdowns();
            openTrigger.focus();
            return;
        }

        if (sidebar && sidebar.classList.contains('is-open')) {
            closeSidebar();
        }
    });
})();
