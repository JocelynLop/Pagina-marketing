import './bootstrap.js';
import './styles/app.css';

const isLikelyMobileDevice = typeof window.orientation !== 'undefined';

document.documentElement.classList.toggle('is-mobile-device', isLikelyMobileDevice);

const initHeroCarousel = () => {
    document.querySelectorAll('[data-hero-carousel]').forEach((heroCarousel) => {
        if (heroCarousel.dataset.carouselBound === 'true') {
            return;
        }

        const slides = [...heroCarousel.querySelectorAll('.hero-slide, .hero-dish-slide')];
        let activeSlide = slides.findIndex((slide) => slide.classList.contains('active'));
        activeSlide = activeSlide >= 0 ? activeSlide : 0;

        if (slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            heroCarousel.dataset.carouselBound = 'true';
            window.setInterval(() => {
                slides[activeSlide].classList.remove('active');
                activeSlide = (activeSlide + 1) % slides.length;
                slides[activeSlide].classList.add('active');
            }, 5200);
        }
    });
};

const initTopMenuCarousel = () => {
    document.querySelectorAll('[data-top-menu-carousel]').forEach((carousel) => {
        if (carousel.dataset.topCarouselBound === 'true') {
            return;
        }

        const track = carousel.querySelector('.top-menu-track');
        const cards = [...carousel.querySelectorAll('.top-menu-card')];

        if (!track || cards.length === 0) {
            return;
        }

        carousel.dataset.topCarouselBound = 'true';
        let activeIndex = Math.max(0, cards.findIndex((card) => card.classList.contains('active')));
        let dragStartX = null;

        const setActiveCard = (index) => {
            activeIndex = (index + cards.length) % cards.length;
            const midpoint = Math.floor(cards.length / 2);

            cards.forEach((card, cardIndex) => {
                const isActive = cardIndex === activeIndex;
                const circularOffset = ((cardIndex - activeIndex + cards.length + midpoint) % cards.length) - midpoint;

                card.classList.toggle('active', isActive);
                card.setAttribute('aria-current', isActive ? 'true' : 'false');
                card.dataset.carouselPosition = String(circularOffset);
            });
        };

        cards.forEach((card, cardIndex) => {
            card.addEventListener('click', () => {
                if (cardIndex !== activeIndex) {
                    setActiveCard(cardIndex);
                }
            });
        });

        track.addEventListener('pointerdown', (event) => {
            dragStartX = event.clientX;
            track.setPointerCapture?.(event.pointerId);
        });

        track.addEventListener('pointerup', (event) => {
            if (dragStartX === null) {
                return;
            }

            const dragDistance = event.clientX - dragStartX;
            dragStartX = null;

            if (Math.abs(dragDistance) < 44) {
                return;
            }

            setActiveCard(activeIndex + (dragDistance < 0 ? 1 : -1));
        });

        track.addEventListener('pointercancel', () => {
            dragStartX = null;
        });

        setActiveCard(activeIndex);

        if (cards.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            window.setInterval(() => setActiveCard(activeIndex + 1), 4800);
        }
    });
};

const setFanCarouselItem = (carousel, index) => {
    const items = [...carousel.querySelectorAll('[data-fan-item]')];
    const dots = [...carousel.querySelectorAll('[data-fan-dot]')];

    if (items.length === 0) {
        return;
    }

    const activeIndex = (index + items.length) % items.length;
    const midpoint = Math.floor(items.length / 2);
    carousel.dataset.fanActiveIndex = String(activeIndex);

    items.forEach((item, itemIndex) => {
        const isActive = itemIndex === activeIndex;
        const circularOffset = ((itemIndex - activeIndex + items.length + midpoint) % items.length) - midpoint;

        item.classList.toggle('active', isActive);
        item.setAttribute('aria-current', isActive ? 'true' : 'false');
        item.dataset.fanPosition = String(circularOffset);
    });

    dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === activeIndex));
};

const getFanActiveIndex = (carousel) => Number.parseInt(carousel.dataset.fanActiveIndex || '0', 10) || 0;

