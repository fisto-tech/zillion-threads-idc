// ============================================
// DYNAMIC PRODUCT SWITCHER UTILITY
// ============================================
const activeSwitchers = [];

/**
 * Check if the page element is currently visible in the DOM
 */
function isPageVisible(pageElement) {
    const rect = pageElement.getBoundingClientRect();
    const style = window.getComputedStyle(pageElement);
    const parentStyle = window.getComputedStyle(pageElement.parentElement || pageElement);

    return rect.width > 0 &&
        rect.height > 0 &&
        style.display !== 'none' &&
        style.visibility !== 'hidden' &&
        parentStyle.display !== 'none' &&
        parentStyle.visibility !== 'hidden';
}

/**
 * Initialize a switcher on a given page
 */
function initGenericSwitcher(pageElement, buttons) {
    let currentIdx = 0;
    let timer = null;

    // Preload all model variant images for instant smooth transitions
    buttons.forEach((btn) => {
        ['image', 'text', 'spec', 'config', 'pos1', 'pos2', 'pos3'].forEach(key => {
            const src = btn.dataset[key];
            if (src) {
                const img = new Image();
                img.src = src;
            }
        });
    });

    const selectModel = (index, isManual = false, force = false) => {
        if (index === currentIdx && !isManual && !force) return;

        const btn = buttons[index];
        if (!btn) return;
        currentIdx = index;

        // Toggle active-cot-btn class
        buttons.forEach((b, idx) => {
            if (idx === index) {
                b.classList.add('active-cot-btn');
            } else {
                b.classList.remove('active-cot-btn');
            }
        });

        // Find target images scoped inside the current page only
        const textEl = pageElement.querySelector('.cot-text, #cot-text');
        const imgEl = pageElement.querySelector('.cot-image, #cot-image');
        const specEl = pageElement.querySelector('.cot-spec, #cot-spec');
        const configEl = pageElement.querySelector('.cot-config, #cot-config');
        const pos1El = pageElement.querySelector('.cot-pos1, #cot-pos1');
        const pos2El = pageElement.querySelector('.cot-pos2, #cot-pos2');
        const pos3El = pageElement.querySelector('.cot-pos3, #cot-pos3');

        const els = [textEl, imgEl, specEl, configEl, pos1El, pos2El, pos3El].filter(Boolean);

        const swapSources = () => {
            if (textEl && btn.dataset.text) textEl.src = btn.dataset.text;
            if (imgEl && btn.dataset.image) imgEl.src = btn.dataset.image;
            if (specEl && btn.dataset.spec) specEl.src = btn.dataset.spec;
            if (configEl && btn.dataset.config) configEl.src = btn.dataset.config;

            if (pos1El) {
                if (btn.dataset.pos1) {
                    pos1El.src = btn.dataset.pos1;
                    pos1El.classList.remove('hidden');
                } else {
                    pos1El.classList.add('hidden');
                }
            }

            if (pos2El) {
                if (btn.dataset.pos2) {
                    pos2El.src = btn.dataset.pos2;
                    pos2El.classList.remove('hidden');
                } else {
                    pos2El.classList.add('hidden');
                }
            }

            if (pos3El) {
                if (btn.dataset.pos3) {
                    pos3El.src = btn.dataset.pos3;
                    pos3El.classList.remove('hidden');
                } else {
                    pos3El.classList.add('hidden');
                }
            }
        };

        // Smooth GSAP animation if available, fallback to CSS smooth fade
        if (window.gsap && els.length > 0) {
            gsap.to(els, {
                opacity: 0,
                scale: 0.94,
                duration: 0.2,
                ease: "power2.in",
                onComplete: () => {
                    swapSources();
                    gsap.to(els, {
                        opacity: 1,
                        scale: 1,
                        duration: 0.35,
                        ease: "power2.out"
                    });
                }
            });
        } else {
            els.forEach(el => el.classList.add('cot-fade-out'));
            setTimeout(() => {
                swapSources();
                els.forEach(el => el.classList.remove('cot-fade-out'));
            }, 300);
        }

        if (isManual || force) {
            startTimer(); // reset rotation schedule
        }
    };

    const startTimer = () => {
        stopTimer();
        timer = setInterval(() => {
            if (window.$ && $('#flipbook').length) {
                const currentView = $('#flipbook').turn('view');
                const wrapper = pageElement.closest('.page-wrapper');
                const pageIndex = wrapper ? parseInt(wrapper.getAttribute('page')) : -1;

                if (currentView.includes(pageIndex)) {
                    const nextIdx = (currentIdx + 1) % buttons.length;
                    selectModel(nextIdx, false);
                } else {
                    stopTimer();
                }
            }
        }, 4000);
    };

    const stopTimer = () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    };

    // Bind click & touchstart event listeners
    buttons.forEach((btn, index) => {
        const handler = (e) => {
            e.preventDefault();
            e.stopPropagation();
            selectModel(index, true);
        };
        btn.addEventListener('click', handler);
        btn.addEventListener('touchstart', handler, { passive: false });
    });

    const switcherInstance = {
        pageElement,
        isActive: false,
        startTimer,
        stopTimer,
        selectModel,
        reset: () => {
            selectModel(0, false, true);
        }
    };
    activeSwitchers.push(switcherInstance);

    startTimer();
}

