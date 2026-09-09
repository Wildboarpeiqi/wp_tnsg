/* ==========================================================================
   About Us 页专属脚本（about.js）
   Vanilla JS, zero dependencies.
   1) CNC Machining 左列轮播：一屏多张（PC 3 / 平板 2 / 手机 1），一次动 1 张，
      底部圆点分页 + 左右箭头 + 触摸滑动 + hover 暂停；
      无缝循环（与证书轮播同方案）：克隆头尾各"每屏张数"，滑入克隆区动画结束后
      无动画跳回真实对应位置（内容一致，视觉无感），像车轮一样永远循环
   2) Testing equipment 左大图 + 右一行 2 张缩略图窗口联动：
      窗口显示"当前大图的下一张起 2 张"（不与大图重复），点击缩略图切换大图并重排窗口；
      底部左右箭头与左侧大图底部对齐；3 秒自动轮播；同样无缝循环（每屏 1 张）
   证书轮播由公共 js/main.js 接管（cert-slider 逐张切换 + 无缝循环）。
   ========================================================================== */
(function () {
  'use strict';

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  /* ---------- CNC Machining 轮播（一次动 1 张 + 圆点分页 + 无缝循环） ---------- */
  var cncRoot = $('#cncSlider');
  var cncRebuildFn = null;   // 暴露给 resize（严格模式下 if 块内函数声明块外不可见）
  if (cncRoot) {
    var cncTrack = $('#cncTrack', cncRoot);
    var cncViewport = $('.cnc-viewport', cncRoot);
    var cncSlides = $$('.cnc-slide', cncRoot);
    var cncDotsBox = $('#cncDots', cncRoot);
    var cncCount = cncSlides.length;
    var cncPv = 3;            // 每屏张数（PC 3 / 平板 2 / 手机 1）
    var cncPositions = 1;     // 真实位置数 = count - pv + 1（圆点数量）
    var cncPos = 0;           // 当前 track 位置（真实区 0..positions-1，克隆过渡区可越界）
    var cncCurrent = 0;       // 圆点激活位置（真实区）
    var cncTimer = null;
    var cncJumpTimer = null;
    var cncBusy = false;      // 动画期间锁定，杜绝连点瞬移
    var cncRawSlides = cncSlides;   // 原始 6 张快照：cncBuild 重建基准（防 resize 时基于含克隆列表再克隆，track 无限累积）

    function cncPerView() {
      var w = window.innerWidth;
      if (w > 960) return 3;   // PC：一屏 3 张（原站 rownum=3，保持不变）
      return 2;                // 移动端（手机+平板）：一屏 2 张（2026-09-06 用户要求：3 张拥挤且标题截断）
    }
    function cncStep() { return (cncViewport ? cncViewport.clientWidth : 0) / cncPv; }

    // 克隆头尾各 pv 张：track = [clone(最后 pv 张), 真实 6 张, clone(最前 pv 张)]
    // 真实第 1 屏在 track index = pv 处；translateX 用 (pos + pv) 计算
    function cncBuild() {
      cncPv = cncPerView();
      cncPositions = Math.max(1, cncCount - cncPv + 1);
      var raw = cncRawSlides.map(function (s) { return s.outerHTML; });
      var head = '', tail = '';
      for (var i = 0; i < cncPv; i++) {
        head += raw[(cncCount - cncPv + i + cncCount) % cncCount];
        tail += raw[i];
      }
      cncTrack.innerHTML = head + raw.join('') + tail;
      cncSlides = $$('.cnc-slide', cncTrack);
      cncSlides.forEach(function (s) { s.style.flexBasis = (100 / cncPv) + '%'; });
      if (cncPos > cncPositions - 1) cncPos = cncPositions - 1;
      if (cncPos < 0) cncPos = 0;
      cncSnap(cncPos, false);
      renderCncDots();
    }
    function cncSnap(i, animate) {
      cncTrack.style.transition = animate === false ? 'none' : 'transform 0.6s ease';
      cncPos = i;
      cncTrack.style.transform = 'translateX(' + (-cncStep() * (i + cncPv)) + 'px)';
      if (animate === false) {
        void cncTrack.offsetWidth;
        cncTrack.style.transition = 'transform 0.6s ease';
      }
      // 圆点激活：真实区才更新（克隆过渡区停在边界）
      var real = i;
      if (real < 0) real = 0;
      if (real > cncPositions - 1) real = cncPositions - 1;
      cncCurrent = real;
      if (cncDotsBox) {
        Array.prototype.forEach.call(cncDotsBox.children, function (d, idx) {
          d.classList.toggle('active', idx === real);
        });
      }
    }
    // 动画结束后：位于克隆区则无动画跳回真实对应位置（内容完全一致，视觉无感）
    function cncSettle() {
      if (cncJumpTimer) { clearTimeout(cncJumpTimer); cncJumpTimer = null; }
      var wrapN = cncPositions + cncPv - 1;   // next 越界阈值（如 PC：4+3-1 = 6）
      if (cncPos >= wrapN) cncSnap(cncPos - wrapN, false);          // 尾部克隆区 → 第一屏
      else if (cncPos <= -cncPv) cncSnap(cncPositions - 1, false);  // 头部克隆区 → 最后屏
      cncBusy = false;
    }
    cncTrack.addEventListener('transitionend', function (e) {
      if (e.propertyName !== 'transform') return;
      cncSettle();
    });
    function cncGo(p) {
      if (cncBusy) return;
      cncBusy = true;
      cncSnap(p, true);
      cncJumpTimer = setTimeout(cncSettle, 700);   // 兜底：transitionend 万一未触发
    }
    function nextCnc() { cncGo(cncPos + 1); }
    function prevCnc() { cncGo(cncPos - 1); }
    function renderCncDots() {
      if (!cncDotsBox) return;
      cncDotsBox.innerHTML = '';
      for (var i = 0; i < cncPositions; i++) {
        (function (idx) {
          var b = document.createElement('button');
          b.type = 'button';
          b.className = 'cnc-dot' + (idx === cncCurrent ? ' active' : '');
          b.setAttribute('aria-label', 'Go to slide ' + (idx + 1));
          b.addEventListener('click', function () { cncGo(idx); startCnc(); });
          cncDotsBox.appendChild(b);
        })(i);
      }
    }
    function startCnc() { stopCnc(); cncTimer = setInterval(nextCnc, 3000); }
    function stopCnc() { if (cncTimer) { clearInterval(cncTimer); cncTimer = null; } }

    var cncPrev = $('.cnc-prev', cncRoot);
    var cncNext = $('.cnc-next', cncRoot);
    if (cncPrev) cncPrev.addEventListener('click', function (e) { e.preventDefault(); prevCnc(); startCnc(); });
    if (cncNext) cncNext.addEventListener('click', function (e) { e.preventDefault(); nextCnc(); startCnc(); });

    cncRoot.addEventListener('mouseenter', stopCnc);
    cncRoot.addEventListener('mouseleave', startCnc);

    var cncTouchX = 0;
    cncRoot.addEventListener('touchstart', function (e) { cncTouchX = e.touches[0].clientX; stopCnc(); }, { passive: true });
    cncRoot.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - cncTouchX;
      if (Math.abs(dx) > 40) { dx < 0 ? nextCnc() : prevCnc(); startCnc(); }
    }, { passive: true });

    cncBuild();
    startCnc();
    cncRebuildFn = cncBuild;   // 块外 resize 重建入口
  }

  /* ---------- Testing equipment（左大图 + 右一行 2 张窗口联动 + 无缝循环） ---------- */
  var testFeatured = $('#testFeatured');
  var testSnapFn = null;   // 暴露给 resize（同上）
  if (testFeatured) {
    var testTrack = $('#testMainTrack', testFeatured);
    var testSlides = $$('.test-featured-slide', testFeatured);
    var thumbsTrack = $('#testThumbs');
    var testCount = testSlides.length;
    var testPos = 1;          // track 位置：真实第 1 张在 index 1（index 0 为末尾克隆、index count+1 为开头克隆）
    var testCurrent = 0;      // 真实索引 0..count-1（缩略图窗口联动）
    var testTimer = null;
    var testJumpTimer = null;
    var testBusy = false;
    var WINDOW = 2;   // 窗口 = 当前大图之后的 2 张（不与大图重复）

    // 从大图 slides 提取数据（src / alt / 标题），供缩略图窗口渲染
    var testData = testSlides.map(function (sl) {
      var img = $('img', sl);
      var cap = $('.test-caption', sl);
      return {
        src: img ? img.getAttribute('src') : '',
        alt: img ? img.getAttribute('alt') : '',
        name: cap ? cap.textContent.trim() : ''
      };
    });

    // 克隆头尾各 1 张：track = [clone(最后), 真实 6 张, clone(最前)]
    function testBuild() {
      var lastNode = testSlides[testCount - 1].cloneNode(true);
      var firstNode = testSlides[0].cloneNode(true);
      testTrack.insertBefore(lastNode, testTrack.firstChild);
      testTrack.appendChild(firstNode);
      testSnap(1, false);
    }
    function testSnap(i, animate) {
      testTrack.style.transition = animate === false ? 'none' : 'transform 0.6s ease';
      testPos = i;
      testTrack.style.transform = 'translateX(' + (-testFeatured.clientWidth * i) + 'px)';
      if (animate === false) {
        void testTrack.offsetWidth;
        testTrack.style.transition = 'transform 0.6s ease';
      }
      var real = ((i - 1) % testCount + testCount) % testCount;
      testCurrent = real;
      renderThumbs(real);
    }
    // 动画结束后：位于克隆区则无动画跳回真实对应位置（内容一致，视觉无感）
    function testSettle() {
      if (testJumpTimer) { clearTimeout(testJumpTimer); testJumpTimer = null; }
      if (testPos > testCount) testSnap(1, false);            // 尾部克隆（= 第 1 张）→ 真实第 1 张
      else if (testPos < 1) testSnap(testCount, false);       // 头部克隆（= 最后一张）→ 真实最后一张
      testBusy = false;
    }
    testTrack.addEventListener('transitionend', function (e) {
      if (e.propertyName !== 'transform') return;
      testSettle();
    });
    function nextTest() {
      if (testBusy) return;
      testBusy = true;
      testSnap(testPos + 1, true);
      testJumpTimer = setTimeout(testSettle, 700);
    }
    function prevTest() {
      if (testBusy) return;
      testBusy = true;
      testSnap(testPos - 1, true);
      testJumpTimer = setTimeout(testSettle, 700);
    }
    function goTest(real) {
      // 点击缩略图直达：打断进行中的动画/兜底计时
      if (testJumpTimer) { clearTimeout(testJumpTimer); testJumpTimer = null; }
      testBusy = false;
      testSnap(real + 1, true);
      testJumpTimer = setTimeout(testSettle, 700);
    }
    // 缩略图窗口 = 当前大图的下一张起 2 张（循环，不与大图重复）；
    // 轨道为 flex 露边布局：第一张完整显示 + 第二张露一小部分
    function renderThumbs(i) {
      if (!thumbsTrack) return;
      thumbsTrack.innerHTML = '';
      for (var j = 0; j < WINDOW; j++) {
        var idx = (i + 1 + j) % testCount;
        var d = testData[idx];
        (function (realIdx) {
          var li = document.createElement('li');
          li.className = 'test-thumb';
          li.setAttribute('data-index', realIdx);
          var imgBox = document.createElement('div');
          imgBox.className = 'test-img';
          var img = document.createElement('img');
          img.loading = 'lazy';
          img.src = d.src;
          img.alt = d.alt;
          imgBox.appendChild(img);
          var name = document.createElement('p');
          name.className = 'test-thumb-name';
          name.textContent = d.name;
          li.appendChild(imgBox);
          li.appendChild(name);
          li.addEventListener('click', function () { goTest(realIdx); startTest(); });
          thumbsTrack.appendChild(li);
        })(idx);
      }
    }
    function startTest() { stopTest(); testTimer = setInterval(nextTest, 3000); }
    function stopTest() { if (testTimer) { clearInterval(testTimer); testTimer = null; } }

    var testPrev = $('.test-prev');
    var testNext = $('.test-next');
    if (testPrev) testPrev.addEventListener('click', function (e) { e.preventDefault(); prevTest(); startTest(); });
    if (testNext) testNext.addEventListener('click', function (e) { e.preventDefault(); nextTest(); startTest(); });

    testFeatured.addEventListener('mouseenter', stopTest);
    testFeatured.addEventListener('mouseleave', startTest);
    if (thumbsTrack) {
      thumbsTrack.addEventListener('mouseenter', stopTest);
      thumbsTrack.addEventListener('mouseleave', startTest);
    }

    var testTouchX = 0;
    testFeatured.addEventListener('touchstart', function (e) { testTouchX = e.touches[0].clientX; stopTest(); }, { passive: true });
    testFeatured.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - testTouchX;
      if (Math.abs(dx) > 40) { dx < 0 ? nextTest() : prevTest(); startTest(); }
    }, { passive: true });

    testBuild();
    startTest();
    testSnapFn = testSnap;   // 块外 resize 重算入口
  }

  /* ---------- resize：重建 CNC 克隆轨道 / 重算大图位移 ---------- */
  var resizeTimer = null;
  window.addEventListener('resize', function () {
    if (resizeTimer) clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (cncRebuildFn) cncRebuildFn();
      if (testSnapFn) testSnapFn(testPos, false);
    }, 200);
  });

  /* ---------- Reveal 兜底 ---------- */
  setTimeout(function () {
    var els = document.querySelectorAll('.section-head, .listBox, .stat-card, .newsBox, .service-card, .industry-item');
    Array.prototype.forEach.call(els, function (el) {
      if (!el.classList.contains('revealed')) el.classList.add('revealed');
    });
  }, 2000);
})();
