const navToggle = document.getElementById('navToggle');
const tocNav = document.getElementById('tocNav');
const navOverlay = document.getElementById('navOverlay');

function openMenu() {
    // Automatically close Thumbnail popup/drawer if open
    if (typeof window.closeThumbDrawer === 'function') {
        window.closeThumbDrawer();
    } else {
        const thumbDrawer = document.getElementById('thumbnailDrawer');
        if (thumbDrawer) thumbDrawer.classList.remove('show');
    }

    navToggle.classList.add('open');
    tocNav.classList.add('show');
    navOverlay.classList.add('show');
    navToggle.setAttribute('aria-expanded', 'true');

    // Toggle body class to lower z-index of navigation arrows
    document.body.classList.add('toc-active');

    // Hide brand logo when menu is open to prevent overlapping
    const logo = document.querySelector('.top-left-brand');
    if (logo) {
        logo.style.opacity = '0';
        logo.style.pointerEvents = 'none';
    }

    // Hide thumbnails behind TOC
    const thumbs = document.querySelector('.thumbnail-preview-container');
    if (thumbs) thumbs.style.zIndex = '0';
}

function closeMenu() {
    navToggle.classList.remove('open');
    tocNav.classList.remove('show');
    navOverlay.classList.remove('show');
    navToggle.setAttribute('aria-expanded', 'false');

    // Restore body class to restore z-index of navigation arrows
    document.body.classList.remove('toc-active');

    // Restore brand logo
    const logo = document.querySelector('.top-left-brand');
    if (logo) {
        logo.style.opacity = '1';
        logo.style.pointerEvents = 'auto';
    }

    // Restore thumbnails
    const thumbs = document.querySelector('.thumbnail-preview-container');
    if (thumbs) thumbs.style.zIndex = '999999';
}

navToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    if (tocNav.classList.contains('show')) closeMenu();
    else openMenu();
});

const closeTocBtn = document.getElementById('closeTocBtn');
if (closeTocBtn) {
    closeTocBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeMenu();
    });
}

// Click outside to close
document.addEventListener('click', (e) => {
    const isMenuVisible = tocNav.classList.contains('show');
    const isClickInsideMenu = tocNav.contains(e.target);
    const isClickOnToggle = navToggle.contains(e.target);

    if (isMenuVisible && !isClickInsideMenu && !isClickOnToggle) {
        closeMenu();
    }
});

// Prevent clicks inside menu from closing it
tocNav.addEventListener('click', (e) => {
    e.stopPropagation();
});

// Close when clicking overlay
navOverlay.addEventListener('click', closeMenu);

// Close when clicking menu links
document.querySelectorAll('.toc-list a').forEach(link => {
    link.addEventListener('click', closeMenu);
});

// ESC key to close
document.addEventListener('keydown', (e) => {
    if (e.key === "Escape" && tocNav.classList.contains('show')) {
        closeMenu();
    }
});

function updateActiveThumbnail(currentPage) {
    if (window.$ && $('#flipbook').turn) {
        const currentView = $('#flipbook').turn('view', currentPage);
        document.querySelectorAll('.tb-link').forEach(item => {
            const itemPage = parseInt(item.dataset.page);
            const isActive = currentView.indexOf(itemPage) !== -1 || itemPage === currentPage;
            item.classList.toggle('active', isActive);
        });
    } else {
        document.querySelectorAll('.tb-link').forEach(item => {
            const itemPage = parseInt(item.dataset.page);
            item.classList.toggle('active', itemPage === currentPage);
        });
    }
}

// Unified handler (works for click + touch + pointer)
function handleThumbnailActivate(e) {
    e.preventDefault();
    e.stopPropagation();

    const link = e.currentTarget;
    const pageNumber = parseInt(link.dataset.page);
    const audioPath = link.dataset.audioPath;

    // Play audio
    if (audioPath) {
        const audio = new Audio(audioPath);
        audio.play().catch(err => console.log('Audio play failed:', err));
    }

    // Navigate flipbook with a small delay
    if (window.$ && $('#flipbook').turn) {
        setTimeout(() => {
            $('#flipbook').turn('page', pageNumber);
        }, 200);
    }

    // Update active thumbnail
    updateActiveThumbnail(pageNumber);

    closeMenu();
}

// Attach events
document.querySelectorAll('.tb-link').forEach(link => {

    // Pointer Events (best â€“ covers mouse + touch + pen)
    link.addEventListener('pointerup', handleThumbnailActivate, {
        passive: false
    });

    // Fallback for older browsers
    // link.addEventListener('click', handleThumbnailActivate);
});


// Detect page change automatically in flipbook
$('#flipbook').bind("turned", function (event, page) {
    updateActiveThumbnail(page);
});

// Run once on page load
document.addEventListener("DOMContentLoaded", () => {
    const firstPage = $('#flipbook').turn('page');
    updateActiveThumbnail(firstPage);
});






// ************************bottom thumnail code start ------------------------------------- -->
const navToggle1 = document.getElementById('navToggle1');
const tocNav1 = document.getElementById('tocNav1');
const navOverlay1 = document.getElementById('navOverlay1');
function openMenu1() {
    if (typeof window.closeThumbDrawer === 'function') {
        window.closeThumbDrawer();
    } else {
        const thumbDrawer = document.getElementById('thumbnailDrawer');
        if (thumbDrawer) thumbDrawer.classList.remove('show');
    }
    navToggle1.classList.add('open');
    tocNav1.classList.add('show');
    navOverlay1.classList.add('show');
    navToggle1.setAttribute('aria-expanded', 'true');
} 1
function closeMenu1() {
    navToggle1.classList.remove('open');
    tocNav1.classList.remove('show');
    navOverlay1.classList.remove('show');
    navToggle1.setAttribute('aria-expanded', 'false');
}
navToggle1.addEventListener('click', function () {
    if (tocNav1.classList.contains('show')) closeMenu1();
    else openMenu1();
});
navOverlay1.addEventListener('click', closeMenu1);
const closeTocBtn1 = document.getElementById('closeTocBtn1');
if (closeTocBtn1) {
    closeTocBtn1.addEventListener('click', function (e) {
        e.stopPropagation();
        closeMenu1();
    });
}
document.querySelectorAll('#tocNav1 .toc-list a').forEach(link => {
    link.addEventListener('click', closeMenu1);
});
// Keyboard: ESC to close
document.addEventListener('keydown', function (e) {
    if (e.key === "Escape") closeMenu1();
});




// // Set your current page number (dynamic)
// let currentPage = 4; // example: you are on page 4-5

// document.addEventListener("DOMContentLoaded", () => {

//     // Apply active based on current page
//     document.querySelectorAll(".tb-link").forEach(item => {
//         if (item.getAttribute("data-page") == currentPage) {
//             item.classList.add("active");
//         }
//     });

//     // On click update active thumbnail
//     document.querySelectorAll('.tb-link').forEach(item => {
//         item.addEventListener('click', function () {

//             // Remove previous active
//             document.querySelectorAll('.tb-link')
//                 .forEach(el => el.classList.remove('active'));

//             // Add active to clicked item
//             this.classList.add('active');

//             // Update currentPage variable
//             currentPage = this.getAttribute("data-page");
//         });
//     });
// });



// ************************bottom thumnail code end  ------------------------------------- -->





// ****************************share button navbar functionality start************************** 

var triggerIcon = document.getElementById('navMenuBarMobile');
var overlay = document.getElementById('navMobileOverlay');

function setIcon(iName) {
    if (iName == 'menu') {
        triggerIcon.src = '../global%20assets/icons/doted-icon.svg';
        triggerIcon.style.scale = 1;
        triggerIcon.style.transform = 'translateY(-50%)';
    }
    else if (iName == 'close') {
        triggerIcon.src = '../global%20assets/bottom-navbar/close-icon.svg';
        triggerIcon.style.scale = .7;
        triggerIcon.style.transform = 'translateY(-65%)';
    }
    else triggerIcon.src = '';
}

function openOverlay() {
    if (overlay.classList.contains('nav-mobile-overlay--visible')) {
        overlay.classList.remove('nav-mobile-overlay--visible');
        setIcon('menu');
    }
    else {
        overlay.classList.add('nav-mobile-overlay--visible');
        setIcon('close');
    }
}

function closeOverlay() {
    overlay.classList.remove('nav-mobile-overlay--visible');
    setIcon('menu');
}

triggerIcon.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    openOverlay();
});


overlay
    .querySelector('.nav-mobile-overlay__backdrop')
    .addEventListener('click', closeOverlay);

const shareBtn = document.getElementById('shareBtn');
const navMobileShareIcon = document.getElementById('navMobileShareIcon');
const shareModal = document.getElementById('shareModal');
const shareOverlay = document.getElementById('shareOverlay');
const closeBtn = document.getElementById('closeBtn');
const shareInput = document.getElementById('shareInput');
const copyBtn = document.getElementById('copyBtn');
const copiedMsg = document.getElementById('copiedMsg');

const headerShareBn = document.getElementById('shareBn');

// Set link initially
if (shareInput) shareInput.value = "";

// Helper to compute current page URL
function getCurrentShareUrl() {
    let baseUrl = window.location.href.split('#')[0];
    // Strip /For_Desc/ path segment so the share link targets root index.html without page number
    baseUrl = baseUrl.replace(/\/For_Desc\/?/i, '/');
    return baseUrl;
}

// Open modal function - updates share input URL when opened
function showShareMenu() {
    const currentUrl = getCurrentShareUrl();
    if (shareInput) shareInput.value = currentUrl;
    if (mobileShareLinkInput) mobileShareLinkInput.value = currentUrl;

    if (shareModal) shareModal.classList.remove('hidden');
    if (shareOverlay) shareOverlay.classList.remove('hidden');
    if (shareInput) {
        shareInput.focus();
        shareInput.select();
    }
}

