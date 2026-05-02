<?php
/* ============================================
   SNAPSERVE SUPPLY — functions.php
   ============================================ */


/* ============================================
   1. THEME SETUP
   ============================================ */
add_action( 'after_setup_theme', 'snapserve_theme_setup' );
function snapserve_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'primary'                 => 'Primary Menu',
        'topbar'                  => 'Top Bar Menu',
        'footer-appliances'       => 'Footer — Shop by Appliance',
        'footer-brands'           => 'Footer — Top Brands',
        'footer-policies'         => 'Footer — Our Policies',
        'footer-customer-service' => 'Footer — Customer Service',
        'footer-company-info'     => 'Footer — Company Info',
    ) );
}


/* ============================================
   2. ENQUEUE STYLES
   ============================================ */
add_action( 'wp_enqueue_scripts', 'snapserve_enqueue_styles' );
function snapserve_enqueue_styles() {
    $uri = get_template_directory_uri();
    $dir = get_template_directory();

    wp_enqueue_style( 'bootstrap',    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2' );
    wp_enqueue_style( 'ss-open-sans', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap', array(), null );
    wp_enqueue_style( 'swiper-css',   'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11' );

    wp_enqueue_style( 'base',       $uri . '/css/base.css',       array( 'bootstrap' ), '1.0' );
    wp_enqueue_style( 'header',     $uri . '/css/header.css',     array( 'base' ),      '1.0' );
    wp_enqueue_style( 'navbar',     $uri . '/css/navbar.css',     array( 'base' ),      '1.0' );
    wp_enqueue_style( 'components', $uri . '/css/components.css', array( 'base' ),      '1.0' );
    wp_enqueue_style( 'mobile',     $uri . '/css/mobile.css',     array( 'base' ),      '1.0' );
    wp_enqueue_style( 'sswc-css',   $uri . '/css/sswc.css',       array( 'base' ),      '1.0' );
    wp_enqueue_style( 'main-style', get_stylesheet_uri(),         array( 'base' ),      '1.0' );

    // Footer stylesheet — versioned by file modification time
    $footer_css = $dir . '/css/footer.css';
    if ( file_exists( $footer_css ) ) {
        wp_enqueue_style( 'ss-footer', $uri . '/css/footer.css', array( 'ss-open-sans' ), filemtime( $footer_css ) );
    }

    if ( is_singular( 'product' ) ) {
        wp_enqueue_style( 'single-page-css', $uri . '/css/single.css', array(), '1.0' );
    }
}


/* ============================================
   3. ENQUEUE SCRIPTS
   ============================================ */
add_action( 'wp_enqueue_scripts', 'snapserve_enqueue_scripts' );
function snapserve_enqueue_scripts() {
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true );
    wp_enqueue_script( 'swiper-js',    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true );
    wp_enqueue_script( 'ss-scripts',   get_template_directory_uri() . '/js/ss.js', array( 'bootstrap-js' ), '1.1', true );

    wp_localize_script( 'ss-scripts', 'ss_ajax', array(
        'url'      => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ss-ajax-nonce' ),
        'shop_url' => get_permalink( wc_get_page_id( 'shop' ) ),
    ) );
}


/* ============================================
   4. DYNAMIC CSS — LOGO SIZE
   ============================================ */
add_action( 'wp_head', 'snapserve_logo_dynamic_css' );
function snapserve_logo_dynamic_css() {
    $width  = absint( get_theme_mod( 'snapserve_logo_width', 180 ) );
    $height = absint( get_theme_mod( 'snapserve_logo_height', 50 ) );
    echo '<style>.custom-logo{width:' . $width . 'px;height:' . $height . 'px;object-fit:contain;}</style>';
}


/* ============================================
   5. CUSTOMIZER — LOGO + FOOTER SETTINGS
   ============================================ */
add_action( 'customize_register', 'snapserve_customizer_settings' );
function snapserve_customizer_settings( $wp_customize ) {

    // --- Logo Section ---
    $wp_customize->add_section( 'snapserve_logo_settings', array(
        'title'    => 'Logo Settings',
        'priority' => 30,
    ) );
    $wp_customize->add_setting( 'snapserve_logo_width',  array( 'default' => '180', 'sanitize_callback' => 'absint' ) );
    $wp_customize->add_setting( 'snapserve_logo_height', array( 'default' => '50',  'sanitize_callback' => 'absint' ) );
    $wp_customize->add_control( 'snapserve_logo_width',  array( 'label' => 'Logo Width (px)',  'section' => 'snapserve_logo_settings', 'type' => 'number' ) );
    $wp_customize->add_control( 'snapserve_logo_height', array( 'label' => 'Logo Height (px)', 'section' => 'snapserve_logo_settings', 'type' => 'number' ) );

    // --- Footer Panel ---
    $wp_customize->add_panel( 'ss_footer_panel', array(
        'title'       => 'SnapServe Footer Settings',
        'description' => 'Control footer text, contact info, social links, and legal names.',
        'priority'    => 160,
    ) );

    // Contact Info
    $wp_customize->add_section( 'ss_contact_section', array( 'title' => 'Contact Information', 'panel' => 'ss_footer_panel' ) );
    $wp_customize->add_setting( 'ss_contact_email', array( 'default' => 'admin@snapserveco.com', 'sanitize_callback' => 'sanitize_email', 'transport' => 'refresh' ) );
    $wp_customize->add_setting( 'ss_contact_phone', array( 'default' => '(888) 123-4567',         'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'ss_contact_email', array( 'label' => 'Contact Email Address', 'section' => 'ss_contact_section', 'type' => 'email' ) );
    $wp_customize->add_control( 'ss_contact_phone', array( 'label' => 'Contact Phone Number',  'section' => 'ss_contact_section', 'type' => 'text' ) );

    // Legal Names
    $wp_customize->add_section( 'ss_legal_section', array( 'title' => 'Legal / Copyright', 'panel' => 'ss_footer_panel' ) );
    $wp_customize->add_setting( 'ss_legal_name', array( 'default' => 'SnapServe Solutions, LLC', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
    $wp_customize->add_setting( 'ss_dba_name',   array( 'default' => 'SnapServe Supply',         'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'ss_legal_name', array( 'label' => 'Legal Entity Name', 'section' => 'ss_legal_section', 'type' => 'text' ) );
    $wp_customize->add_control( 'ss_dba_name',   array( 'label' => 'DBA / Trade Name',  'section' => 'ss_legal_section', 'type' => 'text' ) );

    // Social Links
    $wp_customize->add_section( 'ss_social_section', array( 'title' => 'Social Media Links', 'panel' => 'ss_footer_panel' ) );
    $socials = array(
        'ss_social_facebook'  => 'Facebook URL',
        'ss_social_instagram' => 'Instagram URL',
        'ss_social_youtube'   => 'YouTube URL',
    );
    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( $key, array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
        $wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'ss_social_section', 'type' => 'url' ) );
    }
}


/* ============================================
   6. WIDGET AREAS (FOOTER)
   ============================================ */
add_action( 'widgets_init', 'ss_register_all_sidebars' );
function ss_register_all_sidebars() {

    // --- Legacy footer columns (kept for backwards compatibility) ---
    $legacy = array( 'footer_col_1' => 'Footer Column 1', 'footer_col_2' => 'Footer Column 2', 'footer_col_3' => 'Footer Column 3', 'footer_col_4' => 'Footer Column 4' );
    foreach ( $legacy as $id => $name ) {
        register_sidebar( array(
            'name'          => $name,
            'id'            => $id,
            'before_widget' => '<div class="footer-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h6>',
            'after_title'   => '</h6>',
        ) );
    }
    register_sidebar( array(
        'name'          => 'Footer Bottom',
        'id'            => 'footer_bottom',
        'before_widget' => '<div class="footer-bottom-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ) );

    // --- New named footer widget areas ---
    $areas = array(
        'ss_prefooter_cta'       => array( 'name' => 'Footer — Pre-Footer CTA Banner',        'desc' => 'Banner above the footer. Leave empty for default.' ),
        'ss_footer_brand'        => array( 'name' => 'Footer — Col 1: Brand',                 'desc' => 'Logo, tagline, social icons.' ),
        'ss_footer_col2'         => array( 'name' => 'Footer — Col 2: Shop by Appliance',     'desc' => 'Navigation Menu widget or leave empty to auto-generate.' ),
        'ss_footer_col3'         => array( 'name' => 'Footer — Col 3: Top Brands',            'desc' => 'Navigation Menu widget or leave empty to auto-generate.' ),
        'ss_footer_col4'         => array( 'name' => 'Footer — Col 4: Policies',              'desc' => 'Navigation Menu widget or leave empty to auto-link policy pages.' ),
        'ss_footer_col5'         => array( 'name' => 'Footer — Col 5: Contact & Newsletter',  'desc' => 'Contact + newsletter. Leave empty for theme defaults.' ),
        'ss_footer_bottom_left'  => array( 'name' => 'Footer — Bottom Bar Left (Copyright)',  'desc' => 'Leave empty to auto-generate from Customizer settings.' ),
        'ss_footer_bottom_right' => array( 'name' => 'Footer — Bottom Bar Right (Trust)',     'desc' => 'Leave empty for default SSL / Secure Checkout chips.' ),
    );
    foreach ( $areas as $id => $args ) {
        register_sidebar( array(
            'id'            => $id,
            'name'          => $args['name'],
            'description'   => $args['desc'],
            'before_widget' => '<div id="%1$s" class="ss-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="ss-footer-heading">',
            'after_title'   => '</h4>',
        ) );
    }
}


/* ============================================
   7. FOOTER WIDGET STYLE HELPERS
   ============================================ */

// Ensure heading tags are correct for all footer widgets
add_filter( 'dynamic_sidebar_params', 'ss_footer_widget_classes' );
function ss_footer_widget_classes( $params ) {
    $footer_sidebars = array( 'ss_footer_brand', 'ss_footer_col2', 'ss_footer_col3', 'ss_footer_col4', 'ss_footer_col5', 'ss_footer_bottom_left', 'ss_footer_bottom_right', 'ss_prefooter_cta' );
    if ( in_array( $params[0]['id'], $footer_sidebars, true ) ) {
        $params[0]['before_title'] = '<h4 class="ss-footer-heading">';
        $params[0]['after_title']  = '</h4>';
    }
    return $params;
}

// Add ss-footer-links class to nav menus dropped into footer columns
add_filter( 'widget_nav_menu_args', 'ss_footer_nav_menu_class', 10, 3 );
function ss_footer_nav_menu_class( $nav_menu_args, $nav_menu, $args ) {
    $footer_cols = array( 'ss_footer_col2', 'ss_footer_col3', 'ss_footer_col4' );
    if ( isset( $args['id'] ) && in_array( $args['id'], $footer_cols, true ) ) {
        $nav_menu_args['menu_class'] = 'ss-footer-links';
        $nav_menu_args['container']  = false;
    }
    return $nav_menu_args;
}


/* ============================================
   8. LAZY LOADING IMAGES
   ============================================ */
add_filter( 'the_content',        'ss_lazy_load_images' );
add_filter( 'post_thumbnail_html','ss_lazy_load_images' );
add_filter( 'widget_text',        'ss_lazy_load_images' );
function ss_lazy_load_images( $content ) {
    // Only add loading="lazy" if not already present
    return preg_replace( '/<img(?![^>]*loading=)([^>]*)>/i', '<img$1 loading="lazy">', $content );
}


/* ============================================
   9. SWIPER INIT SCRIPT
   ============================================ */
add_action( 'wp_footer', 'ss_swiper_init_script' );
function ss_swiper_init_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper === 'undefined') return;
        new Swiper('.ss-cat-carousel', {
            slidesPerView: 1.2,
            spaceBetween: 16,
            loop: true,
            pagination: { el: '.ss-cat-pagination', clickable: true },
            navigation: { prevEl: '.ss-cat-prev', nextEl: '.ss-cat-next' },
            breakpoints: {
                576:  { slidesPerView: 2, spaceBetween: 16 },
                768:  { slidesPerView: 3, spaceBetween: 20 },
                992:  { slidesPerView: 4, spaceBetween: 24 },
                1200: { slidesPerView: 5, spaceBetween: 24 },
            }
        });
    });
    </script>
    <?php
}


/* ============================================
   10. WOOCOMMERCE — COMPATIBILITY TAB
   Reads "compatible_models" custom field.
   Format: comma-separated model numbers.
   ============================================ */

// Register tab — only if field has data
add_filter( 'woocommerce_product_tabs', 'snapserve_register_compat_tab' );
function snapserve_register_compat_tab( $tabs ) {
    global $product;
    if ( ! $product instanceof WC_Product ) return $tabs;
    if ( ! empty( get_post_meta( $product->get_id(), 'compatible_models', true ) ) ) {
        $tabs['fits_model'] = array(
            'title'    => __( 'Fits Model / Compatibility', 'snapserve' ),
            'priority' => 25,
            'callback' => 'snapserve_fits_model_tab_content',
        );
    }
    return $tabs;
}

function snapserve_fits_model_tab_content() {
    global $product;
    $compatible_models = get_post_meta( $product->get_id(), 'compatible_models', true );
    if ( empty( $compatible_models ) ) return;
    $models = array_filter( array_map( 'trim', explode( ',', $compatible_models ) ) );
    ?>
    <div class="snapserve-compat-tab">
        <div class="compat-header">
            <h3 class="compat-title"><?php esc_html_e( 'Compatible Appliance Models', 'snapserve' ); ?></h3>
            <p class="compat-desc"><?php esc_html_e( 'This part is confirmed to fit the following appliance models. Always verify your appliance model number — found on the data plate inside the door or on the back panel — before ordering.', 'snapserve' ); ?></p>
        </div>
        <div class="compat-search-wrap">
            <input type="text" id="compat-filter" class="compat-filter-input"
                   placeholder="<?php esc_attr_e( 'Filter by model number…', 'snapserve' ); ?>"
                   onkeyup="snapserveFilterModels(this.value)" />
        </div>
        <div class="compat-grid" id="compat-grid">
            <?php foreach ( $models as $model ) : ?>
                <div class="compat-model-chip" data-model="<?php echo esc_attr( strtolower( $model ) ); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <?php echo esc_html( strtoupper( $model ) ); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="compat-no-results" id="compat-no-results" style="display:none;"><?php esc_html_e( 'No matching models found.', 'snapserve' ); ?></p>
        <div class="compat-disclaimer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?php esc_html_e( "It is the customer's responsibility to verify compatibility before ordering. See our Disclaimer for full details.", 'snapserve' ); ?>
        </div>
    </div>
    <?php
}

// Compatibility tab CSS — single product pages only
add_action( 'wp_head', 'snapserve_compat_tab_css' );
function snapserve_compat_tab_css() {
    if ( ! is_product() ) return;
    ?>
    <style>
    .snapserve-compat-tab{padding:8px 0}
    .compat-header{margin-bottom:20px}
    .compat-title{font-size:18px;font-weight:700;color:#0A1E3F;margin:0 0 8px}
    .compat-desc{font-size:14px;color:#5a6a7e;line-height:1.6;max-width:640px}
    .compat-search-wrap{margin-bottom:16px}
    .compat-filter-input{width:100%;max-width:360px;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:14px;color:#1a2332;outline:none;transition:border-color .2s}
    .compat-filter-input:focus{border-color:#0A1E3F}
    .compat-grid{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px}
    .compat-model-chip{display:inline-flex;align-items:center;gap:6px;background:#f0f4f8;border:1.5px solid #e2e8f0;border-radius:4px;padding:6px 12px;font-size:12px;font-weight:600;color:#0A1E3F;font-family:monospace;letter-spacing:.04em;transition:background .2s,border-color .2s}
    .compat-model-chip svg{stroke:#70B354;flex-shrink:0}
    .compat-model-chip:hover{background:#e8f0f8;border-color:#0A1E3F}
    .compat-model-chip.hidden{display:none}
    .compat-no-results{font-size:14px;color:#94a3b8;padding:16px 0}
    .compat-disclaimer{display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#94a3b8;line-height:1.5;padding-top:16px;border-top:1px solid #f0f4f8;margin-top:8px}
    .compat-disclaimer svg{flex-shrink:0;margin-top:1px;stroke:#94a3b8}
    </style>
    <?php
}

// Compatibility tab JS — single product pages only
add_action( 'wp_footer', 'snapserve_compat_tab_js' );
function snapserve_compat_tab_js() {
    if ( ! is_product() ) return;
    ?>
    <script>
    function snapserveFilterModels(query){
        var q=query.toLowerCase().trim();
        var chips=document.querySelectorAll('.compat-model-chip');
        var shown=0;
        chips.forEach(function(chip){
            if(!q||chip.getAttribute('data-model').indexOf(q)!==-1){chip.classList.remove('hidden');shown++;}
            else{chip.classList.add('hidden');}
        });
        var el=document.getElementById('compat-no-results');
        if(el) el.style.display=(shown===0&&q!=='')?'block':'none';
    }
    </script>
    <?php
}


/* ============================================
   11. WOOCOMMERCE — "FITS X MODELS" BADGE
   Shows on product cards in shop/archive loops
   ============================================ */
add_action( 'woocommerce_after_shop_loop_item_title', 'ss_compat_count_badge', 5 );
function ss_compat_count_badge() {
    global $product;
    $compatible_models = get_post_meta( $product->get_id(), 'compatible_models', true );
    if ( empty( $compatible_models ) ) return;
    $count = count( array_filter( array_map( 'trim', explode( ',', $compatible_models ) ) ) );
    if ( $count > 0 ) {
        echo '<p class="compat-count-badge">'
           . '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#70B354" stroke-width="2.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> '
           . esc_html( sprintf( _n( 'Fits %d model', 'Fits %d models', $count, 'snapserve' ), $count ) )
           . '</p>';
    }
}

add_action( 'wp_head', 'ss_compat_badge_css' );
function ss_compat_badge_css() {
    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) return;
    echo '<style>.compat-count-badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#5a6a7e;margin:4px 0 0;font-weight:500}</style>';
}


/* ============================================
   12. WOOCOMMERCE — 15% RESTOCKING FEE NOTE
   Logs an order note on refund. Auto-waives if
   reason contains seller-fault keywords.
   ============================================ */
add_action( 'woocommerce_order_refunded', 'ss_restocking_fee_note', 10, 2 );
function ss_restocking_fee_note( $order_id, $refund_id ) {
    $refund = wc_get_order( $refund_id );
    $order  = wc_get_order( $order_id );
    if ( ! $refund || ! $order ) return;

    $reason        = strtolower( $refund->get_reason() );
    $skip_keywords = array( 'defective', 'wrong item', 'our error', 'damaged', 'incorrect item' );

    foreach ( $skip_keywords as $keyword ) {
        if ( strpos( $reason, $keyword ) !== false ) {
            $order->add_order_note( __( 'Restocking fee WAIVED — refund reason indicates seller error or defective item. Full refund issued per Return & Refund Policy.', 'snapserve' ) );
            return;
        }
    }

    $refund_total = abs( $refund->get_total() );
    $fee_amount   = round( $refund_total * 0.15 / 0.85, 2 );
    $order->add_order_note( sprintf(
        __( '15%% restocking fee applied per Return & Refund Policy. Refund issued: $%s. Estimated fee deducted: $%s.', 'snapserve' ),
        number_format( $refund_total, 2 ),
        number_format( $fee_amount, 2 )
    ) );
}


/* ============================================
   13. STOCK BADGE HELPER
   ============================================ */
function ss_get_stock_badge( $product ) {
    if ( ! $product instanceof WC_Product || ! $product->is_in_stock() ) return '';

    if ( $product->managing_stock() ) {
        $qty       = $product->get_stock_quantity();
        $threshold = absint( get_option( 'woocommerce_notify_low_stock_amount', 2 ) );
        if ( ! $qty || $qty <= 0 ) return '';

        if ( $qty <= $threshold ) {
            return '<span class="ss-stock-badge ss-stock-low"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" fill="#F47443"/></svg>Only ' . absint( $qty ) . ' Left</span>';
        }
        return '<span class="ss-stock-badge ss-stock-in"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" fill="#70B354"/></svg>In Stock</span>';
    }

    return '<span class="ss-stock-badge ss-stock-ready"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" fill="#70B354"/></svg>Ready to Ship</span>';
}

// Inject stock badge into WooCommerce shop/archive loop
add_action( 'woocommerce_before_shop_loop_item_title', 'ss_loop_stock_badge', 15 );
function ss_loop_stock_badge() {
    global $product;
    if ( $product instanceof WC_Product ) {
        echo ss_get_stock_badge( $product ); // phpcs:ignore WordPress.Security.EscapeOutput
    }
}


/* ============================================
   13b. CUSTOM RATING HELPER
   ============================================ */
function ss_get_rating_html( $product ) {
    if ( ! $product instanceof WC_Product ) return '';

    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();
    $average      = $product->get_average_rating();

    ob_start();
    ?>
    <div class="ss-rating">
        <?php if ( $review_count > 0 ) : ?>
            <div class="star-rating" title="<?php echo esc_attr( $average ); ?>">
                <?php echo wc_get_rating_html( $average, $rating_count ); ?>
            </div>
            <span class="review-count">(<?php echo intval( $review_count ); ?>)</span>
        <?php else : ?>
            <div class="ss-stars-filled">
                ★★★★★
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}


/* ============================================
   14. SHORTCODE — [ss_products]
   ============================================ */
add_shortcode( 'ss_products', 'ss_featured_products_shortcode' );
function ss_featured_products_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'title'    => 'Featured Products',
        'subtitle' => 'Top selling and high demand parts',
        'limit'    => 8,
        'category' => '',
    ), $atts, 'ss_products' );

    $products = new WP_Query( array(
        'post_type'           => 'product',
        'posts_per_page'      => intval( $atts['limit'] ),
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => 1,
        'suppress_filters'    => true,
    ) );

    ob_start();
    ?>
    <section class="ss-products">
        <div class="container">
            <div class="ss-section-head d-flex justify-content-between align-items-end">
                <div>
                    <h2><?php echo esc_html( $atts['title'] ); ?></h2>
                    <p><?php echo esc_html( $atts['subtitle'] ); ?></p>
                </div>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="ss-link">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="ss-product-grid">
                <?php if ( $products->have_posts() ) : ?>
                    <?php while ( $products->have_posts() ) : $products->the_post(); global $product; ?>
                        <div class="ss-product-card">
                            <div class="ss-product-img">
                                <a href="<?php the_permalink(); ?>">
                                    <?php $image_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ); ?>
                                    <?php if ( $image_url ) : ?>
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                    <?php else : ?>
                                        <img src="https://placehold.co/300x150?text=<?php echo urlencode( get_the_title() ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                    <?php endif; ?>
                                    <?php if ( $product->is_on_sale() ) : ?>
                                        <span class="ss-badge">Hot</span>
                                    <?php endif; ?>
                                    <?php echo ss_get_stock_badge( $product ); // phpcs:ignore ?>
                                </a>
                            </div>
                            <div class="ss-product-body">
                                <?php echo ss_get_rating_html( $product ); ?>
                                <h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                <p class="ss-product-meta"><?php echo strip_tags( wc_get_product_category_list( $product->get_id(), ' • ' ) ); ?></p>
                                <div class="ss-product-bottom">
                                    <div>
                                        <span class="ss-price"><?php echo wc_price( $product->get_price() ); ?></span>
                                        <?php if ( $product->is_on_sale() ) : ?>
                                            <span class="ss-old-price"><?php echo wc_price( $product->get_regular_price() ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="ss-add-cart"
                                            onclick="location.href='<?php echo esc_url( $product->add_to_cart_url() ); ?>'"
                                            aria-label="Add <?php the_title_attribute(); ?> to cart">
                                        <i class="bi bi-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'No products found.', 'snapserve' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}


/* ============================================
   15. SHORTCODE — [ss_hero_search]
   ============================================ */
add_shortcode( 'ss_hero_search', 'ss_hero_search_shortcode' );
function ss_hero_search_shortcode() {
    $shop_url   = get_permalink( wc_get_page_id( 'shop' ) );
    $search_val = isset( $_GET['s'] ) ? esc_attr( wp_unslash( $_GET['s'] ) ) : '';

    ob_start();
    ?>
    <form class="ss-hero-search" id="ss-hero-search-form"
          action="<?php echo esc_url( $shop_url ); ?>" method="GET" autocomplete="off">

        <div class="ss-search-box-hero" id="ss-search-wrapper">
            <i class="bi bi-search ss-search-icon"></i>
            <input type="text" id="ss-hero-input" name="s" class="ss-hero-input"
                   placeholder="<?php esc_attr_e( 'Enter model number, SKU, or part name...', 'snapserve' ); ?>"
                   value="<?php echo $search_val; ?>"
                   aria-label="<?php esc_attr_e( 'Search products', 'snapserve' ); ?>"
                   aria-autocomplete="list" aria-controls="ss-suggestions-box" aria-expanded="false">
            <input type="hidden" name="post_type" value="product">
            <span class="ss-search-spinner" id="ss-search-spinner" aria-hidden="true"></span>
            <button type="button" class="ss-search-clear" id="ss-search-clear"
                    aria-label="<?php esc_attr_e( 'Clear search', 'snapserve' ); ?>"
                    style="display:none;">
                <i class="bi bi-x-lg"></i>
            </button>
<!--             <button type="submit" class="ss-search-submit"><?php esc_html_e( 'Search', 'snapserve' ); ?></button> -->
            <div class="ss-suggestions-box" id="ss-suggestions-box" role="listbox" aria-label="<?php esc_attr_e( 'Search suggestions', 'snapserve' ); ?>"></div>
        </div>

<!--         <div class="ss-hero-btns">
            <a href="<?php echo esc_url( $shop_url ); ?>" class="ss-btn ss-btn-orange">
                <i class="bi bi-grid-3x3-gap"></i> <?php esc_html_e( 'Browse Parts', 'snapserve' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ss-btn ss-btn-navy">
                <i class="bi bi-chat-left-text"></i> <?php esc_html_e( 'Contact Us', 'snapserve' ); ?>
            </a>
        </div> -->

    </form>
    <?php
    return ob_get_clean();
}


/* ============================================
   16. SHORTCODE — [ss_categories]
   ============================================ */
add_shortcode( 'ss_categories', 'ss_categories_shortcode' );
function ss_categories_shortcode() {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => true,
        'exclude'    => get_option( 'default_product_cat' ),
    ) );

    if ( empty( $categories ) || is_wp_error( $categories ) ) return '';

    $shop_url = get_permalink( wc_get_page_id( 'shop' ) );

    ob_start();
    ?>
    <section class="ss-categories">
        <div class="container">
            <div class="ss-section-head d-flex justify-content-between align-items-end">
                <div>
                    <h2><?php esc_html_e( 'Shop by Category', 'snapserve' ); ?></h2>
                    <p><?php esc_html_e( 'Find the right parts faster with structured browsing', 'snapserve' ); ?></p>
                </div>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="ss-link">
                    <?php esc_html_e( 'View All', 'snapserve' ); ?> <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="ss-cat-carousel swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $categories as $cat ) :
                        $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                        $image        = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'full' ) : wc_placeholder_img_src( 'full' );
                        $cat_url      = get_term_link( $cat );
                        if ( is_wp_error( $cat_url ) ) continue;
                    ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( $cat_url ); ?>" class="ss-cat-card">
                                <img src="<?php echo esc_url( $image ); ?>" loading="eager" alt="<?php echo esc_attr( $cat->name ); ?>">
                                <div class="ss-cat-overlay"></div>
                                <div class="ss-cat-content">
                                    <h5><?php echo esc_html( $cat->name ); ?></h5>
                                    <span><?php echo intval( $cat->count ); ?>+ <?php esc_html_e( 'Products', 'snapserve' ); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="ss-cat-prev"><i class="bi bi-chevron-left"></i></div>
                <div class="ss-cat-next"><i class="bi bi-chevron-right"></i></div>
                <div class="ss-cat-pagination"></div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}


/* ============================================
   17. AJAX — PRODUCT SEARCH (returns HTML)
   ============================================ */
add_action( 'wp_ajax_ss_ajax_search',        'ss_ajax_search_callback' );
add_action( 'wp_ajax_nopriv_ss_ajax_search', 'ss_ajax_search_callback' );
function ss_ajax_search_callback() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ss-ajax-nonce' ) ) {
        wp_send_json_error( array( 'results' => '<div class="ss-no-results">Security verification failed.</div>' ) );
    }

    $query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
    if ( empty( $query ) ) {
        wp_send_json_success( array( 'results' => '' ) );
    }

    $products = ss_run_extended_search( $query, 8 );

    ob_start();
    if ( $products->have_posts() ) {
        echo '<ul class="ss-search-results-list">';
        while ( $products->have_posts() ) {
            $products->the_post();
            global $product;
            $thumbnail  = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: 'https://placehold.co/100x100?text=No+Image';
            $categories = wc_get_product_category_list( $product->get_id(), ', ' );
            $short_desc = wp_trim_words( $product->get_short_description(), 10 );
            ?>
            <li class="ss-search-result-item">
                <a href="<?php the_permalink(); ?>">
                    <div class="ss-result-thumb">
                        <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php the_title_attribute(); ?>">
                    </div>
                    <div class="ss-result-info">
                        <div class="ss-result-cat"><?php echo strip_tags( $categories ); ?></div>
                        <div class="ss-result-title"><?php the_title(); ?></div>
                        <div class="ss-result-desc"><?php echo esc_html( $short_desc ); ?></div>
                        <div class="ss-result-price"><?php echo $product->get_price_html(); // phpcs:ignore ?></div>
                    </div>
                </a>
            </li>
            <?php
        }
        echo '</ul>';
//         echo '<div class="ss-search-view-all"><a href="' . esc_url( home_url( '/?s=' . rawurlencode( $query ) . '&post_type=product' ) ) . '">' . esc_html__( 'View all results', 'snapserve' ) . ' <i class="bi bi-arrow-right"></i></a></div>';
         $total = $products->found_posts;

echo '<p class="ss-search-vuew-all" style="text-align: center; padding: 1px; color: grey;">';
echo esc_html( sprintf(
    _n( '%d product found', '%d products found', $total, 'snapserve' ),
    $total
) );
echo '</p>';
    } else {
        echo '<div class="ss-no-results">' . esc_html( sprintf( __( 'No products found for "%s"', 'snapserve' ), $query ) ) . '</div>';
    }
    $html = ob_get_clean();
    wp_reset_postdata();
    wp_send_json_success( array( 'results' => $html ) );
}


/* ============================================
   18. AJAX — HERO SUGGESTIONS (returns JSON)
   ============================================ */
add_action( 'wp_ajax_ss_hero_suggest',        'ss_hero_suggest_callback' );
add_action( 'wp_ajax_nopriv_ss_hero_suggest', 'ss_hero_suggest_callback' );
function ss_hero_suggest_callback() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ss-ajax-nonce' ) ) {
        wp_send_json_error( array() );
    }

    $query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
    if ( strlen( $query ) < 2 ) {
        wp_send_json_success( array() );
    }

    $products = ss_run_extended_search( $query, 6 );
    $results  = array();

    if ( $products->have_posts() ) {
        while ( $products->have_posts() ) {
            $products->the_post();
            global $product;
            $thumb     = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: 'https://placehold.co/80x80?text=Part';
            $results[] = array(
                'id'       => get_the_ID(),
                'title'    => get_the_title(),
                'url'      => get_permalink(),
                'thumb'    => esc_url( $thumb ),
                'price'    => wp_strip_all_tags( $product->get_price_html() ),
                'sku'      => $product->get_sku(),
                'category' => wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ' · ' ) ),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success( $results );
}


/* ============================================
   19. SHARED EXTENDED SEARCH HELPER
   Runs a WP_Query with extended JOIN/WHERE so
   it searches title, content, SKU, attributes,
   and custom meta in one query.
   ============================================ */
function ss_run_extended_search( $query, $limit = 8 ) {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $limit ),
        'ss_ajax_search' => true,
        's'              => $query,
    );

    add_filter( 'posts_join',     'ss_search_join_filter',     10, 2 );
    add_filter( 'posts_where',    'ss_search_where_filter',    10, 2 );
    add_filter( 'posts_distinct', 'ss_search_distinct_filter', 10, 2 );

    $result = new WP_Query( $args );

    remove_filter( 'posts_join',     'ss_search_join_filter' );
    remove_filter( 'posts_where',    'ss_search_where_filter' );
    remove_filter( 'posts_distinct', 'ss_search_distinct_filter' );

    return $result;
}

function ss_search_join_filter( $join, $query ) {
    global $wpdb;
    if ( $query->get( 'ss_ajax_search' ) ) {
        $join .= " LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id ";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id ";
        $join .= " LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id ";
        $join .= " LEFT JOIN {$wpdb->postmeta} pm_attr ON {$wpdb->posts}.ID = pm_attr.post_id ";
    }
    return $join;
}


function ss_search_where_filter( $where, $query ) {
    global $wpdb;

    if ( $query->get( 'ss_ajax_search' ) ) {

        $s = $query->get( 's' );
        if ( empty( $s ) ) return $where;

        $s_esc = esc_sql( $wpdb->esc_like( $s ) );

        // IMPORTANT: append instead of override
        $where .= " AND (
            ({$wpdb->posts}.post_title LIKE '%{$s_esc}%')
            OR ({$wpdb->posts}.post_excerpt LIKE '%{$s_esc}%')
            OR ({$wpdb->posts}.post_content LIKE '%{$s_esc}%')
            OR (tt.taxonomy LIKE 'pa_%' AND t.name LIKE '%{$s_esc}%')
            OR (pm_attr.meta_key = '_product_attributes' AND pm_attr.meta_value LIKE '%{$s_esc}%')
            OR (pm_attr.meta_key = '_sku' AND pm_attr.meta_value LIKE '%{$s_esc}%')
            OR (pm_attr.meta_key = 'model_number' AND pm_attr.meta_value LIKE '%{$s_esc}%')
            OR (pm_attr.meta_key = 'model-number' AND pm_attr.meta_value LIKE '%{$s_esc}%')
            OR (pm_attr.meta_key = 'Compatibility' AND pm_attr.meta_value LIKE '%{$s_esc}%')
        ) ";
    }

    return $where;
}


function ss_search_distinct_filter( $distinct, $query ) {
    if ( $query->get( 'ss_ajax_search' ) ) return 'DISTINCT';
    return $distinct;
}