const getFanClickDirection = (carousel, event, item) => {
    const itemPosition = Number.parseInt(item?.dataset.fanPosition || '0', 10);
    if (itemPosition === 1 || itemPosition === -1) {
        return itemPosition;
    }

    const activeItem = carousel.querySelector('[data-fan-item].active');
    if (!activeItem) {
        return 0;
    }

    const activeRect = activeItem.getBoundingClientRect();
    if (event.clientX < activeRect.left) {
        return -1;
    }

    if (event.clientX > activeRect.right) {
        return 1;
    }

    return 0;
};

const initFanCarousels = () => {
    document.querySelectorAll('[data-fan-carousel]').forEach((carousel) => {
        const items = [...carousel.querySelectorAll('[data-fan-item]')];
        const track = carousel.querySelector('.publication-fan-track');

        if (!track || items.length === 0) {
            return;
        }

        const activeIndex = Math.max(0, items.findIndex((item) => item.classList.contains('active')));
        setFanCarouselItem(carousel, activeIndex);

        const mobileFanQuery = window.matchMedia('(max-width: 900px)');
        if (!isLikelyMobileDevice || !mobileFanQuery.matches) {
            carousel.dataset.fanControlsBound = 'desktop-static';
            return;
        }

        if (carousel.dataset.fanControlsBound !== 'true') {
            carousel.dataset.fanControlsBound = 'true';
            let dragStartX = null;
            let mouseStartX = null;
            let touchStartX = null;
            let lastDragAt = 0;
            let didDrag = false;

            const applyDragDistance = (dragDistance) => {
                if (Math.abs(dragDistance) < 44) {
                    return;
                }

                const now = Date.now();
                if (now - lastDragAt < 220) {
                    return;
                }

                lastDragAt = now;
                didDrag = true;
                carousel.dataset.fanJustDragged = 'true';
                setFanCarouselItem(carousel, getFanActiveIndex(carousel) + (dragDistance < 0 ? 1 : -1));
                window.setTimeout(() => {
                    didDrag = false;
                    delete carousel.dataset.fanJustDragged;
                }, 0);
            };

            items.forEach((item) => {
                item.addEventListener('click', (event) => {
                    const target = event.target instanceof Element ? event.target : event.target.parentElement;
                    if (target?.closest('a, button, input, textarea, select, label, form')) {
                        return;
                    }

                    if (didDrag) {
                        event.preventDefault();
                        didDrag = false;
                    }
                });
            });

            track.addEventListener('pointerdown', (event) => {
                dragStartX = event.clientX;
                didDrag = false;
                track.setPointerCapture?.(event.pointerId);
            });

            track.addEventListener('pointerup', (event) => {
                if (dragStartX === null) {
                    return;
                }

                const dragDistance = event.clientX - dragStartX;
                dragStartX = null;
                applyDragDistance(dragDistance);
            });

            track.addEventListener('pointercancel', () => {
                dragStartX = null;
                didDrag = false;
            });

            track.addEventListener('mousedown', (event) => {
                mouseStartX = event.clientX;
            });

            track.addEventListener('mouseup', (event) => {
                if (mouseStartX === null) {
                    return;
                }

                const dragDistance = event.clientX - mouseStartX;
                mouseStartX = null;
                applyDragDistance(dragDistance);
            });

            track.addEventListener('touchstart', (event) => {
                touchStartX = event.touches[0]?.clientX ?? null;
            }, { passive: true });

            track.addEventListener('touchend', (event) => {
                if (touchStartX === null) {
                    return;
                }

                const dragDistance = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
                touchStartX = null;
                applyDragDistance(dragDistance);
            });
        }
    });
};

const initPublicationSearch = () => {
    document.querySelectorAll('[data-publication-search]').forEach((searchInput) => {
        if (searchInput.dataset.searchBound === 'true') {
            return;
        }

        const section = searchInput.closest('section') || document;
        const cards = [...section.querySelectorAll('[data-publication-card]')];
        const noResults = section.querySelector('[data-no-results]');

        if (cards.length === 0) {
            return;
        }

        searchInput.dataset.searchBound = 'true';
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            let visibleCards = 0;

            cards.forEach((card) => {
                const isVisible = card.textContent.toLowerCase().includes(term);
                card.hidden = !isVisible;
                visibleCards += isVisible ? 1 : 0;
            });

            if (noResults) {
                noResults.hidden = visibleCards > 0;
            }
        });
    });
};