// Open modal listeners
if (shareBtn) shareBtn.addEventListener('click', showShareMenu);
if (headerShareBn) headerShareBn.addEventListener('click', showShareMenu);

// Close modal
const closeModal = () => {
    if (shareModal) shareModal.classList.add('hidden');
    if (shareOverlay) shareOverlay.classList.add('hidden');
};

if (closeBtn) closeBtn.addEventListener('click', closeModal);
if (shareOverlay) shareOverlay.addEventListener('click', closeModal);

// Copy link
if (copyBtn) {
    copyBtn.addEventListener('click', () => {
        const urlToCopy = (shareInput && shareInput.value) ? shareInput.value : getCurrentShareUrl();
        navigator.clipboard.writeText(urlToCopy).then(() => {
            if (copiedMsg) {
                copiedMsg.classList.remove('hidden');
                setTimeout(() => copiedMsg.classList.add('hidden'), 1500);
            }
        });
    });
}

// Social share functions
const whatsappBtn = document.getElementById('whatsappBtn');
if (whatsappBtn) {
    whatsappBtn.addEventListener('click', () => {
        const url = encodeURIComponent((shareInput && shareInput.value) ? shareInput.value : getCurrentShareUrl());
        window.open(`https://wa.me/?text=${url}`, '_blank');
    });
}

const twitterBtn = document.getElementById('twitterBtn');
if (twitterBtn) {
    twitterBtn.addEventListener('click', () => {
        const url = encodeURIComponent((shareInput && shareInput.value) ? shareInput.value : getCurrentShareUrl());
        window.open(`https://twitter.com/intent/tweet?url=${url}`, '_blank');
    });
}

const facebookBtn = document.getElementById('facebookBtn');
if (facebookBtn) {
    facebookBtn.addEventListener('click', () => {
        const url = encodeURIComponent((shareInput && shareInput.value) ? shareInput.value : getCurrentShareUrl());
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    });
}

const linkedInBtn = document.getElementById('linkedInBtn');
if (linkedInBtn) {
    linkedInBtn.addEventListener('click', () => {
        const url = encodeURIComponent((shareInput && shareInput.value) ? shareInput.value : getCurrentShareUrl());
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
    });
}

// mobile share
const mobileShareModal = document.getElementById('shareModalMobile');
const mobileShareOverlay = document.getElementById('shareOverlayMobile');
const mobileShareCloseBtn = document.getElementById('shareCloseBtnMobile');

const mobileShareLinkInput = document.getElementById('shareInputMobile');
const mobileCopyLinkBtn = document.getElementById('copyBtnMobile');
const mobileCopiedToast = document.getElementById('copiedMsgMobile');

// Set link initially
if (mobileShareLinkInput) mobileShareLinkInput.value = "";

// Open modal - use desktop share modal for mobile too
if (navMobileShareIcon) {
    navMobileShareIcon.addEventListener('click', function () {
        if (typeof closeOverlay === 'function') closeOverlay();
        showShareMenu(); // reuse the desktop share function
    });
}

// Close modal
function closeMobileShareMenu() {
    if (mobileShareModal) mobileShareModal.classList.add('hidden');
    if (mobileShareOverlay) mobileShareOverlay.classList.add('hidden');
}

if (mobileShareCloseBtn) mobileShareCloseBtn.addEventListener('click', closeMobileShareMenu);
if (mobileShareOverlay) mobileShareOverlay.addEventListener('click', closeMobileShareMenu);

// Copy link
if (mobileCopyLinkBtn) {
    mobileCopyLinkBtn.addEventListener('click', () => {
        const urlToCopy = (mobileShareLinkInput && mobileShareLinkInput.value) ? mobileShareLinkInput.value : getCurrentShareUrl();
        navigator.clipboard.writeText(urlToCopy).then(() => {
            if (mobileCopiedToast) {
                mobileCopiedToast.classList.remove('hidden');
                setTimeout(() => mobileCopiedToast.classList.add('hidden'), 1500);
            }
        });
    });
}

// Social shares
const waShareMobile = document.getElementById('waShareMobile');
if (waShareMobile) {
    waShareMobile.addEventListener('click', () => {
        const url = encodeURIComponent((mobileShareLinkInput && mobileShareLinkInput.value) ? mobileShareLinkInput.value : getCurrentShareUrl());
        window.open(`https://wa.me/?text=${url}`, '_blank');
    });
}

const twShareMobile = document.getElementById('twShareMobile');
if (twShareMobile) {
    twShareMobile.addEventListener('click', () => {
        const url = encodeURIComponent((mobileShareLinkInput && mobileShareLinkInput.value) ? mobileShareLinkInput.value : getCurrentShareUrl());
        window.open(`https://twitter.com/intent/tweet?url=${url}`, '_blank');
    });
}

const fbShareMobile = document.getElementById('fbShareMobile');
if (fbShareMobile) {
    fbShareMobile.addEventListener('click', () => {
        const url = encodeURIComponent((mobileShareLinkInput && mobileShareLinkInput.value) ? mobileShareLinkInput.value : getCurrentShareUrl());
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    });
}

const lnShareMobile = document.getElementById('lnShareMobile');
if (lnShareMobile) {
    lnShareMobile.addEventListener('click', () => {
        const url = encodeURIComponent((mobileShareLinkInput && mobileShareLinkInput.value) ? mobileShareLinkInput.value : getCurrentShareUrl());
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
    });
}

// Close on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});





// ****************************share button navbar functionality end************************** ]
// ****************************music button navbar functionality start************************** 
// ==================== MUSIC/AUDIO FUNCTIONALITY (Alternative) ====================
window.addEventListener('load', function () {
    const bgmAudio = document.getElementById('bgmAudio');
    const bgmButton = document.getElementById('bgmButton');
    const musicOnImg = document.getElementById('musicOnImg');
    const musicOffImg = document.getElementById('musicOffImg');

    const mobileAudioIcon = document.getElementById('navMobileAudioIcon');
    const mobileAudioBtn = document.getElementById('mobileAudioBtn');
    const mobileAudioStatus = document.getElementById('mobileAudioStatus');

    const MOBILE_MUSIC_ON_SRC = "../global%20assets/icons/music-on-icon.svg";
    const MOBILE_MUSIC_OFF_SRC = "../global%20assets/icons/music-off-icon.svg";

    if (bgmAudio) {
        bgmAudio.volume = 0.50;
    }

    let isPlaying = false;

    // Default icon state ON (music enabled by default)
    updateIcons(true);

    // Function to start audio default play
    function startDefaultMusic() {
        if (!bgmAudio) return;
        bgmAudio.volume = 0.50;
        const playPromise = bgmAudio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                isPlaying = true;
                updateIcons(true);
            }).catch((err) => {
                console.log('Autoplay deferred until user interaction:', err);
                // Keep icon in ON state so music starts on first interaction
                updateIcons(true);
            });
        }
    }

    startDefaultMusic();

    function updateIcons(playing) {
        // Desktop icons
        if (musicOnImg && musicOffImg) {
            if (playing) {
                musicOnImg.classList.remove("hidden");
                musicOffImg.classList.add("hidden");
            } else {
                musicOnImg.classList.add("hidden");
                musicOffImg.classList.remove("hidden");
            }
        }

        // Mobile icons
        if (mobileAudioIcon) {
            mobileAudioIcon.src = playing ? MOBILE_MUSIC_ON_SRC : MOBILE_MUSIC_OFF_SRC;
        }

        const mobileDropdownAudioIcon = document.getElementById('mobileAudioIcon');
        if (mobileDropdownAudioIcon) {
            mobileDropdownAudioIcon.src = playing ? MOBILE_MUSIC_ON_SRC : MOBILE_MUSIC_OFF_SRC;
        }

        if (mobileAudioStatus) {
            mobileAudioStatus.textContent = playing ? 'ON' : 'OFF';
            mobileAudioStatus.classList.toggle('on', playing);
        }
    }

    function toggleMusic(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (!bgmAudio) return;

        // Check the actual audio element state rather than a variable
        if (!bgmAudio.paused && !bgmAudio.ended && bgmAudio.currentTime > 0) {
            bgmAudio.pause();
            isPlaying = false;
            updateIcons(false);
        } else {
            bgmAudio.volume = 0.50;
            const playPromise = bgmAudio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    isPlaying = true;
                    updateIcons(true);
                }).catch((error) => {
                    console.error('Audio play error:', error);
                });
            }
        }
    }

    // Event listeners
    if (bgmButton) {
        bgmButton.removeEventListener('click', toggleMusic);
        bgmButton.addEventListener('click', toggleMusic);
    }
    if (mobileAudioIcon) {
        mobileAudioIcon.removeEventListener('click', toggleMusic);
        mobileAudioIcon.addEventListener('click', toggleMusic);
    }
    if (mobileAudioBtn) {
        mobileAudioBtn.removeEventListener('click', toggleMusic);
        mobileAudioBtn.addEventListener('click', toggleMusic);
    }

    // Sync with actual audio state events
    if (bgmAudio) {
        bgmAudio.addEventListener('pause', () => {
            isPlaying = false;
            updateIcons(false);
        });

        bgmAudio.addEventListener('play', () => {
            isPlaying = true;
            updateIcons(true);
        });

        bgmAudio.addEventListener('playing', () => {
            isPlaying = true;
            updateIcons(true);
        });
    }

    // Visibility handling
    let wasPlayingBeforeHidden = false;
    document.addEventListener('visibilitychange', () => {
        if (!bgmAudio) return;
        if (document.hidden) {
            wasPlayingBeforeHidden = isPlaying;
            if (isPlaying) bgmAudio.pause();
        } else {
            if (wasPlayingBeforeHidden) {
                bgmAudio.play().catch(() => { });
            }
        }
    });

    // Auto-play music on first interaction anywhere on the document
    let hasInteracted = false;
    function playOnInteraction() {
        if (!hasInteracted && !isPlaying && bgmAudio) {
            hasInteracted = true;
            bgmAudio.play().then(() => {
                isPlaying = true;
                updateIcons(true);
            }).catch((e) => console.log('Autoplay interaction failed:', e));
        }
        document.removeEventListener('click', playOnInteraction);
        document.removeEventListener('touchstart', playOnInteraction);
        document.removeEventListener('keydown', playOnInteraction);
    }

    document.addEventListener('click', playOnInteraction);
    document.addEventListener('touchstart', playOnInteraction);
    document.addEventListener('keydown', playOnInteraction);

    window.toggleBgmMusic = toggleMusic;
});
// ****************************music button navbar functionality start************************** 



