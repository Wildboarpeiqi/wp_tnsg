/* ==========================================================================
   TNSG main.js
   Vanilla JS, zero dependencies. Handles sliders, counters, menus, modals.
   ========================================================================== */
(function () {
  'use strict';

  /* ---------- Helpers ---------- */
  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  /* ---------- Generic slider factory ---------- */
  function createSlider(root, opts) {
    var track = $('.slider-track', root) || root.querySelector('[class$="-track"]');
    var slides = root.querySelectorAll('.hero-slide, .cert-item');
    var prev = root.querySelector('.hero-prev, .cert-prev');
    var next = root.querySelector('.hero-next, .cert-next');
    var dotsWrap = root.querySelector('.hero-dots');
    var current = 0;
    var timer = null;
    var count = slides.length;

    if (!track || count === 0) return;

    function goTo(i) {
      if (i < 0) i = count - 1;
      if (i > count - 1) i = 0;
      current = i;
      if (opts && opts.isCarousel) {
        // cert carousel: translate by slide width
        var item = slides[0];
        var step = item.getBoundingClientRect().width + (opts.gap || 20);
        track.style.transform = 'translateX(' + (-step * current) + 'px)';
      } else {
        track.style.transform = 'translateX(' + (-100 * current) + '%)';
      }
      // sync active classes
      Array.prototype.forEach.call(slides, function (s, idx) {
        var wasActive = s.classList.contains('hero-slide-active');
        s.classList.toggle('hero-slide-active', idx === current);
        s.classList.toggle('app-slide-active', idx === current);
        // video slides: play the active one, pause / unload the rest
        var vid = s.querySelector('.hero-video video');
        var frame = s.querySelector('.hero-video iframe');
        if (idx === current) {
          if (vid) { var pp = vid.play(); if (pp && pp.catch) { pp.catch(function () {}); } }
          if (frame && frame.dataset.src && !frame.getAttribute('src')) { frame.setAttribute('src', frame.dataset.src); }
        } else {
          if (vid) { try { vid.pause(); } catch (e) {} }
          if (frame && frame.getAttribute('src')) { frame.dataset.src = frame.getAttribute('src'); frame.removeAttribute('src'); }
        }
        // restart the entrance animation every time a slide becomes active
        if (!wasActive && idx === current) {
          var parts = s.querySelectorAll('.hero-company, .hero-title, .hero-desc, .hero-btns');
          Array.prototype.forEach.call(parts, function (el) {
            el.style.animation = 'none';
          });
          void s.offsetWidth; // force reflow so the animation can replay
          Array.prototype.forEach.call(parts, el => { el.style.animation = ''; });
        }
      });
      if (dotsWrap) {
        var dots = dotsWrap.children;
        Array.prototype.forEach.call(dots, function (d, idx) {
          d.classList.toggle('active', idx === current);
        });
      }
    }

    function nextSlide() { goTo(current + 1); }
    function prevSlide() { goTo(current - 1); }

    if (prev) prev.addEventListener('click', function (e) { e.preventDefault(); prevSlide(); });
    if (next) next.addEventListener('click', function (e) { e.preventDefault(); nextSlide(); });

    // build dots
    if (dotsWrap) {
      for (var d = 0; d < count; d++) {
        var dot = document.createElement('button');
        dot.className = 'dot' + (d === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to slide ' + (d + 1));
        (function (idx) {
          dot.addEventListener('click', function () { goTo(idx); });
        })(d);
        dotsWrap.appendChild(dot);
      }
    }

    // autoplay
    if (opts && opts.autoplay) {
      timer = setInterval(nextSlide, opts.interval || 4000);
      root.addEventListener('mouseenter', function () { if (timer) clearInterval(timer); });
      root.addEventListener('mouseleave', function () { timer = setInterval(nextSlide, opts.interval || 4000); });
    }

    // keyboard / swipe
    root.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') prevSlide();
      if (e.key === 'ArrowRight') nextSlide();
    });
    var touchX = 0;
    root.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; }, { passive: true });
    root.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 40) { dx < 0 ? nextSlide() : prevSlide(); }
    }, { passive: true });

    return { goTo: goTo, next: nextSlide, prev: prevSlide };
  }

  /* ---------- Hero slider (full-width) ---------- */
  var heroRoot = $('#heroSlider');
  if (heroRoot) {
    var heroApi = createSlider(heroRoot, { autoplay: false, interval: 4000 });

    // hero 嵌入视频：iframe 无法用 object-fit，这里按容器比例放大到 cover 尺寸（居中、超出裁剪）
    // 规则：视频按比例放大到"填满容器"，宽或高必然有一边超出，由 .hero-video 的 overflow:hidden 裁掉
    function fitHeroVideos() {
      $$('.hero-slide .hero-video iframe', heroRoot).forEach(function (frame) {
        var wrap = frame.closest('.hero-video');
        if (!wrap) return;
        var cw = wrap.clientWidth, ch = wrap.clientHeight;
        if (!cw || !ch) return;
        var ratio = 16 / 9, bw, bh;
        if (cw / ch > ratio) { bw = cw; bh = cw / ratio; }   // 容器更宽：宽撑满容器，高超出（上下裁剪）
        else { bh = ch; bw = ch * ratio; }                    // 容器更高：高撑满容器，宽超出（左右裁剪）
        frame.style.width = bw + 'px';
        frame.style.height = bh + 'px';
      });
    }
    fitHeroVideos();
    window.addEventListener('resize', fitHeroVideos);

    // 首屏初始化：让第一个 slide 的视频（如有）开始播放（无视频 slide 无副作用）
    if (heroApi) { heroApi.goTo(0); }
  }

  /* ---------- Certificate carousel（逐张切换 + 无缝循环：像轮子一样永远循环） ----------
     方案：按每屏张数 perView 克隆头尾（头部克隆最后 perView 张、尾部克隆最前 perView 张），
     滑入尾部/头部克隆区动画结束后瞬间跳回真实对应位置（内容完全一致，视觉无感）；
     gap 动态读取 CSS，保证克隆位置与真实位置像素级一致（避免跳回错位）；
     动画期间锁定点击，杜绝连点导致的瞬移/乱跳。 */
  var certRoot = $('.cert-slider');
  if (certRoot) {
    var certTrack = $('.cert-track', certRoot);
    var certItems = $$('.cert-item', certRoot);
    var certPrev = $('.cert-prev', certRoot);
    var certNext = $('.cert-next', certRoot);
    var certRealCount = certItems.length;
    var certPerView = 1;
    var certCurrent = 0;
    var certTimer = null;
    var certJumpTimer = null;
    var certBusy = false;

    if (certTrack && certRealCount > 0) {
      var certRaw = certItems.map(function (it) { return it.outerHTML; });

      // 动态读取 .cert-track 的实际 gap（CSS gap: 2rem = 32px），与位移计算保持一致
      function certGap() {
        var cs = getComputedStyle(certTrack);
        var g = parseFloat(cs.columnGap || cs.gap || '0');
        return isNaN(g) ? 0 : g;
      }
      function certStep() {
        var first = certTrack.children[0];
        return (first ? first.getBoundingClientRect().width : 0) + certGap();
      }
      function certBuild() {
        certPerView = Math.max(1, Math.round(certRoot.clientWidth / certStep()));
        var head = '', tail = '';
        for (var i = 0; i < certPerView; i++) {
          head += certRaw[(certRealCount - certPerView + i + certRealCount) % certRealCount];
          tail += certRaw[i];
        }
        certTrack.innerHTML = head + certRaw.join('') + tail;
        certCurrent = certPerView;   // 定位到真实第一张
        certBusy = false;
        certTrack.style.transition = 'none';
        certTrack.style.transform = 'translateX(' + (-certStep() * certCurrent) + 'px)';
        void certTrack.offsetWidth;
        certTrack.style.transition = 'transform 0.5s ease';
      }
      function certSnap(i, animate) {
        certTrack.style.transition = animate === false ? 'none' : 'transform 0.5s ease';
        certCurrent = i;
        certTrack.style.transform = 'translateX(' + (-certStep() * i) + 'px)';
        if (animate === false) {
          void certTrack.offsetWidth;
          certTrack.style.transition = 'transform 0.5s ease';
        }
      }
      // 动画结束后：若位于克隆区，瞬间跳回真实对应位置（内容完全一致，视觉无感）
      function certSettle() {
        if (certJumpTimer) { clearTimeout(certJumpTimer); certJumpTimer = null; }
        if (certCurrent > certPerView + certRealCount - 1) {
          certSnap(certPerView, false);
        } else if (certCurrent < certPerView) {
          certSnap(certPerView + certRealCount - 1, false);
        }
        certBusy = false;
      }
      certTrack.addEventListener('transitionend', function (e) {
        if (e.propertyName !== 'transform') return;
        certSettle();
      });
      function certNextF() {
        if (certBusy) return;
        certBusy = true;
        var i = certCurrent + 1;
        if (i > certPerView + certRealCount) i = certPerView + 1;   // 兜底（正常不可达）
        certSnap(i, true);
        certJumpTimer = setTimeout(certSettle, 600);   // 兜底：transitionend 万一未触发
      }
      function certPrevF() {
        if (certBusy) return;
        certBusy = true;
        var i = certCurrent - 1;
        if (i < certPerView - 1) i = certPerView + certRealCount - 2;   // 兜底
        certSnap(i, true);
        certJumpTimer = setTimeout(certSettle, 600);
      }
      function certStart() { certStop(); certTimer = setInterval(certNextF, 4000); }
      function certStop() { if (certTimer) { clearInterval(certTimer); certTimer = null; } }

      if (certPrev) certPrev.addEventListener('click', function (e) { e.preventDefault(); certPrevF(); certStart(); });
      if (certNext) certNext.addEventListener('click', function (e) { e.preventDefault(); certNextF(); certStart(); });
      certRoot.addEventListener('mouseenter', certStop);
      certRoot.addEventListener('mouseleave', certStart);

      certBuild();
      certStart();
      window.addEventListener('resize', function () { certBuild(); });
    }
  }

  /* ---------- Application slider ---------- */
  var appTrack = $('#appTrack');
  var industryTabs = $('#industryTabs');
  if (appTrack && industryTabs) {
    var appSlides = appTrack.children;
    var tabItems = $$('.industry-item', industryTabs);
    var appCurrent = 0;
    var mqMobile = window.matchMedia('(max-width: 960px)');
    var appTimer = null;

    function goToApp(i) {
      if (i < 0 || i >= appSlides.length) return;
      appCurrent = i;
      appTrack.style.transform = 'translateX(' + (-100 * i) + '%)';
      Array.prototype.forEach.call(appSlides, function (s, idx) {
        s.classList.toggle('app-slide-active', idx === i);
      });
      // only highlight tabs on desktop (mobile carousel is decoupled)
      if (!mqMobile.matches) {
        tabItems.forEach(function (tab, idx) {
          tab.classList.toggle('active', idx === i);
        });
      }
    }

    function startAppAuto() {
      stopAppAuto();
      appTimer = setInterval(function () {
        goToApp((appCurrent + 1) % appSlides.length);
      }, 4000);
    }
    function stopAppAuto() {
      if (appTimer) { clearInterval(appTimer); appTimer = null; }
    }

    tabItems.forEach(function (tab, idx) {
      // desktop: hover slides the background (same as original site)
      tab.addEventListener('mouseenter', function () {
        if (!mqMobile.matches) goToApp(idx);
      });
      // touch devices: tap once = switch; tap active tab again = follow link
      tab.addEventListener('click', function (e) {
        if (mqMobile.matches) return; // mobile: tabs are plain links, decoupled
        if (appCurrent !== idx) {
          e.preventDefault();
          goToApp(idx);
        }
      });
    });

    // mobile: auto-play carousel, decoupled from tabs; desktop: tab-driven, no autoplay
    function syncAppMode() {
      if (mqMobile.matches) {
        startAppAuto();
        tabItems.forEach(function (tab) { tab.classList.remove('active'); });
      } else {
        stopAppAuto();
        goToApp(0);
      }
    }
    mqMobile.addEventListener ? mqMobile.addEventListener('change', syncAppMode)
                              : mqMobile.addListener(syncAppMode);
    syncAppMode();

    // mobile: manual swipe to switch slides (restarts autoplay)
    var tsX = 0, tsY = 0, swiping = false;
    appTrack.addEventListener('touchstart', function (e) {
      if (!mqMobile.matches) return;
      tsX = e.touches[0].clientX;
      tsY = e.touches[0].clientY;
      swiping = true;
    }, { passive: true });
    appTrack.addEventListener('touchend', function (e) {
      if (!swiping) return;
      swiping = false;
      var dx = e.changedTouches[0].clientX - tsX;
      var dy = e.changedTouches[0].clientY - tsY;
      if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy) * 1.2) {
        if (dx < 0) { goToApp((appCurrent + 1) % appSlides.length); }
        else { goToApp((appCurrent - 1 + appSlides.length) % appSlides.length); }
        startAppAuto();
      }
    }, { passive: true });
  }

  /* ---------- Animated counters ---------- */
  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var duration = 1800;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target);
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target;
    }
    requestAnimationFrame(step);
  }

  var counters = $$('.count[data-count]');
  var counterDone = false;
  function checkCounters() {
    if (counterDone) return;
    var stats = $('.about-stats');
    if (!stats) { counterDone = true; return; }
    var rect = stats.getBoundingClientRect();
    if (rect.top < window.innerHeight - 60) {
      counters.forEach(animateCount);
      counterDone = true;
    }
  }
  window.addEventListener('scroll', checkCounters, { passive: true });
  checkCounters();

  /* ---------- Header scroll state ---------- */
  var header = $('#SITE_HEADER');
  function onScroll() {
    if (header) header.classList.toggle('scrolled', window.scrollY > 40);
    checkCounters();
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile nav ---------- */
  var navToggle = $('#navToggle');
  var nav = $('.nav');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      navToggle.classList.toggle('open', open);
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    // close button inside drawer
    var navClose = $('#navClose');
    if (navClose) {
      navClose.addEventListener('click', function () {
        nav.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    }
    // close on outside click only; link taps handled separately
    document.addEventListener('click', function (e) {
      if (nav.classList.contains('open') && !nav.contains(e.target) && !navToggle.contains(e.target)) {
        nav.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
    // non-submenu links close the drawer; PRODUCTS title only toggles accordion
    $$('.mainNavLiA', nav).forEach(function (a) {
      a.addEventListener('click', function (e) {
        var li = a.parentElement;
        var isSubmenuTitle = li.classList.contains('has-submenu');
        if (isSubmenuTitle && window.matchMedia('(max-width: 960px)').matches) {
          e.preventDefault();
          li.classList.toggle('expanded');
          return; // keep drawer open
        }
        nav.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ---------- Modals ---------- */
  var modals = $$('.modal');
  function openModal(id) {
    var m = document.getElementById(id);
    if (m) { m.classList.add('open'); m.setAttribute('aria-hidden', 'false'); }
  }
  function closeModal(m) {
    m.classList.remove('open');
    m.setAttribute('aria-hidden', 'true');
  }

  var searchOpen = $('#searchOpen');
  if (searchOpen) searchOpen.addEventListener('click', function () { openModal('searchModal'); });

  // data-lightbox links open contact modal
  $$('[data-lightbox]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      openModal(el.getAttribute('data-lightbox'));
    });
  });

  // close buttons
  $$('.modal-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var m = btn.closest('.modal');
      if (m) closeModal(m);
    });
  });
  // click on backdrop
  modals.forEach(function (m) {
    m.addEventListener('click', function (e) {
      if (e.target === m) closeModal(m);
    });
  });
  // ESC key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') modals.forEach(closeModal);
  });

  /* ---------- Back to top ---------- */
  var backTop = $('#backTop');
  if (backTop) {
    backTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Forms ----------
     Only intercept demo forms (action="#") that have no real backend.
     Real forms (WP search with an actual action URL) must submit normally. */
  $$('form').forEach(function (f) {
    var action = (f.getAttribute('action') || '').trim();
    if (action !== '#' && action !== '') return;
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      // TODO: wire to backend / mail handler after WP conversion
      var btn = f.querySelector('button[type="submit"]');
      if (btn) {
        var orig = btn.textContent;
        btn.textContent = 'Sent!';
        setTimeout(function () { btn.textContent = orig; }, 2000);
      }
    });
  });

  /* ---------- Scroll reveal (subtle fade-up) ---------- */
  if ('IntersectionObserver' in window) {
    var revealEls = $$('.section-head, .listBox, .stat-card, .newsBox, .service-card, .industry-item');
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    revealEls.forEach(function (el) { io.observe(el); });
  }
})();

  /* hero entrance fallback: ensure text visible even if CSS animation
     is unsupported/frozen (headless capture, old browsers) */
  setTimeout(function () { document.body.classList.add('hero-ready'); }, 1200);
