/* ==========================================================================
   Product Detail page scripts (product-detail.html)
   Page-specific JS only. Shared logic lives in js/main.js (homepage).
   Vanilla JS, zero dependencies.
   ========================================================================== */
(function () {
  'use strict';

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  /* ---------- Thumbnail hover / click switches main image ---------- */
  var mainImage = $('#mainImage');
  var thumbs = $$('#thumbList .thumb');
  if (mainImage && thumbs.length) {
    function setMain(src, activeThumb) {
      mainImage.src = src;
      mainImage.classList.remove('zoomed');
      var mb = mainImage.parentNode;
      if (mb) mb.classList.remove('zooming');
      var v = document.querySelector('#productGallery .zoom-view');
      if (v) v.classList.remove('active');
      thumbs.forEach(function (t) { t.classList.toggle('active', t === activeThumb); });
    }
    thumbs.forEach(function (thumb) {
      // hover switches image (desktop)
      thumb.addEventListener('mouseenter', function () {
        setMain(thumb.getAttribute('data-src'), thumb);
      });
      // click also switches (touch / keyboard accessibility)
      thumb.addEventListener('click', function () {
        setMain(thumb.getAttribute('data-src'), thumb);
      });
    });
  }

  /* ---------- Thumbnails > 4 张时左右箭头翻页（PC；移动端箭头隐藏、可直接横向滑动） ---------- */
  var thumbList = $('#thumbList');
  var thumbPrev = $('.thumb-prev');
  var thumbNext = $('.thumb-next');
  if (thumbList && thumbPrev && thumbNext) {
    function thumbStepPx() {
      var t = thumbList.querySelector('.thumb');
      if (!t) return 0;
      return (t.getBoundingClientRect().width + 16) * 4; // 一整屏 = 4 张 + 间距
    }
    function thumbScroll(dir) {
      var step = thumbStepPx();
      var maxScroll = thumbList.scrollWidth - thumbList.clientWidth;
      if (step <= 0 || maxScroll <= 0) return;
      var target = dir < 0
        ? Math.max(0, thumbList.scrollLeft - step)
        : Math.min(maxScroll, thumbList.scrollLeft + step);
      thumbList.scrollTo({ left: target, behavior: 'smooth' });
    }
    thumbPrev.addEventListener('click', function () { thumbScroll(-1); });
    thumbNext.addEventListener('click', function () { thumbScroll(1); });
  }

  /* ---------- Main image window-zoom (magnifier) ---------- */
  var galleryEl = $('#productGallery');
  if (galleryEl && mainImage) {
    // 放大镜 DOM：指示框（在主图内，与主图同比例，避免放大后变形）+ 放大视图（挂 gallery 层）
    var lens = document.createElement('div');
    lens.className = 'zoom-lens';
    var view = document.createElement('div');
    view.className = 'zoom-view';
    var viewImg = document.createElement('img');
    view.appendChild(viewImg);
    var mainBox = mainImage.parentNode; // .main-image
    mainBox.appendChild(lens);
    galleryEl.appendChild(view);
    var zoom = 2.4;    // 放大倍率（view 宽 / 主图宽，因 lens 与主图同比例，实际为 view/lens 的线性倍数）

    // 读取主图实际显示尺寸
    function imgGeom() {
      var r = mainImage.getBoundingClientRect();
      return { w: r.width, h: r.height };
    }
    // lens 与主图保持相同宽高比，尺寸 = 主图显示尺寸 / zoom（这样 lens 框住的区域放大到 view 不变形）
    function lensGeom(w, h) {
      return { w: Math.round(w / zoom), h: Math.round(h / zoom) };
    }
    // 定位 + 等比设置放大视图（尺寸跟随主图，放在主图右侧 / 窄屏叠加在主图上）
    function layoutView() {
      var mb = mainBox.getBoundingClientRect();
      var gapPx = 24; // 1.5rem
      if (window.innerWidth >= 1200) {
        view.style.left = (mb.width + gapPx) + 'px';
        view.style.top = '0px';
      } else {
        view.style.left = '0px';
        view.style.top = '0px';
      }
      view.style.width = mb.width + 'px';
      view.style.height = mb.height + 'px';
    }
    // 按指针相对主图的坐标定位 lens，并同步放大视图内容
    function placeLens(x, y) {
      var g = imgGeom();
      var l = lensGeom(g.w, g.h);
      var lx = Math.min(Math.max(x - l.w / 2, 0), g.w - l.w);
      var ly = Math.min(Math.max(y - l.h / 2, 0), g.h - l.h);
      lens.style.width = l.w + 'px';
      lens.style.height = l.h + 'px';
      lens.style.left = lx + 'px';
      lens.style.top = ly + 'px';
      // 放大视图内容偏移 = lens 在主图内的比例 × (放大图 - 主图)，保持同比例无拉伸
      var vx = (lx / Math.max(g.w - l.w, 1)) * (g.w * zoom - g.w);
      var vy = (ly / Math.max(g.h - l.h, 1)) * (g.h * zoom - g.h);
      viewImg.style.width = (g.w * zoom) + 'px';
      viewImg.style.height = (g.h * zoom) + 'px';
      viewImg.style.left = (-vx) + 'px';
      viewImg.style.top = (-vy) + 'px';
    }
    function positionZoom(e) {
      var mb = mainBox.getBoundingClientRect();
      var x = e.clientX - mb.left; // 相对主图
      var y = e.clientY - mb.top;
      placeLens(x, y);
    }
    function centerZoom() {
      var mb = mainBox.getBoundingClientRect();
      placeLens(mb.width / 2, mb.height / 2);
    }
    mainBox.addEventListener('mouseenter', function () {
      mainBox.classList.add('zooming');
      layoutView();
      view.classList.add('active');
      viewImg.src = mainImage.currentSrc || mainImage.src;
      centerZoom();
    });
    mainBox.addEventListener('mousemove', function (e) { positionZoom(e); });
    mainBox.addEventListener('mouseleave', function () {
      mainBox.classList.remove('zooming');
      view.classList.remove('active');
    });
  }

  /* ---------- Lightbox for workshop images ---------- */
  var overlay = null;
  function ensureOverlay() {
    if (overlay) return overlay;
    overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = '<button type="button" class="lightbox-close" aria-label="Close">&times;</button><img alt="">';
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay || e.target.classList.contains('lightbox-close')) closeLightbox();
    });
    document.body.appendChild(overlay);
    return overlay;
  }
  function openLightbox(src) {
    var ov = ensureOverlay();
    var img = ov.querySelector('img');
    img.src = src;
    ov.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeLightbox() {
    if (!overlay) return;
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
  $$('a.lightbox[data-lightbox]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      openLightbox(a.getAttribute('href'));
    });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
  });

  /* ---------- Related products slider（无缝循环：克隆头尾 + translateX，与首页 cert 同方案；
     一次动 1 张；自动轮播 3s + hover/触摸暂停） ---------- */
  var relatedTrack = $('#relatedTrack');
  if (relatedTrack) {
    var relatedItems = $$('.related-item', relatedTrack);
    var relatedCount = relatedItems.length;
    var relatedPerView = 1;
    var relatedPositions = 1;
    var relatedPos = 0;
    var relatedJumpTimer = null;
    var relatedBusy = false;
    var relatedTimer = null;
    var relatedPaused = false;

    function relatedGap() {
      var cs = getComputedStyle(relatedTrack);
      var g = parseFloat(cs.columnGap || cs.gap || '0');
      return isNaN(g) ? 0 : g;
    }
    function relatedStep() {
      var it = relatedTrack.querySelector('.related-item');
      return it ? it.getBoundingClientRect().width + relatedGap() : 0;
    }
    function relatedBuild() {
      var raw = relatedItems.map(function (it) { return it.outerHTML; });
      relatedPerView = Math.max(1, Math.round(relatedTrack.clientWidth / relatedStep()));
      relatedPositions = Math.max(1, relatedCount - relatedPerView + 1);
      var head = '', tail = '';
      for (var i = 0; i < relatedPerView; i++) {
        head += raw[(relatedCount - relatedPerView + i + relatedCount) % relatedCount];
        tail += raw[i];
      }
      relatedTrack.innerHTML = head + raw.join('') + tail;
      if (relatedPos > relatedPositions - 1) relatedPos = relatedPositions - 1;
      if (relatedPos < 0) relatedPos = 0;
      relatedSnap(relatedPos, false);
    }
    function relatedSnap(i, animate) {
      relatedTrack.style.transition = animate === false ? 'none' : 'transform 0.5s ease';
      relatedPos = i;
      relatedTrack.style.transform = 'translateX(' + (-relatedStep() * (i + relatedPerView)) + 'px)';
      if (animate === false) {
        void relatedTrack.offsetWidth;
        relatedTrack.style.transition = 'transform 0.5s ease';
      }
    }
    // 动画结束后：若位于克隆区，瞬间跳回真实对应位置（内容完全一致，视觉无感）
    function relatedSettle() {
      if (relatedJumpTimer) { clearTimeout(relatedJumpTimer); relatedJumpTimer = null; }
      var maxP = relatedPositions - 1 + relatedPerView;
      if (relatedPos >= maxP) relatedSnap(relatedPos - maxP, false);
      else if (relatedPos <= -relatedPerView) relatedSnap(relatedPositions - 1, false);
      relatedBusy = false;
    }
    relatedTrack.addEventListener('transitionend', function (e) {
      if (e.propertyName === 'transform') relatedSettle();
    });
    function relatedGo(p) {
      if (relatedBusy) return;
      relatedBusy = true;
      relatedSnap(p, true);
      relatedJumpTimer = setTimeout(relatedSettle, 600);
    }
    function relatedNextF() { relatedGo(relatedPos + 1); }
    function relatedBackF() { relatedGo(relatedPos - 1); }
    var prevBtn = $('.related-prev');
    var nextBtn = $('.related-next');
    if (prevBtn) prevBtn.addEventListener('click', function () { relatedBackF(); });
    if (nextBtn) nextBtn.addEventListener('click', function () { relatedNextF(); });

    // 自动轮播：每 3s 前进一张；用户 hover/触摸/页面隐藏时暂停
    function relatedStartAuto() {
      relatedStopAuto();
      relatedTimer = setInterval(function () { if (!relatedPaused) relatedNextF(); }, 3000);
    }
    function relatedStopAuto() { if (relatedTimer) { clearInterval(relatedTimer); relatedTimer = null; } }
    var sliderBox = relatedTrack.closest('.related-slider');
    if (sliderBox) {
      sliderBox.addEventListener('mouseenter', function () { relatedPaused = true; });
      sliderBox.addEventListener('mouseleave', function () { relatedPaused = false; });
    }
    document.addEventListener('visibilitychange', function () {
      relatedPaused = document.hidden;
    });

    // touch swipe（手动滑动切换，暂停自动轮播）
    var tsX = 0, tsY = 0, swiping = false;
    relatedTrack.addEventListener('touchstart', function (e) {
      tsX = e.touches[0].clientX;
      tsY = e.touches[0].clientY;
      swiping = true;
    }, { passive: true });
    relatedTrack.addEventListener('touchmove', function () { relatedPaused = true; }, { passive: true });
    relatedTrack.addEventListener('touchend', function (e) {
      if (!swiping) return;
      swiping = false;
      var dx = e.changedTouches[0].clientX - tsX;
      var dy = e.changedTouches[0].clientY - tsY;
      if (Math.abs(dx) > 30 && Math.abs(dx) > Math.abs(dy) * 1.2) {
        relatedPaused = true;
        if (dx < 0) relatedNextF(); else relatedBackF();
      }
    }, { passive: true });

    relatedBuild();
    relatedStartAuto();
    window.addEventListener('resize', function () { relatedBuild(); });
  }

  /* ---------- 证书横向轮播（无缝循环：克隆头尾 + translateX，与首页 cert 同方案；
     一次动 1 张；箭头 + 触屏滑动） ---------- */
  var certBox = $('.cert-slider-detail');
  if (certBox) {
    var certTrack = $('.cert-track', certBox);
    var certPrev = $('.cert-prev', certBox);
    var certNext = $('.cert-next', certBox);
    var certItems = $$('.cert-item', certTrack);
    var certCount = certItems.length;
    var certPerView = 1;
    var certPositions = 1;
    var certPos = 0;
    var certJumpTimer = null;
    var certBusy = false;

    function certGap() {
      var cs = getComputedStyle(certTrack);
      var g = parseFloat(cs.columnGap || cs.gap || '0');
      return isNaN(g) ? 0 : g;
    }
    function certStep() {
      var it = certTrack.querySelector('.cert-item');
      return it ? it.getBoundingClientRect().width + certGap() : 0;
    }
    function certBuild() {
      var raw = certItems.map(function (it) { return it.outerHTML; });
      certPerView = Math.max(1, Math.round(certTrack.clientWidth / certStep()));
      certPositions = Math.max(1, certCount - certPerView + 1);
      var head = '', tail = '';
      for (var i = 0; i < certPerView; i++) {
        head += raw[(certCount - certPerView + i + certCount) % certCount];
        tail += raw[i];
      }
      certTrack.innerHTML = head + raw.join('') + tail;
      if (certPos > certPositions - 1) certPos = certPositions - 1;
      if (certPos < 0) certPos = 0;
      certSnap(certPos, false);
    }
    function certSnap(i, animate) {
      certTrack.style.transition = animate === false ? 'none' : 'transform 0.5s ease';
      certPos = i;
      certTrack.style.transform = 'translateX(' + (-certStep() * (i + certPerView)) + 'px)';
      if (animate === false) {
        void certTrack.offsetWidth;
        certTrack.style.transition = 'transform 0.5s ease';
      }
    }
    function certSettle() {
      if (certJumpTimer) { clearTimeout(certJumpTimer); certJumpTimer = null; }
      var maxP = certPositions - 1 + certPerView;
      if (certPos >= maxP) certSnap(certPos - maxP, false);
      else if (certPos <= -certPerView) certSnap(certPositions - 1, false);
      certBusy = false;
    }
    certTrack.addEventListener('transitionend', function (e) {
      if (e.propertyName === 'transform') certSettle();
    });
    function certGo(p) {
      if (certBusy) return;
      certBusy = true;
      certSnap(p, true);
      certJumpTimer = setTimeout(certSettle, 600);
    }
    function certNextF() { certGo(certPos + 1); }
    function certPrevF() { certGo(certPos - 1); }
    if (certPrev) certPrev.addEventListener('click', function (e) { e.preventDefault(); certPrevF(); });
    if (certNext) certNext.addEventListener('click', function (e) { e.preventDefault(); certNextF(); });
    // 触屏滑动
    var cx = 0, sw = false;
    certTrack.addEventListener('touchstart', function (e) { cx = e.touches[0].clientX; sw = true; }, { passive: true });
    certTrack.addEventListener('touchend', function (e) {
      if (!sw) return; sw = false;
      var dx = e.changedTouches[0].clientX - cx;
      if (Math.abs(dx) > 40) dx < 0 ? certNextF() : certPrevF();
    }, { passive: true });

    certBuild();
    window.addEventListener('resize', function () { certBuild(); });
  }

  /* ---------- Inquiry button smooth-scroll to #quote ---------- */
  $$('[data-scroll]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var target = document.querySelector(btn.getAttribute('data-scroll'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ---------- 视频封面兜底：poster 字段未填时，抓取视频第一帧作为封面 ---------- */
  $$('.video-box video').forEach(function (v) {
    if (v.hasAttribute('poster')) return;                    // 已填封面则不处理
    var srcEl = v.querySelector('source');
    if (!srcEl || !srcEl.getAttribute('src')) return;
    // 用独立临时 video 预载抓帧，不影响页面上 preload="none" 的真实 video
    var tmp = document.createElement('video');
    tmp.muted = true;
    tmp.preload = 'auto';
    tmp.src = srcEl.getAttribute('src');
    tmp.addEventListener('loadeddata', function () {
      try { tmp.currentTime = 0.1; } catch (e) {}           // 偏移 0.1s 避开纯黑首帧
    });
    tmp.addEventListener('seeked', function () {
      try {
        if (!tmp.videoWidth || !tmp.videoHeight) return;
        var c = document.createElement('canvas');
        c.width = tmp.videoWidth;
        c.height = tmp.videoHeight;
        c.getContext('2d').drawImage(tmp, 0, 0, tmp.videoWidth, tmp.videoHeight);
        v.setAttribute('poster', c.toDataURL('image/jpeg', 0.72));
      } catch (e) {}
    });
    tmp.load();
  });
})();