const closeRestaurantModal = (modal) => {
    if (!modal) {
        return;
    }

    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');
    modal.removeAttribute('role');
    modal.style.display = '';
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop.restaurant-modal-backdrop').forEach((backdrop) => backdrop.remove());

    if (window.location.hash === `#${modal.id}`) {
        window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
    }
};

const openRestaurantModal = (modal, trigger) => {
    if (!modal) {
        return;
    }

    if (window.bootstrap?.Modal) {
        try {
            window.bootstrap.Modal.getOrCreateInstance(modal).show();
        } catch {
            // Use the local fallback below when Bootstrap is unavailable or blocked.
        }

        if (modal.classList.contains('show')) {
            return;
        }
    }

    modal.style.display = 'block';
    modal.removeAttribute('aria-hidden');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    modal.classList.add('show');
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';

    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show restaurant-modal-backdrop';
    document.body.append(backdrop);
    modal.querySelector('.split-modal-body')?.scrollTo({ top: 0 });
    modal.querySelector('.modal-close-floating')?.focus({ preventScroll: true });
    modal.dataset.returnFocus = trigger ? 'true' : 'false';
};

const openPublicationModalFromButton = (button) => {
    if (!button) {
        return true;
    }

    const modalSelector = button.getAttribute('data-card-modal-target') || button.getAttribute('data-bs-target') || button.getAttribute('href');
    const modal = modalSelector ? document.querySelector(modalSelector) : null;
    const card = button.closest('.interactive-card');
    const fanItem = card?.closest('[data-fan-item]');

    if (fanItem && !fanItem.classList.contains('active')) {
        return false;
    }

    const now = Date.now();
    if (Number.parseInt(button.dataset.lastModalOpenAt || '0', 10) + 220 > now) {
        return false;
    }

    button.dataset.lastModalOpenAt = String(now);
    markNoveltyAsViewed(button.dataset.markViewed || card?.dataset.markViewed);
    openRestaurantModal(modal, button);

    if (modalSelector && window.location.hash !== modalSelector) {
        window.location.hash = modalSelector;
    }

    return false;
};

window.openLasTablasCardModal = openPublicationModalFromButton;

const getViewedNovelties = () => new Set(JSON.parse(window.localStorage.getItem('las-tablas-viewed-novelties') || '[]'));

const saveViewedNovelties = (viewedNovelties) => {
    window.localStorage.setItem('las-tablas-viewed-novelties', JSON.stringify([...viewedNovelties]));
};

const markNoveltyAsViewed = (id) => {
    if (!id) {
        return;
    }

    const latestViewed = getViewedNovelties();
    latestViewed.add(id);
    saveViewedNovelties(latestViewed);

    const card = document.querySelector(`[data-novelty-id="${id}"]`);
    const badge = card?.querySelector('[data-new-badge]');
    if (badge) {
        badge.hidden = true;
    }
};

const initViewedNovelties = () => {
    const viewedNovelties = getViewedNovelties();

    document.querySelectorAll('[data-novelty-id]').forEach((card) => {
        const id = card.dataset.noveltyId;
        const badge = card.querySelector('[data-new-badge]');

        if (badge && viewedNovelties.has(id)) {
            badge.hidden = true;
        }
    });

    document.querySelectorAll('[data-mark-viewed]').forEach((button) => {
        if (button.dataset.viewedBound === 'true') {
            return;
        }

        button.dataset.viewedBound = 'true';
        button.addEventListener('click', () => {
            markNoveltyAsViewed(button.dataset.markViewed);
        });
    });
};