/**
 * Control all active switcher timers
 */
function startAllSwitcherTimers() {
    activeSwitchers.forEach(s => s.startTimer());
}

function stopAllSwitcherTimers() {
    activeSwitchers.forEach(s => s.stopTimer());
}

// Auto-discover switcher elements on ready and on page turns (for dynamically loaded pages)
$(document).ready(function () {
    const discoverAndInit = () => {
        const pages = document.querySelectorAll('#flipbook .page');
        pages.forEach((page) => {
            const buttons = page.querySelectorAll('.cot-btn');
            if (buttons.length > 0 && !page.dataset.switcherInitialized) {
                page.dataset.switcherInitialized = 'true';
                initGenericSwitcher(page, buttons);
            }
        });
    };

    const triggerPageSwitchers = () => {
        if (!window.$ || !$('#flipbook').length) return;
        const currentView = $('#flipbook').turn('view');

        // Clean up switchers referencing detached DOM elements
        const activeSwitchersClean = [];
        activeSwitchers.forEach(s => {
            if (document.body.contains(s.pageElement)) {
                activeSwitchersClean.push(s);
            } else {
                s.stopTimer();
            }
        });
        activeSwitchers.length = 0;
        activeSwitchers.push(...activeSwitchersClean);

        activeSwitchers.forEach(switcher => {
            const wrapper = switcher.pageElement.closest('.page-wrapper');
            const pageIndex = wrapper ? parseInt(wrapper.getAttribute('page')) : -1;

            if (currentView.includes(pageIndex)) {
                if (!switcher.isActive) {
                    switcher.isActive = true;
                    switcher.reset();
                }
            } else {
                switcher.isActive = false;
                switcher.stopTimer();
            }
        });
    };

    discoverAndInit();
    setTimeout(triggerPageSwitchers, 600);

    // Re-check when Turn.js dynamically creates/reveals pages
    if (window.$ && $('#flipbook').length) {
        // Sync first-page / last-page classes immediately when turning starts
        $('#flipbook').on('turning', function (event, page) {
            const viewer = document.getElementById('viewer');
            const appEl = document.querySelector('.catalog-app');
            const totalPages = $('#flipbook').turn('pages');
            
            const addClass = (cls) => {
                if (viewer) viewer.classList.add(cls);
                if (appEl) appEl.classList.add(cls);
                document.body.classList.add(cls);
            };
            const removeClass = (cls) => {
                if (viewer) viewer.classList.remove(cls);
                if (appEl) appEl.classList.remove(cls);
                document.body.classList.remove(cls);
            };

            if (page === 1) {
                addClass('first-page');
                removeClass('last-page');
            } else if (page === totalPages) {
                addClass('last-page');
                removeClass('first-page');
            } else {
                removeClass('first-page');
                removeClass('last-page');
            }
        });

        $('#flipbook').on('turned', function (event, page) {
            discoverAndInit();
            triggerPageSwitchers();
        });
    }
});

// ============================================
// ============================================
// 3D EXPERIENCE ANIMATION MANAGED VIA SCRIPT.JS
// ============================================

// ============================================
// DYNAMIC FLIPBOOK EDGE NAVIGATION ARROW POSITIONING
// ============================================