// *********************home button start**************
const goToPage1 = document.getElementById("goToPage1");

goToPage1.addEventListener("click", function () {
    if ($("#flipbook").turn) {
        $("#flipbook").turn("page", 1);
    }

    const audioPath = goToPage1.dataset.audioPath;
    if (audioPath) {
        const audio = new Audio(audioPath);
        audio.play();
    }
});


// *********************home button end**************




$('#flipbook').bind('turned', function (event, page, view) {

    // âœ… UPDATE PAGE COUNTER WITH LAST PAGE FIX
    const totalPages = $('#flipbook').turn('pages');
    const pageNoElement = document.getElementById('page-no');

    if (pageNoElement) {
        if (page === 1) {
            // First page (cover)
            pageNoElement.textContent = `1 / ${totalPages}`;
        } else if (page === totalPages) {
            // Last page (back cover) - show single number
            pageNoElement.textContent = `${totalPages} / ${totalPages}`;
        } else if (page % 2 === 0) {
            // Even page - show as spread
            pageNoElement.textContent = `${page}-${page + 1} / ${totalPages}`;
        } else {
            // Odd page - show as spread
            pageNoElement.textContent = `${page - 1}-${page} / ${totalPages}`;
        }
    }

    // Update active thumbnail (your existing code)
    updateActiveThumbnail(page);
});






// *****************************search icon code*******************************

// ==================== SEARCH MODAL FUNCTIONALITY ====================
// ==================== SEARCH MODAL - CLICK OUTSIDE TO CLOSE ====================

const searchIcon = document.querySelector('img[alt="search-icon"]');
const searchModal = document.getElementById('searchModal');
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const closeSearchModal = document.getElementById('closeSearchModal');

// Define your pages with search keywords
const pages = [
    { page: 1, title: "Home page", keywords: ["cover", "front", "home", "title", "1", "mathurams", "engineering", "idc"] },
    { page: 2, title: "About Us", keywords: ["intro", "introduction", "about us", "2", "values", "mission", "vision", "profile"] },
    { page: 3, title: "Infrastructure", keywords: ["3", "infrastructure", "facility", "manufacturing", "plant", "factory"] },
    { page: 4, title: "Table of Contents", keywords: ["4", "table of contents", "toc", "index", "chapters", "products", "categories"] },
    { page: 5, title: "3D Experience", keywords: ["5", "3d", "experience", "interactive", "virtual", "models", "icu cot", "fowler cot", "examination", "hi-lo", "labour", "semi fowler", "attender", "bedside locker", "overbed"] },
    { page: 6, title: "Semi Fowler Cot", keywords: ["6", "semi fowler cot", "ward furniture", "bed", "MF-34", "MF-35"] },
    { page: 7, title: "Fowler Cot", keywords: ["7", "fowler cot", "ward furniture", "bed", "MF-39", "MF-41"] },
    { page: 8, title: "Plain Cot", keywords: ["8", "plain cot", "ward furniture", "bed", "MF-61", "MF-62", "MF-63"] },
    { page: 9, title: "Attender Cot", keywords: ["9", "attender cot", "attender bed", "ward furniture", "MF-19", "MF-20", "MF-22"] },
    { page: 10, title: "Bed Side Locker", keywords: ["10", "bed side locker", "bedside cabinet", "locker", "cabinet", "ward furniture", "MF-01", "MF-02"] },
    { page: 11, title: "Over Bed Table", keywords: ["11", "over bed table", "cardiac table", "ward furniture", "MF-07", "MF-08", "MF-09"] },
    { page: 12, title: "ICU Cot 5 Functions", keywords: ["12", "icu cot", "5 functions", "critical care", "electric", "motor", "remote", "bed"] },
    { page: 13, title: "ICU Cot 3 Functions", keywords: ["13", "icu cot", "3 functions", "critical care", "manual", "crank", "bed"] },
    { page: 14, title: "Labour Cot", keywords: ["14", "labour cot", "maternity", "delivery bed", "MF-71", "MF-72"] },
    { page: 15, title: "Gynec Examination Couch", keywords: ["15", "gynec examination couch", "gynecology", "examination", "couch", "labour", "maternity", "MF-93"] },
    { page: 16, title: "Baby Cradle & Pediatric Cot", keywords: ["16", "baby cradle", "cradle", "pediatric cot", "child bed", "maternity", "MF-36", "MF-37", "MF-90"] },
    { page: 17, title: "Casualty Cot Cum Trolley", keywords: ["17", "casualty cot", "trolley cum cot", "emergency", "patient transfer", "trolley", "MF-38"] },
    { page: 18, title: "Transfer Trolley", keywords: ["18", "transfer trolley", "hi-lo", "stretcher", "emergency", "patient transfer", "trolley", "MF-67", "MF-68"] },
    { page: 19, title: "Hi-Lo Stretcher", keywords: ["19", "hi-lo stretcher", "emergency", "patient transfer", "stretcher", "trolley", "MF-48"] },
    { page: 20, title: "Stretcher Trolley", keywords: ["20", "stretcher trolley", "emergency", "patient transfer", "stretcher", "trolley", "MF-44", "MF-43"] },
    { page: 21, title: "Wheel Chair & Patient Shifter", keywords: ["21", "wheel chair", "foldable wheel chair", "patient shifter", "aluminum shifter", "emergency", "patient transfer", "MF-50", "MF-52", "MF-49", "MF-115"] },
    { page: 22, title: "Dressing Trolley & Mayo's Trolley", keywords: ["22", "dressing trolley", "mayo's trolley", "medical trolleys", "trolley", "MF-57", "MF-46"] },
    { page: 23, title: "Crash Cart", keywords: ["23", "crash cart", "emergency cart", "medical trolleys", "trolley", "MF-84", "MF-86"] },
    { page: 24, title: "Drug Trolley", keywords: ["24", "drug trolley", "medical trolleys", "trolley", "MF-47"] },
    { page: 25, title: "Laparoscopy Trolley", keywords: ["25", "laparoscopy trolley", "medical trolleys", "trolley", "MF-111"] },
    { page: 26, title: "Endoscopy Trolley", keywords: ["26", "endoscopy trolley", "medical trolleys", "trolley", "MF-113"] },
    { page: 27, title: "ECG Trolley", keywords: ["27", "ecg trolley", "medical trolleys", "trolley", "MF-58"] },
    { page: 28, title: "Instrument Trolley SS", keywords: ["28", "instrument trolley", "ss instrument trolley", "medical trolleys", "trolley", "MF-10", "MF-11", "MF-12"] },
    { page: 29, title: "Biomedical Waste Bin & Cylinder Trolley", keywords: ["29", "waste bin trolley", "biomedical waste", "cylinder trolley", "oxygen cylinder", "medical trolleys", "trolley", "MF-107", "MF-156", "MF-75", "MF-77"] },
    { page: 30, title: "Deluxe Examination Couch", keywords: ["30", "deluxe examination couch", "examination", "couch", "table", "MF-64", "MF-65"] },
    { page: 31, title: "Examination Table", keywords: ["31", "examination table", "examination", "table", "MF-80", "MF-83"] },
    { page: 32, title: "Scan Couch", keywords: ["32", "scan couch", "scan table", "examination", "ultrasound", "MF-96"] },
    { page: 33, title: "Revolving Stool & Blood Collection Chair", keywords: ["33", "revolving stool", "stool", "blood collection", "blood collection chair", "examination", "MF-13", "MF-14", "MF-15", "MF-101"] },
    { page: 34, title: "X-Ray Lobby", keywords: ["34", "x-ray lobby", "x-ray", "examination", "MF-119", "MF-110", "MF-153"] },
    { page: 35, title: "IV Stand & Foot Step", keywords: ["35", "iv stand", "infusion stand", "drip stand", "foot step", "single foot step", "double foot step", "kick bucket", "stainless steel furniture", "ward accessories", "MF-04", "MF-05", "MF-06", "MF-53", "MF-55", "MF-97"] },
    { page: 36, title: "Wash Basin Stand & 3-Fold Screen", keywords: ["36", "wash basin stand", "basin stand", "3 fold screen", "privacy screen", "ss rack", "ot scrub", "stainless steel furniture", "ward accessories", "MF-98", "MF-99", "MF-100", "MF-108", "MF-109"] },
    { page: 37, title: "General Furniture", keywords: ["37", "general furniture", "3 seater chair", "4 seater chair", "2 tier cot", "waiting chair", "furniture", "MF-104", "MF-105", "MF-117"] },
    { page: 38, title: "Accessories", keywords: ["38", "accessories", "mattress", "pillow", "aluminum side rail", "bush", "castors", "ss collapsible", "MF-102", "MF-103", "MF-120", "MF-123", "MF-148"] },
    { page: 39, title: "Accessories", keywords: ["39", "accessories", "ss arms", "bins", "ss plain side rails", "actuator", "control box", "wired remote handset", "remote", "MF-124", "MF-125", "MF-126", "MF-145", "MF-146", "MF-147"] },
    { page: 40, title: "Contact Us", keywords: ["40", "contact us", "address", "phone", "email", "location", "maps", "website", "mathurams"] }
];

