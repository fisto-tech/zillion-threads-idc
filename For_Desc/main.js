/* =====================================================================
   main.js  -  Flipbook corner-peel BLUR FIX
   ---------------------------------------------------------------------
   Why the blur happens
   1. Turn.js (acceleration:true) moves/rotates the page with translate3d.
      The browser turns the page into a GPU bitmap and rotates that bitmap
      -> text + images get resampled = blurry.
   2. Your CSS has hints that FORCE those GPU layers on page content:
        style.css  : img { transform: translateZ(0); backface-visibility:hidden }
                     .product-item-card, .product-info-wrap ... translateZ(0)
                     image-rendering: crisp-edges  (jagged when rotated)
        main.css   : .card-slot { will-change: transform, opacity, filter }
   What this file does
   - turns Turn.js acceleration OFF (plain 2D transforms = crisp)
   - while the mouse is near a page corner (or a flip is running) it adds
     the class "flip-peeling" on #flipbook; a small CSS block (injected
     here) removes the GPU-layer hints ONLY during that time
   - removes the class again when the mouse leaves -> page looks normal
   No other file needs CSS changes.
   ===================================================================== */
(function () {
  'use strict';

  var CFG = {
    flipbook: '#flipbook',
    cornerZone: 90,     // px from a corner that counts as "peel zone" (bigger than Turn.js 50px hover area)
    leaveExtra: 25      // extra px before the fix switches off again (stops flicker)
  };

  /* ---------- 1. CSS that is active only while .flip-peeling ---------- */
  function injectCss() {
    if (document.getElementById('flip-crisp-css')) return;

    // :where() keeps specificity low, so your own class rules / inline GSAP styles still win
    var scope = ':where(#flipbook.flip-peeling) ';

    var layerHints = [            // elements that currently force a GPU layer
      'img',
      '.product-img-wrap img', '.product-img-box img', '.product-detail-img',
      '.product-thumb-item img',
      '.product-item-card', '.product-info-wrap',
      '.product-card-name', '.product-card-desc'
    ];
    var containers = [            // containers with backface / will-change hints
      '.page', '.page-wrapper', '.inner-page', '.products-page-container', '.card-slot'
    ];

    var css = '';
    css += layerHints.map(function (s) { return scope + s; }).join(',\n') +
      ' {\n  transform: translate(0, 0);\n  -webkit-backface-visibility: visible;\n' +
      '  backface-visibility: visible;\n  will-change: auto;\n}\n';
    css += scope + 'img {\n  image-rendering: auto;\n}\n';
    css += containers.map(function (s) { return scope + s; }).join(',\n') +
      ' {\n  -webkit-backface-visibility: visible;\n  backface-visibility: visible;\n' +
      '  will-change: auto;\n}\n';

    var style = document.createElement('style');
    style.id = 'flip-crisp-css';
    style.textContent = css;
    document.head.appendChild(style);   // appended last = wins ties against style.css/main.css
  }

  /* ---------- 2. Turn.js: switch acceleration off ---------- */
  function turnOffAcceleration($fb) {
    try {
      $fb.turn('options', { acceleration: false });
    } catch (e) { /* option is also set in index.html (see instructions) */ }
  }

  /* ---------- 3. Add / remove .flip-peeling ---------- */
  function bindPeelState($fb) {
    var el = $fb[0];
    var nearCorner = false;
    var animating = false;

    function apply() {
      el.classList.toggle('flip-peeling', nearCorner || animating);
    }

    // mouse near any of the 4 corners of the flipbook
    document.addEventListener('mousemove', function (e) {
      var r = el.getBoundingClientRect();
      if (!r.width) return;

      var scale = el.offsetWidth ? r.width / el.offsetWidth : 1;   // works with your zoom (scale)
      var zone = CFG.cornerZone * scale + (nearCorner ? CFG.leaveExtra * scale : 0);

      var inside = e.clientX >= r.left && e.clientX <= r.right &&
                   e.clientY >= r.top && e.clientY <= r.bottom;
      var dx = Math.min(e.clientX - r.left, r.right - e.clientX);
      var dy = Math.min(e.clientY - r.top, r.bottom - e.clientY);
      var now = inside && dx <= zone && dy <= zone;

      if (now !== nearCorner) { nearCorner = now; apply(); }
    }, { passive: true });

    document.addEventListener('mouseleave', function () {
      nearCorner = false; apply();
    });

    // Turn.js events (bubble up from the pages to #flipbook)
    $fb.on('start', function () { animating = true; apply(); });
    $fb.on('end turned', function () { animating = false; apply(); });
  }

  /* ---------- 4. Wait until the flipbook is ready, then start ---------- */
  function init() {
    var $ = window.jQuery;
    if (!$) return;

    var tries = 0;
    var timer = setInterval(function () {
      var $fb = $(CFG.flipbook);
      var ready = false;
      try { ready = $fb.length && $fb.turn('options') && true; } catch (e) { ready = false; }

      if (ready) {
        clearInterval(timer);
        injectCss();
        turnOffAcceleration($fb);
        bindPeelState($fb);
      } else if (++tries > 100) {          // give up after ~10 s
        clearInterval(timer);
      }
    }, 100);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();