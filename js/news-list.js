/* ==========================================================================
   News List page script (news/index.html)
   Page-specific JS only. Shared scripts live in js/main.js (homepage).
   Static demo: category filter + pagination driven by a plain front-end
   dataset. On WP conversion this file is replaced by server-rendered
   category archives + the_posts_pagination(); the HTML structure and
   CSS class names are kept so styling carries over.
   ========================================================================== */
(function () {
  'use strict';

  var PER_PAGE = 6; // WP 端由 reading settings / 后台每页条数控制

  var tabs = Array.prototype.slice.call(document.querySelectorAll('.news-tab'));
  var cards = Array.prototype.slice.call(document.querySelectorAll('.news-card'));
  var grid = document.getElementById('newsGrid');
  var pagination = document.getElementById('newsPagination');
  if (!grid || !pagination || cards.length === 0) return;

  var pageBtns = Array.prototype.slice.call(pagination.querySelectorAll('.pagi-btn'));
  var prevBtn = pagination.querySelector('.pagi-prev');
  var nextBtn = pagination.querySelector('.pagi-next');
  var numBtns = Array.prototype.slice.call(pagination.querySelectorAll('.pagi-num'));

  var currentCat = 'all';
  var currentPage = 1;

  /* ---------- 计算当前分类下的可见卡片 ---------- */
  function visibleCards() {
    return cards.filter(function (card) {
      return currentCat === 'all' || card.getAttribute('data-cat') === currentCat;
    });
  }

  /* ---------- 重算分页：页数变化时重建数字按钮 ---------- */
  function rebuildPagination(total) {
    var pages = Math.max(1, Math.ceil(total / PER_PAGE));
    if (currentPage > pages) currentPage = pages;

    // 重建数字按钮（保留 prev/next）
    numBtns.forEach(function (b) { b.remove(); });
    numBtns = [];
    for (var i = 1; i <= pages; i++) {
      (function (page) {
        var btn = document.createElement('button');
        btn.className = 'pagi-btn pagi-num' + (page === currentPage ? ' active' : '');
        btn.setAttribute('data-page', page);
        btn.setAttribute('aria-label', 'Page ' + page);
        btn.textContent = page;
        btn.addEventListener('click', function () { goToPage(page); });
        pagination.insertBefore(btn, nextBtn);
        numBtns.push(btn);
      })(i);
    }

    // 单页时隐藏整个分页区（WP 端 the_posts_pagination 同样不输出）
    pagination.style.display = pages <= 1 ? 'none' : '';
  }

  /* ---------- 渲染当前页 ---------- */
  function render() {
    var visible = visibleCards();
    var start = (currentPage - 1) * PER_PAGE;
    var end = start + PER_PAGE;

    cards.forEach(function (card) {
      var show = visible.indexOf(card) >= start && visible.indexOf(card) < end;
      card.style.display = show ? '' : 'none';
    });

    // 同步按钮高亮 / 禁用态
    numBtns.forEach(function (b) {
      b.classList.toggle('active', parseInt(b.getAttribute('data-page'), 10) === currentPage);
    });
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === Math.max(1, Math.ceil(visible.length / PER_PAGE));

    // 回到列表顶部（小屏更友好）
    var top = grid.getBoundingClientRect().top + window.scrollY - 120;
    if (window.scrollY > top) window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
  }

  function goToPage(page) {
    currentPage = page;
    render();
  }

  /* ---------- 分类 tab 点击过滤（WP 端为分类归档跳转） ---------- */
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      if (tab.classList.contains('active')) return;
      tabs.forEach(function (t) {
        var active = t === tab;
        t.classList.toggle('active', active);
        t.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      currentCat = tab.getAttribute('data-cat');
      currentPage = 1;
      rebuildPagination(visibleCards().length);
      render();
    });
  });

  /* ---------- 上一页 / 下一页 ---------- */
  if (prevBtn) prevBtn.addEventListener('click', function () {
    if (currentPage > 1) goToPage(currentPage - 1);
  });
  if (nextBtn) nextBtn.addEventListener('click', function () {
    var pages = Math.max(1, Math.ceil(visibleCards().length / PER_PAGE));
    if (currentPage < pages) goToPage(currentPage + 1);
  });

  /* ---------- 初始化 ---------- */
  rebuildPagination(cards.length);
  render();
})();