// âœ… CLOSE SEARCH FUNCTION
const closeSearch = () => {
    searchModal.classList.remove('show');
    searchModal.classList.add('hidden');
    searchInput.value = '';
    searchResults.innerHTML = '';
};

// âœ… OPEN SEARCH MODAL
if (searchIcon) {
    searchIcon.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent immediate closure
        searchModal.classList.remove('hidden');

        requestAnimationFrame(() => {
            searchModal.classList.add('show');
        });

        searchInput.focus();
    });
}

// âœ… CLOSE BUTTON CLICK
if (closeSearchModal) {
    closeSearchModal.addEventListener('click', (e) => {
        e.stopPropagation();
        closeSearch();
    });
}

// âœ… CLICK OUTSIDE MODAL TO CLOSE - THIS IS THE KEY!
document.addEventListener('click', (e) => {
    // Check if modal is visible
    const isModalVisible = !searchModal.classList.contains('hidden');

    // Check if click is inside modal or on search icon
    const isClickInsideModal = searchModal.contains(e.target);
    const isClickOnSearchIcon = searchIcon && searchIcon.contains(e.target);

    // Close if clicking outside and modal is open
    if (isModalVisible && !isClickInsideModal && !isClickOnSearchIcon) {
        closeSearch();
    }
});

// âœ… PREVENT CLICKS INSIDE MODAL FROM CLOSING IT
searchModal.addEventListener('click', (e) => {
    e.stopPropagation();
});

// âœ… ESC KEY TO CLOSE
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
        closeSearch();
    }
});

// âœ… SEARCH FUNCTIONALITY
function runSearch() {
    const query = searchInput.value.toLowerCase().trim();

    if (!query) {
        searchResults.innerHTML = '';
        return;
    }

    let currentPage = 1;
    if (typeof $ !== 'undefined' && $('#flipbook').length && $('#flipbook').turn) {
        currentPage = $('#flipbook').turn('page') || 1;
    } else {
        const hashMatch = window.location.hash.match(/page\/(\d+)/);
        if (hashMatch) {
            currentPage = parseInt(hashMatch[1], 10);
        }
    }

    const filtered = pages.filter(page =>
        page.title.toLowerCase().includes(query) ||
        page.keywords.some(kw => kw.toLowerCase().includes(query))
    );

    if (filtered.length === 0) {
        searchResults.innerHTML = '<p class="text-gray-500 text-center p-3">No results found</p>';
        return;
    }

    searchResults.innerHTML = filtered.map(page => {
        const isCurrent = (page.page === currentPage);
        return `
            <div class="search-result-item p-3 hover:bg-gray-700 cursor-pointer border-b rounded ${isCurrent ? 'bg-orange-600/20 border-l-4 border-l-orange-500' : ''}" data-page="${page.page}">
                <div class="text-white font-semibold text-[.9vw] flex items-center justify-between">
                    <span>${page.title}</span>
                    ${isCurrent ? '<span class="text-[.65vw] bg-orange-500 text-white px-2 py-0.5 rounded-full uppercase font-bold">Current Page</span>' : ''}
                </div>
                <div class="text-gray-400 text-[.7vw]">Page ${page.page}</div>
            </div>
        `;
    }).join('');

    // Add click events to results
    document.querySelectorAll('.search-result-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const pageNum = parseInt(item.dataset.page, 10);
            if (typeof $ !== 'undefined' && $('#flipbook').length && $('#flipbook').turn) {
                $('#flipbook').turn('page', pageNum);
            }
            closeSearch();
        });
    });
}

// âœ… SEARCH INPUT - TYPE TO FILTER
if (searchInput) {
    searchInput.addEventListener('input', runSearch);

    // Enter key to navigate to first result
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const firstResult = document.querySelector('.search-result-item');
            if (firstResult) firstResult.click();
        }
    });
}

// âœ… SEARCH BUTTON CLICK
document.getElementById("SearchModal")?.addEventListener("click", (e) => {
    e.stopPropagation();
    runSearch();
    const firstResult = document.querySelector('.search-result-item');
    if (firstResult) firstResult.click();
});

// *****************************search icon code end*******************************



// *********************************zoom in zoom out button start ********************************** */


// ***********************************download code start******************************************

const downloadBtn = document.getElementById("download-btn");
const navMobileDownloadIcon = document.getElementById("navMobileDownloadIcon");
const downloadPopup = document.getElementById("downloadPopup");
let isGeneratingPdf = false;

if (downloadBtn) {
    downloadBtn.addEventListener("click", () => {
        startCatalogPdfDownload();
    });
}
if (navMobileDownloadIcon) {
    navMobileDownloadIcon.addEventListener("click", () => {
        startCatalogPdfDownload();
    });
}

function showDownloadToast(text, autoHideMs = 0) {
    if (!downloadPopup) return;
    downloadPopup.textContent = text;
    downloadPopup.classList.remove("hidden");
    requestAnimationFrame(() => {
        downloadPopup.classList.add("opacity-100");
    });

    if (autoHideMs > 0) {
        setTimeout(() => {
            downloadPopup.classList.remove("opacity-100");
            setTimeout(() => downloadPopup.classList.add("hidden"), 300);
        }, autoHideMs);
    }
}

function hideDownloadToast() {
    if (!downloadPopup) return;
    downloadPopup.classList.remove("opacity-100");
    setTimeout(() => downloadPopup.classList.add("hidden"), 300);
}

async function startCatalogPdfDownload() {
    if (isGeneratingPdf) return;
    isGeneratingPdf = true;

    // Check if html2canvas and jsPDF are loaded
    if (typeof html2canvas === "undefined" || (typeof window.jspdf === "undefined" && typeof jsPDF === "undefined")) {
        showDownloadToast("Loading PDF engine...", 2000);
        // Fallback direct link
        const fallback = document.createElement("a");
        fallback.href = "../global%20assets/Mathurams_IDC.pdf";
        fallback.download = "ZillionThreads_Catalog.pdf";
        document.body.appendChild(fallback);
        fallback.click();
        fallback.remove();
        isGeneratingPdf = false;
        return;
    }

    const { jsPDF } = window.jspdf || window;

    try {
        showDownloadToast("Preparing PDF: capturing pages...", 0);

        // Standard Page dimensions (4:5 ratio matching Yarika catalog standard, 800x1000px)
        const targetWidth = 800;
        const targetHeight = 1000;

        // Create PDF document (portrait, mm units, A4)
        const pdf = new jsPDF({
            orientation: "portrait",
            unit: "mm",
            format: "a4",
            compress: true
        });

        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        // Make sure all SVGs on all pages are fully loaded into the DOM before capturing
        if (typeof loadSVGs === "function") {
            try { loadSVGs(); } catch (e) {}
        }

        // Define the 6 pages configuration:
        // Pages 1, 2, 3, 6 use the direct vector SVGs from global assets/Images/home-page/
        // Pages 4 and 5 use the live customized Yarika collage stage
        const catalogPagesConfig = [
            { pageNum: 1, type: 'svg', src: '../global assets/Images/home-page/home-page-image.svg' },
            { pageNum: 2, type: 'svg', src: '../global assets/Images/home-page/problems-page.svg' },
            { pageNum: 3, type: 'svg', src: '../global assets/Images/home-page/opportunities-page.svg' },
            { pageNum: 4, type: 'stage', selector: '#yarika-stage-page4', pageKey: 'page4' },
            { pageNum: 5, type: 'stage', selector: '#yarika-stage-page5', pageKey: 'page5' },
            { pageNum: 6, type: 'svg', src: '../global assets/Images/home-page/contact-page.svg' }
        ];

        let renderedCount = 0;

        for (let i = 0; i < catalogPagesConfig.length; i++) {
            const pageCfg = catalogPagesConfig[i];
            showDownloadToast(`Generating PDF: Page ${pageCfg.pageNum} of ${catalogPagesConfig.length}...`, 0);

            // Create offscreen container
            const sandbox = document.createElement('div');
            sandbox.style.position = 'fixed';
            sandbox.style.left = '-9999px';
            sandbox.style.top = '0';
            sandbox.style.width = targetWidth + 'px';
            sandbox.style.height = targetHeight + 'px';
            sandbox.style.background = '#FFFFFF';
            sandbox.style.overflow = 'hidden';
            sandbox.style.zIndex = '-9999';

            if (pageCfg.type === 'svg') {
                // Fetch the clean original SVG image directly without runtime reveal animation styles
                try {
                    const resp = await fetch(pageCfg.src);
                    const svgText = await resp.text();
                    const parser = new DOMParser();
                    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
                    const svgEl = svgDoc.querySelector("svg");
                    if (svgEl) {
                        svgEl.removeAttribute("width");
                        svgEl.removeAttribute("height");
                        svgEl.setAttribute("width", "100%");
                        svgEl.setAttribute("height", "100%");
                        svgEl.style.width = "100%";
                        svgEl.style.height = "100%";
                        svgEl.style.display = "block";
                        // Ensure all elements inside the pure SVG have full opacity
                        svgEl.querySelectorAll('*').forEach(node => {
                            node.style.opacity = '1';
                            node.style.visibility = 'visible';
                        });
                        sandbox.appendChild(svgEl);
                    }
                } catch (e) {
                    console.error("Failed loading SVG directly for page " + pageCfg.pageNum, e);
                }
            } else if (pageCfg.type === 'stage') {
                // Live customized Yarika stage for Pages 4 & 5
                const origStage = document.querySelector(pageCfg.selector) || document.querySelector(`.page-${pageCfg.pageNum} .yarika-page-stage`);
                if (origStage) {
                    const clone = origStage.cloneNode(true);
                    clone.style.width = '100%';
                    clone.style.height = '100%';
                    clone.style.position = 'relative';
                    clone.style.backgroundSize = 'cover';
                    clone.style.backgroundPosition = 'center';
                    // Remove calibration overlays if present
                    clone.querySelectorAll('.calibrate-overlay-img, .slot-id-label').forEach(e => e.remove());
                    clone.querySelectorAll('.slot, .slot img, .yarika-heading-bar, img, text').forEach(el => {
                        el.style.opacity = '1';
                        el.style.visibility = 'visible';
                    });
                    sandbox.appendChild(clone);
                }
            }

            document.body.appendChild(sandbox);

            // Brief pause for layout rendering
            await new Promise(r => setTimeout(r, 60));

            // Capture to canvas
            const canvas = await html2canvas(sandbox, {
                width: targetWidth,
                height: targetHeight,
                scale: 1.5,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#FFFFFF',
                logging: false
            });

            document.body.removeChild(sandbox);

            const imgData = canvas.toDataURL('image/jpeg', 0.92);

            if (renderedCount > 0) {
                pdf.addPage('a4', 'portrait');
            }

            pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
            renderedCount++;
        }

        showDownloadToast("Download ready! Saving...", 1500);

        // Save PDF with requested filename
        pdf.save("ZillionThreads_Catalog.pdf");

    } catch (err) {
        console.error("[CatalogPDF] PDF generation error:", err);
        showDownloadToast("Download error, using direct file...", 2000);
        // Fallback direct download
        const fallback = document.createElement("a");
        fallback.href = "../global%20assets/Mathurams_IDC.pdf";
        fallback.download = "ZillionThreads_Catalog.pdf";
        document.body.appendChild(fallback);
        fallback.click();
        fallback.remove();
    } finally {
        isGeneratingPdf = false;
        setTimeout(() => hideDownloadToast(), 2500);
    }
}
// ***********************************download code end******************************************