const updateCountdowns = () => {
    document.querySelectorAll('[data-countdown]').forEach((badge) => {
        const output = badge.querySelector('small');
        const expiresAt = new Date(badge.dataset.countdown).getTime();
        const diff = expiresAt - Date.now();

        if (!output) {
            return;
        }

        if (Number.isNaN(expiresAt) || diff <= 0) {
            badge.hidden = true;
            return;
        }

        const minutes = Math.floor(diff / 60000) % 60;
        const hours = Math.floor(diff / 3600000) % 24;
        const days = Math.floor(diff / 86400000);
        output.textContent = `${days}d ${hours}h ${minutes}m`;
    });
};

const initMenuFilters = () => {
    document.querySelectorAll('[data-menu-list]').forEach((menuList) => {
        const container = menuList.closest('section') || document;
        const typeButtons = [...container.querySelectorAll('[data-menu-filter]')];
        const categoryButtons = [...container.querySelectorAll('[data-category-filter]')];
        const menuCards = [...menuList.querySelectorAll('[data-menu-card]')];
        const categorySections = [...menuList.querySelectorAll('[data-menu-category-section]')];
        const toggles = [...menuList.querySelectorAll('[data-menu-category-toggle]')];

        if (typeButtons.length === 0 || menuCards.length === 0) {
            return;
        }

        let activeType = (typeButtons.find((button) => button.classList.contains('active')) || typeButtons[0]).dataset.menuFilter || 'morning';
        const activeCategoriesByType = typeButtons.reduce((categories, button) => ({
            ...categories,
            [button.dataset.menuFilter || 'morning']: 'all',
        }), {});

        const applyMenuFilter = () => {
            let activeCategory = activeCategoriesByType[activeType] || 'all';
            const visibleCategoryButtons = categoryButtons.filter((button) => {
                const supportedTypes = (button.dataset.categoryTypes || 'morning afternoon').split(' ');
                const shouldShow = button.dataset.categoryFilter === 'all' || supportedTypes.includes(activeType);
                button.hidden = !shouldShow;

                return shouldShow;
            });

            if (activeCategory !== 'all' && !visibleCategoryButtons.some((button) => button.dataset.categoryFilter === activeCategory)) {
                activeCategory = 'all';
                activeCategoriesByType[activeType] = 'all';
            }

            typeButtons.forEach((button) => {
                const isActive = button.dataset.menuFilter === activeType;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            categoryButtons.forEach((button) => {
                const isActive = button.dataset.categoryFilter === activeCategory;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            menuCards.forEach((card) => {
                const matchesType = card.dataset.menuType === activeType;
                const matchesCategory = activeCategory === 'all' || card.dataset.menuCategory === activeCategory;
                const shouldShow = matchesType && matchesCategory;
                card.hidden = !shouldShow;
                card.classList.toggle('is-filtered-out', !shouldShow);
            });

            categorySections.forEach((section) => {
                const visibleCards = [...section.querySelectorAll('[data-menu-card]')].some((card) => !card.hidden);
                section.hidden = !visibleCards;
            });
        };

        applyMenuFilter();

        typeButtons.forEach((button) => {
            if (button.dataset.menuBound === 'true') {
                return;
            }

            button.dataset.menuBound = 'true';
            const selectMenuType = (event) => {
                event.preventDefault();
                activeType = button.dataset.menuFilter || activeType;
                applyMenuFilter();
            };

            button.addEventListener('click', selectMenuType);
            button.addEventListener('pointerup', selectMenuType);
        });

        categoryButtons.forEach((button) => {
            if (button.dataset.categoryBound === 'true') {
                return;
            }

            button.dataset.categoryBound = 'true';
            const selectCategory = (event) => {
                event.preventDefault();
                const now = Date.now();
                if (Number.parseInt(button.dataset.lastCategoryAt || '0', 10) + 180 > now) {
                    return;
                }

                button.dataset.lastCategoryAt = String(now);
                const currentCategory = activeCategoriesByType[activeType] || 'all';
                activeCategoriesByType[activeType] = currentCategory === button.dataset.categoryFilter ? 'all' : button.dataset.categoryFilter;
                applyMenuFilter();
            };

            button.addEventListener('click', selectCategory);
            button.addEventListener('pointerup', selectCategory);
        });

        toggles.forEach((button) => {
            if (button.dataset.toggleBound === 'true') {
                return;
            }

            button.dataset.toggleBound = 'true';
            const toggleCategoryBlock = () => {
                const now = Date.now();
                if (Number.parseInt(button.dataset.lastToggleAt || '0', 10) + 180 > now) {
                    return;
                }

                button.dataset.lastToggleAt = String(now);
                const block = button.closest('.menu-category-block');
                const panel = block?.querySelector('[data-menu-category-panel]');
                const collapsed = block?.classList.toggle('is-collapsed');
                button.setAttribute('aria-expanded', String(!collapsed));
                panel?.toggleAttribute('hidden', Boolean(collapsed));
            };

            button.addEventListener('click', toggleCategoryBlock);
            button.addEventListener('pointerup', toggleCategoryBlock);
        });
    });
};

const initInteractiveCards = () => {
    document.querySelectorAll('.restaurant-modal').forEach((modal) => {
        if (modal.parentElement !== document.body) {
            document.body.append(modal);
        }

        if (modal.dataset.drawerBound !== 'true') {
            modal.dataset.drawerBound = 'true';
            modal.addEventListener('show.bs.modal', () => {
                modal.querySelector('.split-modal-body').scrollTop = 0;
            });
            modal.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
                button.addEventListener('click', () => closeRestaurantModal(modal));
            });
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeRestaurantModal(modal);
                }
            });
        }
    });

    document.querySelectorAll('.interactive-card').forEach((card) => {
        if (card.dataset.cardBound === 'true') {
            return;
        }

        card.dataset.cardBound = 'true';
        const openCardModal = () => {
            const target = card.dataset.cardModal ? document.querySelector(card.dataset.cardModal) : null;
            if (!target) {
                return;
            }

            markNoveltyAsViewed(card.dataset.markViewed);
            target.addEventListener('hidden.bs.modal', () => card.focus({ preventScroll: true }), { once: true });
            openRestaurantModal(target, card);
        };

        card.querySelectorAll('[data-bs-target]').forEach((button) => {
            if (button.dataset.cardModalButtonBound === 'true') {
                return;
            }

            button.dataset.cardModalButtonBound = 'true';
            button.addEventListener('click', () => {
                openCardModal();
            });
        });

        card.addEventListener('click', (event) => {
            const fanItem = card.closest('[data-fan-item]');
            const fanCarousel = fanItem?.closest('[data-fan-carousel]');
            if (fanCarousel?.dataset.fanJustDragged === 'true') {
                return;
            }

            if (fanItem && !fanItem.classList.contains('active')) {
                return;
            }

            if (event.target.closest('a, button, input, textarea, select, label, form')) {
                return;
            }

            openCardModal();
        });

        card.addEventListener('keydown', (event) => {
            if (event.target === card && (event.key === 'Enter' || event.key === ' ')) {
                event.preventDefault();
                openCardModal();
            }
        });
    });
};

