<main/>
<footer class="ss-footer">

    <!-- TOP SECTION -->
    <div class="container ss-footer-main">
        <div class="row gy-4 align-items-start">

            <!-- Footer Column 1 -->
            <div class="col-lg-4">
                <?php if(is_active_sidebar('footer_col_1')): ?>
                    <?php dynamic_sidebar('footer_col_1'); ?>
                <?php endif; ?>
            </div>

            <!-- Footer Column 2 -->
            <div class="col-6 col-lg-2">
                <?php if(is_active_sidebar('footer_col_2')): ?>
                    <?php dynamic_sidebar('footer_col_2'); ?>
                <?php endif; ?>
            </div>

            <!-- Footer Column 3 -->
            <div class="col-6 col-lg-3">
                <?php if(is_active_sidebar('footer_col_3')): ?>
                    <?php dynamic_sidebar('footer_col_3'); ?>
                <?php endif; ?>
            </div>

            <!-- Footer Column 4 -->
            <div class="col-lg-3">
                <?php if(is_active_sidebar('footer_col_4')): ?>
                    <?php dynamic_sidebar('footer_col_4'); ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- BOTTOM SECTION -->
    <div class="ss-footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <?php if(is_active_sidebar('footer_bottom')): ?>
                <?php dynamic_sidebar('footer_bottom'); ?>
            <?php else: ?>
                <p>© <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
            <?php endif; ?>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
<script>
	document.addEventListener('DOMContentLoaded', function () {

  // =====================
  // WAIT FOR BOOTSTRAP
  // =====================
  function waitForBootstrap(callback) {
    if (typeof bootstrap !== 'undefined') {
      callback();
    } else {
      setTimeout(function () { waitForBootstrap(callback); }, 50);
    }
  }

  // =====================
  // STICKY HEADER SHADOW
  // =====================
  const header = document.querySelector('.ss-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('ss-scrolled', window.scrollY > 10);
    });
  }

  // =====================
  // SEARCH OVERLAY
  // =====================
  const searchToggle  = document.getElementById('searchToggle');
  const searchOverlay = document.getElementById('searchOverlay');

  function openSearch() {
    searchOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    const input = searchOverlay.querySelector('input');
    if (input) setTimeout(() => input.focus(), 100);
  }

  function closeSearch() {
    searchOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (searchToggle && searchOverlay) {
    // Open on button click
    searchToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      openSearch();
    });

    // Close when clicking the dark backdrop (overlay itself, not the search box)
    searchOverlay.addEventListener('click', function (e) {
      // only close if click is directly on overlay, not on child elements
      if (e.target === searchOverlay) {
        closeSearch();
      }
    });

    // Close on ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeSearch();
    });
  }

  // =====================
  // DESKTOP NAVBAR DROPDOWN
  // =====================
  const dropdowns = document.querySelectorAll('.ss-navbar .menu-item-has-children');

  dropdowns.forEach(function (item) {
    const submenu = item.querySelector('.sub-menu');
    if (!submenu) return;

    item.addEventListener('mouseenter', function () {
      if (window.innerWidth >= 992) submenu.style.display = 'block';
    });
    item.addEventListener('mouseleave', function () {
      if (window.innerWidth >= 992) submenu.style.display = '';
    });
  });

  // =====================
  // MOBILE OFFCANVAS
  // =====================
  waitForBootstrap(function () {
    const mobileMenuEl = document.getElementById('mobileMenu');
    if (!mobileMenuEl) return;

    // Kill any instance Bootstrap auto-created via data attributes
    const existing = bootstrap.Offcanvas.getInstance(mobileMenuEl);
    if (existing) existing.dispose();

    // Single clean instance — NO animation (eliminates flicker)
    const offcanvas = new bootstrap.Offcanvas(mobileMenuEl, {
      backdrop: true,
      scroll: false,
      keyboard: true
    });

    // Hamburger button
    const toggleBtn = document.querySelector('[data-bs-target="#mobileMenu"]');
    if (toggleBtn) {
      // Remove Bootstrap's own data attrs to prevent double-firing
      toggleBtn.removeAttribute('data-bs-toggle');
      toggleBtn.removeAttribute('data-bs-target');

      toggleBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        offcanvas.toggle();
      });
    }

    // Close on nav link click (not submenu toggles)
    mobileMenuEl.addEventListener('click', function (e) {
      const link = e.target.closest('a');
      if (link && !link.closest('.menu-item-has-children .sub-menu') === false) return;
      if (link) offcanvas.hide();
    });

    // Mobile submenu tap toggle
    const mobileDropdowns = mobileMenuEl.querySelectorAll('.menu-item-has-children');
    mobileDropdowns.forEach(function (item) {
      const link = item.querySelector(':scope > a');
      const submenu = item.querySelector(':scope > .sub-menu');
      if (!link || !submenu) return;

      link.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        // Close others
        mobileDropdowns.forEach(function (other) {
          if (other !== item) {
            const otherSub = other.querySelector(':scope > .sub-menu');
            if (otherSub) otherSub.classList.remove('open');
          }
        });
        submenu.classList.toggle('open');
      });
    });
  });

  // =====================
  // CART BADGE SYNC
  // =====================
  function updateCartBadges() {
    const badges = document.querySelectorAll('.ss-cart b');
    const count  = document.querySelector('.woocommerce-cart-count');
    if (count) badges.forEach(b => b.textContent = count.textContent);
  }
  document.body.addEventListener('wc_fragments_refreshed', updateCartBadges);
  document.body.addEventListener('added_to_cart', updateCartBadges);

});
</script>

</body>
</html>