// const searchModal1 = document.getElementById("searchModal");
// const searchIcon1 = document.querySelector('img[alt="search-icon"]');
// const closeSearchModal1 = document.getElementById("closeSearchModal");

// // OPEN MODAL WITH ANIMATION
// searchIcon1.addEventListener("click", () => {
//     searchModal1.classList.remove("hidden");

//     // allow browser to apply display change
//     requestAnimationFrame(() => {
//         searchModal1.classList.add("show");
//     });
// });

// // CLOSE MODAL WITH ANIMATION
// closeSearchModal1.addEventListener("click", () => {
//     searchModal1.classList.remove("show");

//     // after animation ends, hide it
//     searchModal1.addEventListener(
//         "transitionend",
//         () => {
//             searchModal1.classList.add("hidden");
//         },
//         { once: true }
//     );
// });




document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.svg-container').forEach(container => {
        const src = container.getAttribute('data-src');
        if (src) {
            fetch(src)
                .then(response => {
                    if (!response.ok) throw new Error(`Failed to load ${src}`);
                    return response.text();
                })
                .then(svgText => {
                    const parser = new DOMParser();
                    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
                    const svgEl = svgDoc.querySelector("svg");
                    if (svgEl) {
                        // Clean up SVG dimensions
                        svgEl.removeAttribute("width");
                        svgEl.removeAttribute("height");
                        svgEl.style.width = "100%";
                        svgEl.style.height = "100%";
                        svgEl.style.cursor = "pointer";
                        svgEl.classList.add("line-reveal9"); // keep your reveal animation
                    }
                    container.innerHTML = svgEl ? svgEl.outerHTML : svgText;
                })
                .catch(err => console.error("SVG Load Error:", err));
        }
    });




});




// ************************ Zoom In Zoom Out Code Start ******************************
// ************************ COMPLETE ZOOM SYSTEM WITH BLOCK FUNCTIONALITY ************************