const initMenuCategorySelects = () => {
    document.querySelectorAll('[data-menu-type-select]').forEach((typeSelect) => {
        const form = typeSelect.closest('form') || document;
        const categorySelect = form.querySelector('[data-menu-category-select]');

        if (!categorySelect || typeSelect.dataset.categorySelectBound === 'true') {
            return;
        }

        const updateCategoryOptions = () => {
            const activeType = typeSelect.value || 'morning';
            const options = [...categorySelect.querySelectorAll('option')];
            let selectedStillVisible = false;
            let firstVisible = null;

            options.forEach((option) => {
                const supportedTypes = (option.dataset.categoryTypes || 'morning afternoon').split(' ');
                const shouldShow = supportedTypes.includes(activeType);
                option.hidden = !shouldShow;
                option.disabled = !shouldShow;

                if (shouldShow && !firstVisible) {
                    firstVisible = option;
                }

                if (shouldShow && option.selected) {
                    selectedStillVisible = true;
                }
            });

            if (!selectedStillVisible && firstVisible) {
                firstVisible.selected = true;
            }
        };

        typeSelect.dataset.categorySelectBound = 'true';
        typeSelect.addEventListener('change', updateCategoryOptions);
        updateCategoryOptions();
    });
};

const initPublicationModalButtons = () => {
    if (window.lasTablasModalButtonsBound) {
        return;
    }

    window.lasTablasModalButtonsBound = true;
    const openFromButton = (event) => {
        const target = event.target instanceof Element ? event.target : event.target.parentElement;
        const button = target?.closest('.read-more-pill[data-card-modal-target], .read-more-pill[data-bs-target]');

        if (!button) {
            return;
        }

        const card = button.closest('.interactive-card');
        const fanItem = card?.closest('[data-fan-item]');

        if (fanItem && !fanItem.classList.contains('active')) {
            return;
        }

        openPublicationModalFromButton(button);
    };

    document.addEventListener('click', openFromButton, true);
    document.addEventListener('pointerup', openFromButton, true);
};