function getVisiblePageWrappers() {
    let wrappers = [];

    if (window.$ && $('#flipbook').length && typeof $('#flipbook').turn === 'function') {
        try {
            const view = $('#flipbook').turn('view');
            if (Array.isArray(view)) {
                view.filter(p => p > 0).forEach(p => {
                    const el = document.querySelector(`#flipbook .page-wrapper[page="${p}"]`);
                    if (el) wrappers.push(el);
                });
            }
        } catch (e) {}
    }

    if (wrappers.length === 0) {
        const allWrappers = document.querySelectorAll('#flipbook .page-wrapper');
        allWrappers.forEach(el => {
            const rect = el.getBoundingClientRect();
            const style = window.getComputedStyle(el);
            if (rect.width > 0 && rect.height > 0 && style.display !== 'none' && style.visibility !== 'hidden') {
                wrappers.push(el);
            }
        });
    }

    if (wrappers.length === 0) {
        const flipbookEl = document.getElementById('flipbook');
        if (flipbookEl) {
            const rect = flipbookEl.getBoundingClientRect();
            if (rect.width > 0 && rect.height > 0) {
                wrappers.push(flipbookEl);
            }
        }
    }

    return wrappers;
}

function updateDesktopArrowPositions() {
    const prevArrow = document.querySelector('.prev-arrow, .ui-arrow-previous-page');
    const nextArrow = document.querySelector('.next-arrow, .ui-arrow-next-page');

    if (!prevArrow && !nextArrow) return;

    // Mobile screens: reset inline positioning so mobile CSS layout takes effect
    if (window.innerWidth <= 768) {
        [prevArrow, nextArrow].forEach(arrow => {
            if (arrow) {
                arrow.style.removeProperty('left');
                arrow.style.removeProperty('right');
                arrow.style.removeProperty('top');
                arrow.style.removeProperty('transform');
                arrow.style.removeProperty('position');
                arrow.style.removeProperty('opacity');
                arrow.style.removeProperty('pointer-events');
            }
        });
        return;
    }

    const wrappers = getVisiblePageWrappers();
    if (!wrappers || wrappers.length === 0) return;

    let minLeft = Infinity;
    let maxRight = -Infinity;
    let minTop = Infinity;
    let maxBottom = -Infinity;

    wrappers.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
            if (rect.left < minLeft) minLeft = rect.left;
            if (rect.right > maxRight) maxRight = rect.right;
            if (rect.top < minTop) minTop = rect.top;
            if (rect.bottom > maxBottom) maxBottom = rect.bottom;
        }
    });

    if (minLeft === Infinity || maxRight === -Infinity) return;

    // Determine current view page numbers & total pages
    let validPages = [];
    let totalPages = 40;
    if (window.$ && $('#flipbook').length && typeof $('#flipbook').turn === 'function') {
        try {
            const currentView = $('#flipbook').turn('view') || [];
            validPages = currentView.filter(p => p > 0);
            totalPages = $('#flipbook').turn('pages') || 40;
        } catch (e) {}
    }

    // Fallback check DOM body/viewer classes
    const bodyClass = document.body.className || '';
    const isFirstPage = (validPages.length === 1 && validPages[0] === 1) || bodyClass.includes('first-page');
    const isLastPage = (validPages.length === 1 && validPages[0] === totalPages) || bodyClass.includes('last-page');

    // Single page width to project full double-spread boundaries
    const singlePageWidth = maxRight - minLeft;

    let fullSpreadLeft = minLeft;
    let fullSpreadRight = maxRight;

    if (isFirstPage) {
        // Page 1 is on the right side of the spread
        fullSpreadLeft = minLeft - singlePageWidth;
        fullSpreadRight = maxRight;
    } else if (isLastPage) {
        // Last page is on the left side of the spread
        fullSpreadLeft = minLeft;
        fullSpreadRight = maxRight + singlePageWidth;
    }

    const centerY = (minTop + maxBottom) / 2;
    const gap = 15; // gap in px outside flipbook edge

    // PREVIOUS ARROW (Left arrow)
    if (prevArrow) {
        const arrowWidth = prevArrow.offsetWidth || 45;

        if (isFirstPage) {
            // Animate off to left outside desktop screen
            prevArrow.style.setProperty('position', 'fixed', 'important');
            prevArrow.style.setProperty('left', '-100px', 'important');
            prevArrow.style.setProperty('right', 'auto', 'important');
            prevArrow.style.setProperty('top', Math.round(centerY) + 'px', 'important');
            prevArrow.style.setProperty('transform', 'translateY(-50%)', 'important');
            prevArrow.style.setProperty('opacity', '0', 'important');
            prevArrow.style.setProperty('pointer-events', 'none', 'important');
        } else {
            let prevLeft = Math.round(fullSpreadLeft - arrowWidth - gap);
            if (prevLeft < 10) prevLeft = 10;

            prevArrow.style.setProperty('position', 'fixed', 'important');
            prevArrow.style.setProperty('left', prevLeft + 'px', 'important');
            prevArrow.style.setProperty('right', 'auto', 'important');
            prevArrow.style.setProperty('top', Math.round(centerY) + 'px', 'important');
            prevArrow.style.setProperty('transform', 'translateY(-50%)', 'important');
            prevArrow.style.setProperty('opacity', '1', 'important');
            prevArrow.style.setProperty('pointer-events', 'auto', 'important');
        }
    }

    // NEXT ARROW (Right arrow)
    if (nextArrow) {
        const arrowWidth = nextArrow.offsetWidth || 45;

        if (isLastPage) {
            // Animate off to right outside desktop screen
            nextArrow.style.setProperty('position', 'fixed', 'important');
            nextArrow.style.setProperty('left', (window.innerWidth + 100) + 'px', 'important');
            nextArrow.style.setProperty('right', 'auto', 'important');
            nextArrow.style.setProperty('top', Math.round(centerY) + 'px', 'important');
            nextArrow.style.setProperty('transform', 'translateY(-50%)', 'important');
            nextArrow.style.setProperty('opacity', '0', 'important');
            nextArrow.style.setProperty('pointer-events', 'none', 'important');
        } else {
            let nextLeft = Math.round(fullSpreadRight + gap);
            if (nextLeft > window.innerWidth - arrowWidth - 10) {
                nextLeft = Math.round(window.innerWidth - arrowWidth - 10);
            }

            nextArrow.style.setProperty('position', 'fixed', 'important');
            nextArrow.style.setProperty('left', nextLeft + 'px', 'important');
            nextArrow.style.setProperty('right', 'auto', 'important');
            nextArrow.style.setProperty('top', Math.round(centerY) + 'px', 'important');
            nextArrow.style.setProperty('transform', 'translateY(-50%)', 'important');
            nextArrow.style.setProperty('opacity', '1', 'important');
            nextArrow.style.setProperty('pointer-events', 'auto', 'important');
        }
    }
}