(function () {
    'use strict';

    // ==================== ELEMENT REFERENCES ====================
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const zoomSlider = document.getElementById('zoomSlider');
    const zoomPercentage = document.getElementById('zoomPercentage');
    const flipbookContainer = document.getElementById('flipbook');
    const wrapper = document.querySelector('.flipbook-scroll-wrapper');

    // ==================== STATE VARIABLES ====================
    let currentZoom = 100;
    let isZoomed = false;

    // ==================== ðŸ”¥ ZOOM ALERT POPUP SYSTEM ====================
    function createZoomAlertElements() {
        if (!document.getElementById('zoom-alert-overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'zoom-alert-overlay';
            overlay.innerHTML = `
                <style>
                    #zoom-alert-overlay {
                        position: fixed;
                        inset: 0;
                        background: rgba(0, 0, 0, 0.6);
                        backdrop-filter: blur(8px);
                        z-index: 999999999;
                        opacity: 0;
                        visibility: hidden;
                        transition: all 0.3s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        pointer-events: none;
                    }
                    
                    #zoom-alert-overlay.show {
                        opacity: 1;
                        visibility: visible;
                    }
                    
                    #zoom-alert-box {
                        background: linear-gradient(135deg, #0f766e 0%, #134e4a 50%, #0d5c52 100%);
                        color: white;
                        padding: 2vw 3vw;
                        border-radius: 1.5vw;
                        text-align: center;
                        box-shadow: 
                            0 25px 50px rgba(0, 0, 0, 0.4),
                            0 0 0 1px rgba(255, 255, 255, 0.1),
                            inset 0 1px 0 rgba(255, 255, 255, 0.2);
                        transform: scale(0.9) translateY(20px);
                        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        max-width: 90vw;
                        min-width: 300px;
                        pointer-events: auto;
                    }
                    
                    #zoom-alert-overlay.show #zoom-alert-box {
                        transform: scale(1) translateY(0);
                    }
                    
                    .zoom-alert-icon {
                        width: 4vw;
                        height: 4vw;
                        min-width: 50px;
                        min-height: 50px;
                        background: rgba(255, 255, 255, 0.15);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 1vw;
                        animation: pulse-icon 2s ease-in-out infinite;
                    }
                    
                    @keyframes pulse-icon {
                        0%, 100% { transform: scale(1); }
                        50% { transform: scale(1.1); }
                    }
                    
                    .zoom-alert-icon svg {
                        width: 2vw;
                        height: 2vw;
                        min-width: 24px;
                        min-height: 24px;
                        stroke: #4ade80;
                    }
                    
                    .zoom-alert-title {
                        font-size: 1.3vw;
                        font-weight: 700;
                        margin-bottom: 0.5vw;
                        font-family: 'Inter', sans-serif;
                    }
                    
                    .zoom-alert-message {
                        font-size: 0.9vw;
                        opacity: 0.9;
                        margin-bottom: 1vw;
                        line-height: 1.5;
                    }
                    
                    .zoom-alert-hint {
                        font-size: 0.75vw;
                        opacity: 0.7;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5vw;
                    }
                    
                    .zoom-alert-hint kbd {
                        background: rgba(255, 255, 255, 0.2);
                        padding: 0.2vw 0.5vw;
                        border-radius: 0.3vw;
                        font-family: monospace;
                    }
                    
                    .zoom-alert-progress {
                        width: 100%;
                        height: 4px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 2px;
                        margin-top: 1vw;
                        overflow: hidden;
                    }
                    
                    .zoom-alert-progress-bar {
                        height: 100%;
                        background: linear-gradient(90deg, #4ade80, #22c55e);
                        border-radius: 2px;
                        animation: progress-shrink 3s linear forwards;
                    }
                    
                    @keyframes progress-shrink {
                        from { width: 100%; }
                        to { width: 0%; }
                    }
                    
                    /* Mobile Responsive */
                    @media (max-width: 768px) {
                        #zoom-alert-box {
                            padding: 20px 25px;
                            border-radius: 16px;
                            min-width: 280px;
                        }
                        
                        .zoom-alert-title {
                            font-size: 16px;
                        }
                        
                        .zoom-alert-message {
                            font-size: 13px;
                        }
                        
                        .zoom-alert-hint {
                            font-size: 11px;
                        }
                    }
                </style>
                
                <div id="zoom-alert-box">
                    <div class="zoom-alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            <line x1="11" y1="8" x2="11" y2="14"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </div>
                    <div class="zoom-alert-title">Action Blocked</div>
                    <div class="zoom-alert-message">Please zoom out first to use this feature</div>
                    <div class="zoom-alert-hint">
                        Press <kbd>Ctrl</kbd> + <kbd>0</kbd> or use zoom buttons
                    </div>
                    <div class="zoom-alert-progress">
                        <div class="zoom-alert-progress-bar"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            // âŒ REMOVED: Click overlay to dismiss - popup only auto-hides now
        }
    }

    // ==================== SHOW ZOOM ALERT ====================
    function showZoomAlert(title, message) {
        createZoomAlertElements();

        const overlay = document.getElementById('zoom-alert-overlay');
        const alertBox = document.getElementById('zoom-alert-box');

        if (!overlay || !alertBox) return;

        // Update content
        const titleEl = alertBox.querySelector('.zoom-alert-title');
        const messageEl = alertBox.querySelector('.zoom-alert-message');
        const progressBar = alertBox.querySelector('.zoom-alert-progress-bar');

        if (titleEl) titleEl.textContent = title || 'Action Blocked';
        if (messageEl) messageEl.innerHTML = message || 'Please zoom out first to use this feature';

        // Reset progress bar animation
        if (progressBar) {
            progressBar.style.animation = 'none';
            progressBar.offsetHeight; // Trigger reflow
            progressBar.style.animation = 'progress-shrink 3s linear forwards';
        }

        // Show overlay
        overlay.classList.add('show');

        // Auto-hide after 3 seconds (ONLY way to close)
        clearTimeout(overlay.hideTimer);
        overlay.hideTimer = setTimeout(hideZoomAlert, 3000);
    }

    // ==================== HIDE ZOOM ALERT ====================
    function hideZoomAlert() {
        const overlay = document.getElementById('zoom-alert-overlay');
        if (overlay) {
            overlay.classList.remove('show');
            clearTimeout(overlay.hideTimer);
        }
    }

    // ==================== BLOCK ACTION WHEN ZOOMED ====================
    function blockIfZoomed(e, title, message) {
        if (!isZoomed) return false;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        showZoomAlert(title, message);
        return true;
    }

    // ==================== APPLY ZOOM FUNCTION ====================
    function applyZoom(zoomLevel) {
        currentZoom = Math.max(100, Math.min(130, zoomLevel));
        const scale = currentZoom / 100;
        isZoomed = currentZoom > 100;

        if (flipbookContainer) {
            flipbookContainer.style.transform = `scale(${scale})`;
            flipbookContainer.style.transformOrigin = 'top center';
            flipbookContainer.style.transition = 'transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        }

        if (wrapper) {
            if (isZoomed) {
                wrapper.classList.add('zoomed');
                wrapper.classList.remove('no-scrollbar');
                wrapper.style.overflowY = 'auto';
            } else {
                wrapper.classList.remove('zoomed');
                wrapper.classList.add('no-scrollbar');
                wrapper.style.overflowY = 'hidden';
                wrapper.scrollTop = 0;
            }
        }

        // Update UI
        if (zoomPercentage) zoomPercentage.textContent = currentZoom + '%';
        if (zoomSlider) zoomSlider.value = currentZoom;

        // Button states
        if (zoomOutBtn) {
            zoomOutBtn.style.opacity = currentZoom <= 100 ? '0.4' : '1';
            zoomOutBtn.style.pointerEvents = currentZoom <= 100 ? 'none' : 'auto';
        }
        if (zoomInBtn) {
            zoomInBtn.style.opacity = currentZoom >= 130 ? '0.4' : '1';
            zoomInBtn.style.pointerEvents = currentZoom >= 130 ? 'none' : 'auto';
        }

        // Update blocked elements visual state
        updateBlockedElementsState();



        // Add this inside your applyZoom function (at the end, before the console.log)

        // Hide/Show Page Controller based on zoom
        const pageController = document.querySelector('.page-controller');
        if (pageController) {
            if (isZoomed) {
                pageController.classList.add('zoom-hidden');
            } else {
                pageController.classList.remove('zoom-hidden');
            }
        }
    }

    // ==================== UPDATE BLOCKED ELEMENTS VISUAL STATE ====================
    function updateBlockedElementsState() {
        const blockedSelectors = [
            '#iconTOC', '#navToggle', '#mobileTocBtn',
            '.thumb-dot', '.tb-link', '#navToggle1',
            '#iconText', '#searchIconDesktop', 'img[alt="search-icon"]',
            '#iconNextPage', '#iconPrevPage', '#iconFrontPage', '#iconSkipForward',
            '.ui-arrow-next-page', '.ui-arrow-previous-page', '.next-arrow', '.prev-arrow',
            '#iconAutoplay' // âœ… ADDED: Autoplay icon
        ];

        blockedSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                if (isZoomed) {
                    el.style.opacity = '0.5';
                    el.style.cursor = 'not-allowed';
                    el.classList.add('zoom-blocked');
                } else {
                    el.style.opacity = '';
                    el.style.cursor = '';
                    el.classList.remove('zoom-blocked');
                }
            });
        });
    }

    // ==================== BLOCK HANDLERS ====================

    // ðŸ”’ TABLE OF CONTENTS
    function blockTOC(e) {
        if (blockIfZoomed(e, 'ðŸ“‹ Table of Contents Blocked', 'Zoom out to access Table of Contents')) {
            return false;
        }
    }

    // ðŸ”’ THUMBNAIL NAVIGATION
    function blockThumbnail(e) {
        if (blockIfZoomed(e, 'ðŸ–¼ï¸ Thumbnail Navigation Blocked', 'Zoom out to navigate using thumbnails')) {
            return false;
        }
    }

    // ðŸ”’ SEARCH
    function blockSearch(e) {
        if (blockIfZoomed(e, 'ðŸ” Search Blocked', 'Zoom out to use the search feature')) {
            return false;
        }
    }

    // ðŸ”’ PAGE NAVIGATION (Next/Prev/First/Last)
    function blockPageNav(e) {
        if (blockIfZoomed(e, 'ðŸ“„ Page Navigation Blocked', 'Zoom out to navigate between pages')) {
            return false;
        }
    }

    // ðŸ”’ FLIPBOOK PAGE FLIP
    function blockFlip(e) {
        if (blockIfZoomed(e, 'ðŸ“– Page Flip Blocked', 'Zoom out to flip pages')) {
            return false;
        }
    }

    // ðŸ”’ AUTOPLAY - NEW!
    function blockAutoplay(e) {
        if (blockIfZoomed(e, 'â–¶ï¸ Autoplay Blocked', 'Zoom out to use autoplay feature')) {
            return false;
        }
    }

    // ==================== ATTACH BLOCK HANDLERS ====================
    function attachBlockHandlers() {

        // ===== TABLE OF CONTENTS =====
        ['iconTOC', 'navToggle', 'mobileTocBtn'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', blockTOC, true);
                el.addEventListener('touchstart', blockTOC, true);
            }
        });

        // ===== BOTTOM THUMBNAIL DOTS =====
        document.querySelectorAll('.thumb-dot').forEach(dot => {
            dot.addEventListener('click', blockThumbnail, true);
            dot.addEventListener('touchstart', blockThumbnail, true);
        });

        // ===== TOC LIST LINKS (tb-link) =====
        document.querySelectorAll('.tb-link').forEach(link => {
            link.addEventListener('click', blockThumbnail, true);
            link.addEventListener('touchstart', blockThumbnail, true);
        });

        // ===== THUMBNAIL TOGGLE (navToggle1) =====
        const navToggle1 = document.getElementById('navToggle1');
        if (navToggle1) {
            navToggle1.addEventListener('click', blockThumbnail, true);
            navToggle1.addEventListener('touchstart', blockThumbnail, true);
        }

        // ===== SEARCH ICONS =====
        ['iconText', 'searchIconDesktop'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', blockSearch, true);
                el.addEventListener('touchstart', blockSearch, true);
            }
        });

        document.querySelectorAll('img[alt="search-icon"]').forEach(el => {
            el.addEventListener('click', blockSearch, true);
            el.addEventListener('touchstart', blockSearch, true);
        });

        // ===== PAGE NAVIGATION BUTTONS =====
        ['iconNextPage', 'iconPrevPage', 'iconFrontPage', 'iconSkipForward'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', blockPageNav, true);
                el.addEventListener('touchstart', blockPageNav, true);
            }
        });

        // ===== ARROW CONTROLS =====
        document.querySelectorAll('.ui-arrow-next-page, .ui-arrow-previous-page, .next-arrow, .prev-arrow').forEach(el => {
            el.addEventListener('click', blockFlip, true);
            el.addEventListener('touchstart', blockFlip, true);
        });

        // ===== AUTOPLAY BUTTON - NEW! =====
        const autoplayBtn = document.getElementById('iconAutoplay');
        if (autoplayBtn) {
            autoplayBtn.addEventListener('click', blockAutoplay, true);
            autoplayBtn.addEventListener('touchstart', blockAutoplay, true);
        }

        // ===== FLIPBOOK DIRECT CLICKS (for page corners) =====
        if (flipbookContainer) {
            ['mousedown', 'touchstart'].forEach(eventType => {
                flipbookContainer.addEventListener(eventType, function (e) {
                    if (isZoomed) {
                        const isCornerClick = e.target.closest('.page') ||
                            e.target.closest('.p') ||
                            e.target.closest('.even') ||
                            e.target.closest('.odd');
                        if (isCornerClick) {
                            blockFlip(e);
                        }
                    }
                }, true);
            });
        }

        // ===== TURN.JS EVENTS =====
        if (typeof $ !== 'undefined' && $('#flipbook').turn) {
            $('#flipbook').bind('start', function (e, pageObject, corner) {
                if (isZoomed && corner) {
                    e.preventDefault();
                    showZoomAlert('ðŸ“– Page Flip Blocked', 'Zoom out to flip pages');
                    return false;
                }
            });

            $('#flipbook').bind('turning', function (e, page, view) {
                if (isZoomed) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // ===== MOBILE TOC ITEMS =====
        document.querySelectorAll('.mobile-toc-item').forEach(item => {
            item.addEventListener('click', blockThumbnail, true);
            item.addEventListener('touchstart', blockThumbnail, true);
        });


    }

    // ==================== ZOOM BUTTON HANDLERS ====================
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (currentZoom < 150) {
                applyZoom(currentZoom + 10);
            }
        });
    }

    const zoomResetBtn = document.getElementById('zoomResetBtn');
    if (zoomResetBtn) {
        zoomResetBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (currentZoom === 100) {
                applyZoom(120);
            } else {
                applyZoom(100);
            }
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (currentZoom > 100) {
                applyZoom(currentZoom - 10);
            }
        });
    }

    if (zoomSlider) {
        zoomSlider.addEventListener('input', function (e) {
            applyZoom(parseInt(e.target.value, 10));
        });
    }

    // ==================== KEYBOARD SHORTCUTS ====================
    document.addEventListener('keydown', function (e) {
        // Ctrl/Cmd + Plus/Minus for zoom
        if (e.ctrlKey || e.metaKey) {
            if (e.key === '+' || e.key === '=') {
                e.preventDefault();
                applyZoom(Math.min(150, currentZoom + 10));
            } else if (e.key === '-') {
                e.preventDefault();
                applyZoom(Math.max(100, currentZoom - 10));
            } else if (e.key === '0') {
                e.preventDefault();
                applyZoom(100);
            }
        }

        // ESC to reset zoom
        if (e.key === 'Escape' && isZoomed) {
            applyZoom(100);
            hideZoomAlert();
        }
    });

    // ==================== MOUSE WHEEL ZOOM ====================
    let wheelTimeout;
    document.addEventListener('wheel', function (e) {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            clearTimeout(wheelTimeout);

            const delta = e.deltaY > 0 ? -10 : 10;
            const newZoom = Math.max(100, Math.min(150, currentZoom + delta));

            wheelTimeout = setTimeout(() => {
                applyZoom(newZoom);
            }, 10);
        }
    }, { passive: false });

    // ==================== INITIALIZE ====================
    function init() {
        createZoomAlertElements();
        attachBlockHandlers();
        applyZoom(100);

    }

    // Wait for DOM and flipbook
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(init, 500);
        });
    } else {
        setTimeout(init, 500);
    }

    // Expose functions globally
    window.applyZoom = applyZoom;
    window.isZoomed = () => isZoomed;
    window.showZoomAlert = showZoomAlert;
    window.hideZoomAlert = hideZoomAlert;

})();

/* ========================================
   TOP ICON NAVIGATION FUNCTIONALITY
   ======================================== */
document.addEventListener('DOMContentLoaded', function () {

    // Get all icon buttons
    const iconTOC = document.getElementById('iconTOC');
    const iconZoomOut = document.getElementById('iconZoomOut');
    const iconZoomIn = document.getElementById('iconZoomIn');
    const iconAudio = document.getElementById('iconAudio');
    const iconPrevPage = document.getElementById('iconPrevPage');
    const iconNextPage = document.getElementById('iconNextPage');
    const iconSkipForward = document.getElementById('iconSkipForward');
    const iconFrontPage = document.getElementById('iconFrontPage');
    const iconRotate = document.getElementById('iconRotate');
    const iconText = document.getElementById('iconText');
    const iconFullscreen = document.getElementById('iconFullscreen');

    // ========== TABLE OF CONTENTS ==========
    if (iconTOC && navToggle) {
        iconTOC.addEventListener('click', function (e) {
            e.stopPropagation();
            navToggle.click(); // Trigger existing TOC
        });
    }

    // ========== ZOOM OUT ==========
    if (iconZoomOut && zoomOutBtn) {
        iconZoomOut.addEventListener('click', function () {
            zoomOutBtn.click();
        });
    }


    // ========== ZOOM IN ==========
    if (iconZoomIn && zoomInBtn) {
        iconZoomIn.addEventListener('click', function () {
            zoomInBtn.click();
        });
    }

    // ========== AUDIO TOGGLE ==========
    if (iconAudio && typeof toggleMusic === 'function') {
        // Update icon based on music state
        function updateAudioIcon() {
            if (musicOn) {
                iconAudio.classList.add('active');
                iconAudio.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
          </svg>
        `;
            } else {
                iconAudio.classList.remove('active');
                iconAudio.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
            <line x1="23" y1="9" x2="17" y2="15"/>
            <line x1="17" y1="9" x2="23" y2="15"/>
          </svg>
        `;
            }
        }

        iconAudio.addEventListener('click', function () {
            toggleMusic();
            updateAudioIcon();
        });

        // Initial state
        updateAudioIcon();
    }

    // ========== PREVIOUS PAGE ==========
    if (iconPrevPage) {
        iconPrevPage.addEventListener('click', function () {
            if ($('#flipbook').turn) {
                $('#flipbook').turn('previous');
            }
        });
    }

    // ========== NEXT PAGE ==========
    if (iconNextPage) {
        iconNextPage.addEventListener('click', function () {
            if ($('#flipbook').turn) {
                $('#flipbook').turn('next');
            }
        });
    }

    // ========== SKIP FORWARD (Go to last page) ==========
    if (iconSkipForward) {
        iconSkipForward.addEventListener('click', function () {
            if ($('#flipbook').turn) {
                const totalPages = $('#flipbook').turn('pages');
                $('#flipbook').turn('page', totalPages);
            }
        });
    }

    // ========== front PAGE (Example: Jump to specific page) ==========
    if (iconFrontPage) {
        iconFrontPage.addEventListener('click', function () {
            const currentPage = $('#flipbook').turn('page');
            const jumpTo = 1; // Jump 2 pages forward
            const totalPages = $('#flipbook').turn('pages');

            if (jumpTo <= totalPages) {
                $('#flipbook').turn('page', jumpTo);
            }
        });
    }

    // ========== ROTATE (Example: Rotate current view) ==========
    if (iconRotate) {
        let rotationAngle = 0;
        iconRotate.addEventListener('click', function () {
            rotationAngle = (rotationAngle + 90) % 360;
            const flipbook = document.getElementById('flipbook');
            if (flipbook) {
                flipbook.style.transform = `rotate(${rotationAngle}deg)`;
                flipbook.style.transition = 'transform 0.5s ease';
            }
        });
    }

    // ========== TEXT TOOL (Example: Toggle search) ==========
    if (iconText && searchIcon) {
        iconText.addEventListener('click', function () {
            searchIcon.click(); // Trigger search modal
        });
    }

    // ========== FULLSCREEN ==========
    if (iconFullscreen) {
        iconFullscreen.addEventListener('click', function () {
            const fsBtn = document.getElementById('full-screen-btn');
            if (fsBtn) {
                fsBtn.click();
            } else {
                // Fallback: toggle fullscreen directly
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            }
        });
    }

    // ========== UPDATE BUTTON STATES BASED ON PAGE ==========
    if ($('#flipbook').turn) {
        $('#flipbook').bind('turned', function (event, page) {
            const totalPages = $('#flipbook').turn('pages');

            // FIRST PAGE
            if (page === 1) {
                iconFrontPage?.setAttribute('disabled', true);
                iconPrevPage?.setAttribute('disabled', true);
            } else {
                iconFrontPage?.removeAttribute('disabled');
                iconPrevPage?.removeAttribute('disabled');
            }

            // LAST PAGE
            if (page === totalPages) {
                iconNextPage?.setAttribute('disabled', true);
                iconSkipForward?.setAttribute('disabled', true);
            } else {
                iconNextPage?.removeAttribute('disabled');
                iconSkipForward?.removeAttribute('disabled');
            }
        });
    }

});

$(document).ready(function () {

    const $flipbook = $('#flipbook');
    const $pageInput = $('#pageInput');
    const $totalPages = $('#totalPages');
    const $goPageBtn = $('#goPageBtn');

    // ========== FUNCTION: GO TO PAGE ==========
    function goToPage() {
        const totalPages = $flipbook.turn('pages');
        let page = parseInt($pageInput.val(), 10);

        // Validate page number
        if (!page || page < 1) {
            page = 1;
        }
        if (page > totalPages) {
            page = totalPages;
        }

        // Update input and go to page
        $pageInput.val(page);
        $flipbook.turn('page', page);
    }

    // ========== UPDATE INPUT WHEN PAGE TURNS ==========
    $flipbook.on('turned', function (event, page) {
        $pageInput.val(page);
    });

    // ========== INITIALIZE TOTAL PAGES ==========
    setTimeout(() => {
        const totalPages = $flipbook.turn('pages');

        if (!totalPages) return;

        $totalPages.text(totalPages);
        $pageInput.attr('max', totalPages);

        const currentPage = $flipbook.turn('page');
        $pageInput.val(currentPage);
    }, 300);

    // ========== GO BUTTON CLICK ==========
    $goPageBtn.on('click', function () {
        goToPage();
    });

    // ========== ENTER KEY TO GO ==========
    $pageInput.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            goToPage();
            $(this).blur(); // Remove focus
        }
    });

    // ========== PREVENT INVALID INPUT ==========
    $pageInput.on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

});

// ========== THUMBNAIL DRAWER SYSTEM (DESKTOP ONLY) ==========
document.addEventListener("DOMContentLoaded", function () {
    const thumbToggleBtn = document.getElementById('desktopThumbToggle');
    const thumbDrawer = document.getElementById('thumbnailDrawer');
    const closeThumbBtn = document.getElementById('closeThumbBtn');
    const thumbGrid = document.getElementById('thumbnailGrid');

    if (!thumbGrid || !thumbDrawer) return;

    // Generate thumbnails dynamically for only the 6 catalog pages:
    // Page 1 (Single cover)
    createThumbItem(1, 'page 1', '../global%20assets/Images/Thumbnail-Images/page-1.webp');

    // Pages 2-3 (Double page spread)
    createThumbItem(2, 'page 2-3', '../global%20assets/Images/Thumbnail-Images/page-2-3.webp');

    // Pages 4-5 (Double page spread - Yarika Collections)
    createThumbItem(4, 'page 4-5', '../global%20assets/Images/Thumbnail-Images/page-4-5.webp');

    // Page 6 (Single contact/back page)
    createThumbItem(6, 'page 6', '../global%20assets/Images/Thumbnail-Images/page-6.webp');

    function createThumbItem(pageNumber, label, imageSrc) {
        const item = document.createElement('div');
        item.className = 'thumb-grid-item';
        if (pageNumber === 1 || pageNumber === 40) {
            item.classList.add('portrait-card');
        }
        item.setAttribute('data-page', pageNumber);

        const img = document.createElement('img');
        img.className = 'thumb-grid-img';
        img.src = imageSrc;
        img.alt = label;
        // Fallback placeholder if image fails to load
        img.onerror = function () {
            this.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="130" viewBox="0 0 100 130"><rect width="100%" height="100%" fill="%23f3f4f6"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Segoe UI, Arial" font-size="9" font-weight="600" fill="%23E67429">Page ' + pageNumber + '</text></svg>';
        };

        const labelDiv = document.createElement('div');
        labelDiv.className = 'thumb-grid-label';
        labelDiv.textContent = label;

        item.appendChild(img);
        item.appendChild(labelDiv);

        // Click event to flip book
        item.addEventListener('click', function () {
            if (window.jQuery && $('#flipbook').turn) {
                $('#flipbook').turn('page', pageNumber);
            }
            closeThumbDrawer();
        });

        thumbGrid.appendChild(item);
    }

    // Toggle Thumbnail Drawer
    if (thumbToggleBtn) {
        thumbToggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (thumbDrawer.classList.contains('show')) {
                closeThumbDrawer();
            } else {
                openThumbDrawer();
            }
        });
    }

    if (closeThumbBtn) {
        closeThumbBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeThumbDrawer();
        });
    }

    function openThumbDrawer() {
        // Close TOC if open
        if (typeof closeMenu === 'function') closeMenu();

        thumbDrawer.classList.add('show');
        document.body.classList.add('toc-active'); // Reuses backdrop logic to push arrows back
        const overlay = document.getElementById('navOverlay');
        if (overlay) overlay.classList.add('show');

        // Highlight active page item in grid
        updateActiveThumbnailItem();
    }

    function closeThumbDrawer() {
        thumbDrawer.classList.remove('show');
        // Only remove overlay if TOC is not showing
        const tocNav = document.getElementById('tocNav');
        if (!tocNav || !tocNav.classList.contains('show')) {
            document.body.classList.remove('toc-active');
            const overlay = document.getElementById('navOverlay');
            if (overlay) overlay.classList.remove('show');
        }
    }
    window.closeThumbDrawer = closeThumbDrawer;

    // Close when clicking overlay
    const overlay = document.getElementById('navOverlay');
    if (overlay) {
        overlay.addEventListener('click', function () {
            closeThumbDrawer();
        });
    }

    // Close when clicking outside of the drawer
    document.addEventListener('click', function (e) {
        const isDrawerVisible = thumbDrawer.classList.contains('show');
        const isClickInsideDrawer = thumbDrawer.contains(e.target);
        const isClickOnToggle = thumbToggleBtn.contains(e.target);
        if (isDrawerVisible && !isClickInsideDrawer && !isClickOnToggle) {
            closeThumbDrawer();
        }
    });

    // Close when pressing Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape" && thumbDrawer.classList.contains('show')) {
            closeThumbDrawer();
        }
    });

    // Update active item inside grid based on flipbook page
    function updateActiveThumbnailItem() {
        if (!window.jQuery || !$('#flipbook').turn) return;
        const currentPage = $('#flipbook').turn('page');

        document.querySelectorAll('.thumb-grid-item').forEach(item => {
            const pageVal = parseInt(item.getAttribute('data-page'));
            // If current page matches target page or target page + 1 (for double spreads)
            if (pageVal === currentPage || (pageVal > 1 && pageVal + 1 === currentPage)) {
                item.classList.add('active');
                // Scroll item into view smoothly
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Listen to flipbook turning events to update active grid item
    if (window.jQuery) {
        $('#flipbook').on('turned', function () {
            if (thumbDrawer.classList.contains('show')) {
                updateActiveThumbnailItem();
            }
        });
    }
});

// ==================== 3D EXPERIENCE PAGE ANIMATION CONTROLLER ====================
function init3dExperienceAnimations() {
    const container = document.querySelector('.exp-3d-container');
    const cards = Array.from(document.querySelectorAll('.exp-3d-card'));
    if (!cards.length) return;

    let isSequenceRunning = false;
    let revealTimeouts = [];
    let pulseInterval = null;
    let currentPulseIndex = 0;

    function clearTimers() {
        revealTimeouts.forEach(t => clearTimeout(t));
        revealTimeouts = [];
        if (pulseInterval) {
            clearInterval(pulseInterval);
            pulseInterval = null;
        }
    }

    function start3dSequence() {
        if (isSequenceRunning) return;
        isSequenceRunning = true;
        clearTimers();

        // Step 1: Initial state - smooth scaled down
        cards.forEach(card => {
            card.classList.remove('revealed', 'card-pulse-active', 'pop-active', 'threed-card-animate');
        });

        // Step 2: Smooth one-by-one staggered entrance (130ms delay per card)
        cards.forEach((card, idx) => {
            const t = setTimeout(() => {
                if (!isSequenceRunning) return;
                card.classList.add('revealed');
            }, idx * 130 + 50);
            revealTimeouts.push(t);
        });

        // Step 3: Start smooth auto-scale looping pulse after entrance finishes
        const totalRevealTime = cards.length * 130 + 700;
        const tLoop = setTimeout(() => {
            if (!isSequenceRunning) return;

            currentPulseIndex = 0;

            function pulseStep() {
                if (!isSequenceRunning) return;
                cards.forEach(card => card.classList.remove('card-pulse-active'));
                if (cards[currentPulseIndex]) {
                    cards[currentPulseIndex].classList.add('card-pulse-active');
                }
                currentPulseIndex = (currentPulseIndex + 1) % cards.length;
            }

            pulseStep();
            pulseInterval = setInterval(pulseStep, 1700);
        }, totalRevealTime);
        revealTimeouts.push(tLoop);
    }

    function stop3dSequence() {
        isSequenceRunning = false;
        clearTimers();
        cards.forEach(card => {
            card.classList.remove('card-pulse-active');
            card.classList.add('revealed');
        });
    }

    function check3dPageVisibility() {
        let isVisible = false;

        if (typeof $ !== 'undefined' && $('#flipbook').length && $('#flipbook').turn) {
            const currentView = $('#flipbook').turn('view') || [];
            const pageNum = $('#flipbook').turn('page');
            const isSinglePage = $('#flipbook').turn('display') === 'single' || $(window).width() <= 768;

            if (isSinglePage) {
                // Mobile single page mode: animate specifically when page 5 is active
                if (pageNum === 5 || currentView.includes(5)) {
                    isVisible = true;
                }
            } else {
                // Desktop double page mode: animate when page 4 or 5 is visible
                if (pageNum === 4 || pageNum === 5 || currentView.includes(4) || currentView.includes(5)) {
                    isVisible = true;
                }
            }
        } else {
            isVisible = true;
        }

        if (isVisible) {
            start3dSequence();
        } else {
            stop3dSequence();
        }
    }

    if (typeof $ !== 'undefined' && $('#flipbook').length) {
        $('#flipbook').bind('turned turning', check3dPageVisibility);
    }

    // Trigger check on load
    setTimeout(check3dPageVisibility, 300);
    setTimeout(check3dPageVisibility, 800);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init3dExperienceAnimations);
} else {
    init3dExperienceAnimations();
}

// ==================== PRODUCT IMAGE LIGHTBOX POPUP MODAL ====================
function initProductImageModal() {
    const modal = document.getElementById('product-image-modal');
    const modalImg = document.getElementById('product-modal-img');
    const closeBtn = document.getElementById('product-modal-close');
    if (!modal || !modalImg) return;

    function openPopup(imageSrc) {
        if (!imageSrc) return;
        modalImg.src = imageSrc;
        modal.classList.add('active');

        // Lock/disable flipbook page flipping while modal is open
        if (typeof $ !== 'undefined' && $('#flipbook').length && $('#flipbook').turn) {
            try {
                $('#flipbook').turn('disable', true);
            } catch (err) {}
        }
    }

    function closePopup() {
        modal.classList.remove('active');
        setTimeout(() => {
            if (!modal.classList.contains('active')) {
                modalImg.src = '';
            }
        }, 350);

        // Re-enable flipbook page flipping when modal closes
        if (typeof $ !== 'undefined' && $('#flipbook').length && $('#flipbook').turn) {
            try {
                $('#flipbook').turn('disable', false);
            } catch (err) {}
        }
    }

    // Delegated click handler for any product image clicked
    document.addEventListener('click', function (e) {
        const target = e.target;
        if (!target || target.tagName !== 'IMG') return;

        // Ignore UI overlays, drawer links, modal contents, etc.
        if (
            target.closest('#mobileTocPanel') ||
            target.closest('#tocNav') ||
            target.closest('#tocNav1') ||
            target.closest('.thumbnail-preview-container') ||
            target.closest('#product-image-modal') ||
            target.closest('.exp-3d-container')
        ) {
            return;
        }

        const srcLower = (target.src || '').toLowerCase();

        // Strictly exclude non-product section images (Home, About Us, Table of Content, Infrastructure, Contact Us, 3D Experience, etc.)
        const isNonProductSection =
            srcLower.includes('home-page') ||
            srcLower.includes('about-us') ||
            srcLower.includes('table-of-content') ||
            srcLower.includes('infrastructure-page') ||
            srcLower.includes('contact-us') ||
            srcLower.includes('3d-experience') ||
            srcLower.includes('commonthings') ||
            srcLower.includes('background') ||
            srcLower.includes('thumbnail') ||
            srcLower.includes('icons') ||
            srcLower.includes('logo') ||
            srcLower.includes('shade') ||
            srcLower.includes('bottom-navbar');

        if (isNonProductSection) return;

        // Check if image belongs to a product catalog item
        const isProductImg =
            target.id === 'cot-image' ||
            target.classList.contains('cot-image') ||
            target.classList.contains('cot-pos1') ||
            target.classList.contains('cot-pos2') ||
            target.classList.contains('cot-pos3') ||
            target.classList.contains('cot-pos4') ||
            target.classList.contains('cot-pos5') ||
            target.classList.contains('cot-pos6') ||
            srcLower.includes('/products/') ||
            (srcLower.includes('-image.webp') || srcLower.includes('-image.png') || srcLower.includes('-cot') || srcLower.includes('-bed') || srcLower.includes('-stretcher'));

        if (isProductImg) {
            e.preventDefault();
            e.stopPropagation();
            openPopup(target.src);
        }
    }, true);

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closePopup();
        });
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal || e.target.classList.contains('product-modal-container')) {
            e.preventDefault();
            e.stopPropagation();
            closePopup();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closePopup();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductImageModal);
} else {
    initProductImageModal();
}






