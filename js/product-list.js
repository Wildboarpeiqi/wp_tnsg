/* ==========================================================================
   Product List page script (archive-product.php + taxonomy-product_category.php)
   Page-specific JS only. Shared interactions live in js/main.js (homepage).
   Mobile category nav: tap the hamburger to expand/collapse the
   product_category list.
   Loaded by functions.php jc_assets() on product archive / taxonomy.
   ========================================================================== */
(function () {
  'use strict';

  var mnav = document.getElementById('plMnav');
  var mnavBtn = document.getElementById('plMnavBtn');
  var mnavList = document.getElementById('plMnavList');
  if (!mnav || !mnavBtn || !mnavList) return;

  function setOpen(isOpen) {
    mnav.classList.toggle('open', isOpen);
    mnavBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    if (isOpen) {
      mnavList.removeAttribute('hidden');
    } else {
      mnavList.setAttribute('hidden', '');
    }
  }

  mnavBtn.addEventListener('click', function () {
    var isOpen = !mnav.classList.contains('open');
    setOpen(isOpen);
  });

  // Close the dropdown when a category link is tapped (mobile)
  var links = mnav.querySelectorAll('.pl-mnav-list a');
  for (var i = 0; i < links.length; i++) {
    links[i].addEventListener('click', function () {
      setOpen(false);
    });
  }

  // Close when tapping outside the nav bar
  document.addEventListener('click', function (e) {
    if (!mnav.contains(e.target)) {
      setOpen(false);
    }
  });
})();