let turnAnimationRafId = null;
function animateArrowPositionsDuringTurn() {
    if (turnAnimationRafId) cancelAnimationFrame(turnAnimationRafId);
    const startTime = performance.now();
    const duration = 800; // ms matching Turn.js flip animation duration

    function frame(now) {
        updateDesktopArrowPositions();
        if (now - startTime < duration) {
            turnAnimationRafId = requestAnimationFrame(frame);
        }
    }
    turnAnimationRafId = requestAnimationFrame(frame);
}

// Global window/document listeners
window.addEventListener('resize', updateDesktopArrowPositions);
window.addEventListener('scroll', updateDesktopArrowPositions, true);
['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
    document.addEventListener(evt, () => setTimeout(updateDesktopArrowPositions, 100));
});

$(document).ready(function () {
    updateDesktopArrowPositions();
    setTimeout(updateDesktopArrowPositions, 100);
    setTimeout(updateDesktopArrowPositions, 300);
    setTimeout(updateDesktopArrowPositions, 600);
    setTimeout(updateDesktopArrowPositions, 1000);

    if (window.$ && $('#flipbook').length) {
        $('#flipbook').on('turning turned zoom zoomed start end', function () {
            updateDesktopArrowPositions();
            animateArrowPositionsDuringTurn();
        });
    }
});

// ============================================
// DYNAMIC CATEGORY BACKGROUND SWITCHER (SMOOTH CROSSFADE)
// ============================================