const openModalFromHash = () => {
    const id = window.location.hash.slice(1);
    if (!id) {
        return;
    }

    const modal = document.getElementById(id);
    if (!modal?.classList.contains('restaurant-modal')) {
        return;
    }

    openRestaurantModal(modal, null);
};

const initHashModals = () => {
    if (window.lasTablasHashModalsBound) {
        return;
    }

    window.lasTablasHashModalsBound = true;
    window.addEventListener('hashchange', openModalFromHash);
    openModalFromHash();
};

const initPhoneCopy = () => {
    document.querySelectorAll('[data-copy-phone]').forEach((button) => {
        if (button.dataset.copyBound === 'true') {
            return;
        }

        button.dataset.copyBound = 'true';
        button.addEventListener('click', async () => {
            const phone = button.dataset.copyPhone;

            try {
                await navigator.clipboard.writeText(phone);
                button.classList.add('copied');
                button.querySelector('span').textContent = 'Copiado';
                window.setTimeout(() => {
                    button.classList.remove('copied');
                    button.querySelector('span').textContent = 'Telefono';
                }, 1600);
            } catch {
                window.alert(`Telefono: ${phone}`);
            }
        });
    });
};

const reactionButtonTextNode = (button) => [...button.childNodes].find((node) => node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '');

const updateReactionButton = (button, payload) => {
    const icon = button.querySelector('i');
    const label = button.querySelector('span');
    const activeIcon = payload.iconActive || (button.classList.contains('star-button') ? 'bi-star-fill' : 'bi-heart-fill');
    const inactiveIcon = payload.iconInactive || (button.classList.contains('star-button') ? 'bi-star' : 'bi-heart');

    button.classList.toggle('active', Boolean(payload.active));
    button.setAttribute('aria-pressed', payload.active ? 'true' : 'false');

    if (icon) {
        icon.classList.remove(activeIcon, inactiveIcon);
        icon.classList.add(payload.active ? activeIcon : inactiveIcon);
    }

    if (label) {
        label.textContent = payload.label;
        return;
    }

    const textNode = reactionButtonTextNode(button);
    if (textNode) {
        textNode.textContent = ` ${payload.label}`;
    }
};

const initAsyncReactions = () => {
    document.querySelectorAll('form').forEach((form) => {
        const button = form.querySelector('.star-button, .heart-button');

        if (!button || form.dataset.reactionBound === 'true') {
            return;
        }

        form.dataset.reactionBound = 'true';
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const action = form.getAttribute('action');
            if (!action) {
                return;
            }

            const relatedButtons = [...document.querySelectorAll('form')]
                .filter((candidate) => candidate.getAttribute('action') === action)
                .flatMap((candidate) => [...candidate.querySelectorAll('.star-button, .heart-button')]);
            relatedButtons.forEach((relatedButton) => {
                relatedButton.disabled = true;
                relatedButton.setAttribute('aria-busy', 'true');
            });

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const payload = await response.json();
                if (!response.ok) {
                    throw new Error(payload.error || 'No se pudo guardar la reaccion.');
                }

                relatedButtons.forEach((relatedButton) => updateReactionButton(relatedButton, payload));
            } catch (error) {
                window.alert(error.message || 'No se pudo guardar la reaccion.');
            } finally {
                relatedButtons.forEach((relatedButton) => {
                    relatedButton.disabled = false;
                    relatedButton.removeAttribute('aria-busy');
                });
            }
        });
    });
};

