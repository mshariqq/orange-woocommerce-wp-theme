<?php
/**
 * SnapServe Supply — footer.php
 * Premium, unified dark eCommerce footer.
 */
?>

</main>

<!-- SECTION 1: HOW IT WORKS (Premium Compact CTA) ================= -->
<section class="ss-how-it-works ss-hiw-premium">
  <div class="container">
    <div class="row ss-hiw-grid g-4 align-items-center">
      
      <!-- Step 1 -->
      <div class="col-lg-4 mt-0">
        <div class="ss-hiw-item">
          <div class="ss-hiw-visual">
            <span class="ss-hiw-step">01</span>
            
          </div>
          <div class="ss-hiw-content">
            <h3>Search Model</h3>
            <p>Enter your appliance model to find exact matches.</p>
          </div>
          <div class="ss-hiw-connector"></div>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-lg-4 mt-0">
        <div class="ss-hiw-item">
          <div class="ss-hiw-visual">
            <span class="ss-hiw-step">02</span>
            
          </div>
          <div class="ss-hiw-content">
            <h3>Find Your Part</h3>
            <p>Browse 100% genuine OEM parts for your model.</p>
          </div>
          <div class="ss-hiw-connector"></div>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-lg-4 mt-0">
        <div class="ss-hiw-item">
          <div class="ss-hiw-visual">
            <span class="ss-hiw-step">03</span>
            
          </div>
          <div class="ss-hiw-content">
            <h3>Fast Delivery</h3>
            <p>Same-day dispatch to get your repair started fast.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- SECTION 2: UNIFIED DARK FOOTER ========================= -->
<footer class="ss-merged-footer" itemscope itemtype="https://schema.org/WPFooter">
  <div class="container">
    
    <!-- Top Row: Newsletter & Main Links -->
    <div class="row ss-footer-top">
      
      <!-- Newsletter -->
      <div class="col-lg-5 mb-5 mb-lg-0">
        <div class="ss-footer-newsletter-minimal">
          <h5>Join our mailing list</h5>
          <p>Get exclusive part deals and appliance maintenance tips.</p>
          <?php echo do_shortcode('[fluentform id="2"]'); ?>
        </div>
      </div>

      <!-- Links Column 1: Customer Service -->
      <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
        <div class="ss-footer-links-col">
          <h5>Customer Service</h5>
          <?php
          wp_nav_menu([
            'theme_location' => 'footer-customer-service',
            'container'      => false,
            'menu_class'     => 'ss-footer-menu-list',
            'fallback_cb'    => false,
            'depth'          => 1,
          ]);
          ?>
        </div>
      </div>

      <!-- Links Column 2: Our Policies -->
      <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
        <div class="ss-footer-links-col">
          <h5>Our Policies</h5>
          <?php
          wp_nav_menu([
            'theme_location' => 'footer-policies',
            'container'      => false,
            'menu_class'     => 'ss-footer-menu-list',
            'fallback_cb'    => false,
            'depth'          => 1,
          ]);
          ?>
        </div>
      </div>

      <!-- Links Column 3: Company Info -->
      <div class="col-lg-3 col-md-4 mb-4 mb-lg-0">
        <div class="ss-footer-links-col">
          <h5>Company Info</h5>
          <?php
          wp_nav_menu([
            'theme_location' => 'footer-company-info',
            'container'      => false,
            'menu_class'     => 'ss-footer-menu-list',
            'fallback_cb'    => false,
            'depth'          => 1,
          ]);
          ?>
          <div class="ss-footer-contact-info mt-3">
            <div class="contact-item">
              <i class="bi bi-envelope"></i>
              <span><?php echo esc_html( get_theme_mod( 'ss_contact_email', 'admin@snapserveco.com' ) ); ?></span>
            </div>
<!--             <div class="contact-item mt-2">
              <i class="bi bi-telephone"></i>
              <span><?php echo esc_html( get_theme_mod( 'ss_contact_phone', '(888) 123-4567' ) ); ?></span>
            </div> -->
			  <div class="contact-item mt-2 align-items-start">
  <i class="bi bi-geo-alt"></i>
  <span class="ss-footer-address">
    SnapServe Solutions LLC<br>
    PO Box 11089<br>
    Riviera Beach, FL 33404
  </span>
</div>
          </div>
        </div>
      </div>

    </div>

    <hr class="ss-footer-divider">

    <!-- Bottom Row: Copyright, Socials, Payments -->
    <div class="row align-items-center ss-footer-bottom-minimal">
      
      <!-- Copyright & Trust -->
      <div class="col-md-4 mb-4 mb-md-0">
        <p class="ss-copyright">
          &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( get_theme_mod( 'ss_dba_name', 'SnapServe Supply' ) ); ?>.
          <span class="ms-3">Secure Checkout • Genuine Parts</span>
        </p>
      </div>

      <!-- Social Icons -->
      <div class="col-md-4 text-center mb-4 mb-md-0">
        <div class="ss-social-minimal">
          <a href="<?php echo esc_url( get_theme_mod( 'ss_social_facebook', '#' ) ); ?>" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="<?php echo esc_url( get_theme_mod( 'ss_social_instagram', '#' ) ); ?>" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="<?php echo esc_url( get_theme_mod( 'ss_social_youtube', '#' ) ); ?>" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <!-- Payment Icons -->
      <div class="col-md-4 text-md-end">
        <div class="ss-payment-icons">
          <i class="bi bi-credit-card-2-front" title="Secure Payment"></i>
          <i class="bi bi-shield-lock" title="SSL Secured"></i>
          <span class="ss-payment-label">Stripe Payments</span>
        </div>
      </div>

    </div>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