const categoryBackgrounds = {
    ranges: [
        { start: 1, end: 1, bg: '../global%20assets/bottom-navbar/desktop-background-image.webp', name: 'Home' },
        { start: 2, end: 3, bg: '../global%20assets/bottom-navbar/desktop-background-image.webp', name: 'About Us' },
        { start: 4, end: 5, bg: '../global%20assets/bottom-navbar/desktop-background-image.webp', name: 'Products' },
        { start: 5, end: 5, bg: '../global%20assets/bottom-navbar/desktop-background-image.webp', name: '3D Experience' },
        { start: 6, end: 11, bg: '../global%20assets/Images/background-images/ward-furniture-image.webp', name: 'Ward Furniture' },
        { start: 12, end: 13, bg: '../global%20assets/Images/background-images/icu-critical-care-image.webp', name: 'ICU & Critical Care' },
        { start: 14, end: 15, bg: '../global%20assets/Images/background-images/labor-maternity-image.webp', name: 'Labour & Maternity' },
        { start: 16, end: 21, bg: '../global%20assets/Images/background-images/emergency-patient-transfer-image.webp', name: 'Emergency & Patient Transfer' },
        { start: 22, end: 29, bg: '../global%20assets/Images/background-images/medical-trolley-image.webp', name: 'Medical Trolleys' },
        { start: 30, end: 33, bg: '../global%20assets/Images/background-images/examination-consultation-image.webp', name: 'Examination & Consultation' },
        { start: 34, end: 35, bg: '../global%20assets/Images/background-images/stainless-steal-ward-accessories-image.webp', name: 'Stainless Steel Furniture & Ward Accessories' },
        { start: 36, end: 37, bg: '../global%20assets/Images/background-images/general-furniture-image.webp', name: 'General Furniture' },
        { start: 38, end: 39, bg: '../global%20assets/Images/background-images/accessories-image.webp', name: 'Accessories' },
        { start: 40, end: 40, bg: '../global%20assets/bottom-navbar/desktop-background-image.webp', name: 'Contact Us' }
    ],
    defaultBg: '../global%20assets/bottom-navbar/desktop-background-image.webp'
};

let currentBgUrl = '';
let activeBgLayerNum = 1;

function getCategoryBgForPage(pageNum) {
    const isMobile = window.innerWidth <= 768;

    // On mobile: product pages (6–39) use their category-specific image,
    // all other pages use the mobile background image.
    if (isMobile) {
        if (pageNum >= 6 && pageNum <= 39) {
            for (let r of categoryBackgrounds.ranges) {
                if (pageNum >= r.start && pageNum <= r.end) {
                    return r.bg;
                }
            }
        }
        return '../global%20assets/bottom-navbar/mobile-background-image.webp';
    }

    for (let r of categoryBackgrounds.ranges) {
        if (pageNum >= r.start && pageNum <= r.end) {
            return r.bg;
        }
    }
    return categoryBackgrounds.defaultBg;
}

function updateCategoryBackground(targetPage) {
    let pageNum = targetPage;
    if (!pageNum && window.$ && $('#flipbook').length && typeof $('#flipbook').turn === 'function') {
        const view = $('#flipbook').turn('view');
        if (Array.isArray(view)) {
            const valid = view.filter(p => p > 0);
            if (valid.length > 0) pageNum = valid[0];
        }
        if (!pageNum) pageNum = $('#flipbook').turn('page') || 1;
    }
    if (!pageNum) pageNum = 1;

    const targetBg = getCategoryBgForPage(pageNum);
    if (!targetBg || targetBg === currentBgUrl) return;

    const layer1 = document.getElementById('bgLayer1');
    const layer2 = document.getElementById('bgLayer2');
    if (!layer1 || !layer2) return;

    const currentLayer = activeBgLayerNum === 1 ? layer1 : layer2;
    const nextLayer = activeBgLayerNum === 1 ? layer2 : layer1;

    // Smooth crossfade using preloaded image
    const img = new Image();
    img.onload = () => {
        nextLayer.style.backgroundImage = `url('${targetBg}')`;
        nextLayer.classList.add('active');
        currentLayer.classList.remove('active');
        currentBgUrl = targetBg;
        activeBgLayerNum = activeBgLayerNum === 1 ? 2 : 1;
    };
    img.src = targetBg;
}

// Global utility method so any category's background image can be updated dynamically
window.setCategoryBackground = function(categoryNameOrPage, imagePath) {
    if (typeof categoryNameOrPage === 'number') {
        const item = categoryBackgrounds.ranges.find(r => categoryNameOrPage >= r.start && categoryNameOrPage <= r.end);
        if (item) item.bg = imagePath;
    } else if (typeof categoryNameOrPage === 'string') {
        const item = categoryBackgrounds.ranges.find(r => r.name.toLowerCase() === categoryNameOrPage.toLowerCase());
        if (item) item.bg = imagePath;
    }
    currentBgUrl = ''; // reset so update forces re-evaluation
    updateCategoryBackground();
};

$(document).ready(function () {
    updateCategoryBackground(1);
    setTimeout(() => updateCategoryBackground(), 300);
    setTimeout(() => updateCategoryBackground(), 600);

    if (window.$ && $('#flipbook').length) {
        $('#flipbook').on('turning turned', function (event, page) {
            updateCategoryBackground(page);
        });
    }
});