const initMotionCards = () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce), (pointer: coarse)').matches) {
        return;
    }

    document.querySelectorAll('.publication-card, .menu-card, .dish-feature-grid article, .contact-icon').forEach((card) => {
        if (card.dataset.motionBound === 'true') {
            return;
        }

        card.dataset.motionBound = 'true';
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - 0.5) * 10;
            const y = ((event.clientY - rect.top) / rect.height - 0.5) * -10;

            card.style.setProperty('--tilt-x', `${y.toFixed(2)}deg`);
            card.style.setProperty('--tilt-y', `${x.toFixed(2)}deg`);
            card.style.setProperty('--spot-x', `${event.clientX - rect.left}px`);
            card.style.setProperty('--spot-y', `${event.clientY - rect.top}px`);
            card.classList.add('is-tilting');
        });

        card.addEventListener('pointerleave', () => {
            card.classList.remove('is-tilting');
            card.style.removeProperty('--tilt-x');
            card.style.removeProperty('--tilt-y');
            card.style.removeProperty('--spot-x');
            card.style.removeProperty('--spot-y');
        });
    });
};

let disconnectHomeObservers = () => {};

const initHomeScrollSections = () => {
    disconnectHomeObservers();
    const sections = [...document.querySelectorAll('[data-scroll-section]')];
    const navLinks = [...document.querySelectorAll('.glass-nav .nav-link[href^="#"]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let activeSection = null;
    let frameRequest = null;

    document.documentElement.classList.toggle('has-home-scroll', sections.length > 0);

    if (sections.length === 0) {
        return;
    }

    const activateSection = (activeSection) => {
        navLinks.forEach((link) => {
            const targetId = link.getAttribute('href')?.slice(1);
            const isActive = targetId === activeSection.id;
            link.classList.toggle('is-active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const setActiveSection = (section) => {
        if (!section || section === activeSection) {
            return;
        }

        activeSection = section;
        const activeIndex = sections.indexOf(section);
        sections.forEach((candidate, index) => {
            candidate.classList.toggle('is-in-view', candidate === section || reducedMotion.matches);
            candidate.classList.toggle('is-before-active', index < activeIndex);
            candidate.classList.toggle('is-after-active', index > activeIndex);
        });
        activateSection(section);
    };

    const findBestSection = () => {
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const viewportCenter = viewportHeight * 0.48;

        return sections.reduce((best, section) => {
            const rect = section.getBoundingClientRect();
            const visibleTop = Math.max(rect.top, 0);
            const visibleBottom = Math.min(rect.bottom, viewportHeight);
            const visibleHeight = Math.max(0, visibleBottom - visibleTop);
            const centerDistance = Math.abs((rect.top + rect.bottom) / 2 - viewportCenter);
            const score = visibleHeight * 2 - centerDistance * 0.35;

            if (!best || score > best.score) {
                return { section, score };
            }

            return best;
        }, null)?.section || sections[0];
    };

    const updateActiveSection = () => {
        frameRequest = null;
        setActiveSection(findBestSection());
    };

    const requestActiveSectionUpdate = () => {
        if (frameRequest) {
            return;
        }

        frameRequest = window.requestAnimationFrame(updateActiveSection);
    };

    const sectionObserver = new IntersectionObserver(requestActiveSectionUpdate, {
        rootMargin: '-12% 0px -12% 0px',
        threshold: [0, 0.2, 0.45, 0.7, 1],
    });

    sections.forEach((section) => {
        sectionObserver.observe(section);
        if (reducedMotion.matches) {
            section.classList.add('is-in-view');
        }
    });
    setActiveSection(sections.find((section) => section.classList.contains('is-in-view')) || findBestSection());
    requestActiveSectionUpdate();
    window.addEventListener('scroll', requestActiveSectionUpdate, { passive: true });
    window.addEventListener('resize', requestActiveSectionUpdate);
    disconnectHomeObservers = () => {
        sectionObserver.disconnect();
        window.removeEventListener('scroll', requestActiveSectionUpdate);
        window.removeEventListener('resize', requestActiveSectionUpdate);
        if (frameRequest) {
            window.cancelAnimationFrame(frameRequest);
            frameRequest = null;
        }
    };

    document.querySelectorAll('.glass-nav .nav-link[href^="#"], .hero-discover[href^="#"], .hero-actions a[href^="#"]').forEach((link) => {
        if (link.dataset.scrollBound === 'true') {
            return;
        }

        link.dataset.scrollBound = 'true';
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href')?.slice(1);
            const target = targetId ? document.getElementById(targetId) : null;

            if (!target) {
                return;
            }

            event.preventDefault();
            setActiveSection(target);
            target.scrollIntoView({ behavior: reducedMotion.matches ? 'instant' : 'smooth', block: 'start' });
            window.history.replaceState(null, '', `#${target.id}`);

            const navbarCollapse = link.closest('.navbar-collapse');
            const bootstrapCollapse = navbarCollapse && window.bootstrap?.Collapse.getOrCreateInstance(navbarCollapse, { toggle: false });
            bootstrapCollapse?.hide();
            navbarCollapse?.classList.remove('show');
            document.querySelector(`[data-main-nav-toggle][aria-controls="${navbarCollapse?.id}"]`)?.setAttribute('aria-expanded', 'false');
        });
    });
};

const initMobileNavToggle = () => {
    document.querySelectorAll('[data-main-nav-toggle]').forEach((button) => {
        if (button.dataset.mobileNavBound === 'true') {
            return;
        }

        const nav = document.getElementById(button.getAttribute('aria-controls'));
        if (!nav) {
            return;
        }

        button.dataset.mobileNavBound = 'true';
        button.addEventListener('click', () => {
            const shouldOpen = !nav.classList.contains('show');
            nav.classList.toggle('show', shouldOpen);
            button.setAttribute('aria-expanded', String(shouldOpen));
        });
    });
};

const boundPasswordToggles = new WeakSet();

const initPasswordToggles = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        if (boundPasswordToggles.has(button)) {
            return;
        }
        boundPasswordToggles.add(button);
        button.addEventListener('click', () => {
            const input = document.getElementById(button.getAttribute('aria-controls'));
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(reveal));
            button.setAttribute('aria-label', reveal ? 'Ocultar contrase\u00f1a' : 'Mostrar contrase\u00f1a');
            button.title = button.getAttribute('aria-label');
            button.querySelector('i').className = reveal ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
};

const initDeleteConfirm = () => {
    document.querySelectorAll('[data-confirm-delete]').forEach((deleteForm) => {
        if (deleteForm.dataset.confirmBound === 'true') {
            return;
        }

        deleteForm.dataset.confirmBound = 'true';
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('Deseas eliminar este elemento?')) {
                event.preventDefault();
            }
        });
    });
};

const initModalKeyboardClose = () => {
    if (window.lasTablasModalKeyboardBound) {
        return;
    }

    window.lasTablasModalKeyboardBound = true;
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeRestaurantModal(document.querySelector('.restaurant-modal.show'));
    });
};

const initLasTablas = () => {
    initHeroCarousel();
    initTopMenuCarousel();
    initFanCarousels();
    initPublicationSearch();
    initViewedNovelties();
    updateCountdowns();
    initMenuFilters();
    initMenuCategorySelects();
    initInteractiveCards();
    initPublicationModalButtons();
    initHashModals();
    initPhoneCopy();
    initAsyncReactions();
    initMotionCards();
    initHomeScrollSections();
    initMobileNavToggle();
    initModalKeyboardClose();
    initPasswordToggles();
    initDeleteConfirm();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLasTablas);
} else {
    initLasTablas();
}

document.addEventListener('turbo:load', initLasTablas);

if (!window.lasTablasCountdownInterval) {
    window.lasTablasCountdownInterval = window.setInterval(updateCountdowns, 60000);
}
