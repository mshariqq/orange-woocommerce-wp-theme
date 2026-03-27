<?php
/* ============================================
   SNAPSERVE SUPPLY — functions.php
   Custom Theme
   ============================================ */


/* ============================================
   THEME SETUP
   ============================================ */

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'woocommerce' );

register_nav_menus( array(
    'primary' => 'Primary Menu',
    'topbar'  => 'Top Bar Menu',
) );

add_theme_support( 'custom-logo' );

function snapserve_custom_logo_setup() {
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
}
add_action( 'after_setup_theme', 'snapserve_custom_logo_setup' );
/* ============================================
   ENQUEUE SCRIPTS
   ============================================ */
function snapserve_scripts() {
    // Bootstrap JS (bundle includes Popper)
    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
        array(),
        '5.3.2',
        true
    );

    // Custom theme scripts
    wp_enqueue_script(
        'ss-scripts',
        get_template_directory_uri() . '/js/ss.js',
        array('bootstrap-js'),
        '1.1',
        true
    );

    // Localize for AJAX
    wp_localize_script('ss-scripts', 'ss_ajax', array(
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ss-ajax-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'snapserve_scripts', 10);

/* ============================================
   CUSTOMIZER — LOGO SETTINGS
   ============================================ */
function snapserve_logo_customizer( $wp_customize ) {

    $wp_customize->add_section( 'snapserve_logo_settings', array(
        'title'    => 'Logo Settings',
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'snapserve_logo_width', array(
        'default'           => '180',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'snapserve_logo_width', array(
        'label'   => 'Logo Width (px)',
        'section' => 'snapserve_logo_settings',
        'type'    => 'number',
    ) );

    $wp_customize->add_setting( 'snapserve_logo_height', array(
        'default'           => '50',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'snapserve_logo_height', array(
        'label'   => 'Logo Height (px)',
        'section' => 'snapserve_logo_settings',
        'type'    => 'number',
    ) );
}
add_action( 'customize_register', 'snapserve_logo_customizer' );


/* ============================================
   DYNAMIC CSS — LOGO SIZE
   ============================================ */
function snapserve_logo_dynamic_css() {
    $width  = get_theme_mod( 'snapserve_logo_width', 180 );
    $height = get_theme_mod( 'snapserve_logo_height', 50 );
    ?>
    <style>
        .custom-logo {
            width: <?php echo esc_attr( $width ); ?>px;
            height: <?php echo esc_attr( $height ); ?>px;
            object-fit: contain;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'snapserve_logo_dynamic_css' );


/* ============================================
   ENQUEUE STYLES & SCRIPTS
   ============================================ */
function snapserve_enqueue_all_styles() {
    $theme_uri = get_template_directory_uri();

    // Bootstrap
    wp_enqueue_style(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
        array(),
        '5.3.2'
    );

    // Theme CSS (order matters)
    wp_enqueue_style( 'base',       $theme_uri . '/css/base.css', array('bootstrap'), '1.0' );
    wp_enqueue_style( 'header',     $theme_uri . '/css/header.css', array('base'), '1.0' );
    wp_enqueue_style( 'navbar',     $theme_uri . '/css/navbar.css', array('base'), '1.0' );
    wp_enqueue_style( 'components', $theme_uri . '/css/components.css', array('base'), '1.0' );
    wp_enqueue_style( 'mobile',     $theme_uri . '/css/mobile.css', array('base'), '1.0' );
    wp_enqueue_style( 'footer',     $theme_uri . '/css/footer.css', array('base'), '1.0' );
    wp_enqueue_style( 'sswc-css',   $theme_uri . '/css/sswc.css', array('footer'), '1.0' );

    if ( is_singular('product') ) {
        wp_enqueue_style( 'single-page-css', $theme_uri . '/css/single.css','', '1.0' );
    }

    // Optional: enqueue style.css (main theme stylesheet) last if needed
    wp_enqueue_style( 'main-style', get_stylesheet_uri(), array('base'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'snapserve_enqueue_all_styles' );


function sss_add_woocommerce_support() {
    
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
	
}
add_action( 'after_setup_theme', 'sss_add_woocommerce_support' );
// Add this to your theme’s functions.php
// remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/* ============================================
   WOOCOMMERCE — FITS MODEL / COMPATIBILITY TAB

   Reads the "compatible_models" custom field
   set during WP All Import CSV import.
   Value format: comma-separated model numbers
   e.g. "GTDP490ED7WS, WTW4816FW2, MVW18PDAWW"
   ============================================ */

// Register the tab — only shows if field has data
add_filter( 'woocommerce_product_tabs', function( $tabs ) {

    global $product;

    $compatible_models = get_post_meta( $product->get_id(), 'compatible_models', true );

    if ( ! empty( $compatible_models ) ) {
        $tabs['fits_model'] = array(
            'title'    => __( 'Fits Model / Compatibility', 'snapserve' ),
            'priority' => 25,
            'callback' => 'snapserve_fits_model_tab_content',
        );
    }

    return $tabs;
} );


// Tab content
function snapserve_fits_model_tab_content() {

    global $product;

    $compatible_models = get_post_meta( $product->get_id(), 'compatible_models', true );

    if ( empty( $compatible_models ) ) return;

    $models = array_filter( array_map( 'trim', explode( ',', $compatible_models ) ) );
    ?>

    <div class="snapserve-compat-tab">

        <div class="compat-header">
            <h3 class="compat-title">
                <?php esc_html_e( 'Compatible Appliance Models', 'snapserve' ); ?>
            </h3>
            <p class="compat-desc">
                <?php esc_html_e( 'This part is confirmed to fit the following appliance models. Always verify your appliance model number — found on the data plate inside the door or on the back panel — before ordering.', 'snapserve' ); ?>
            </p>
        </div>

        <div class="compat-search-wrap">
            <input
                type="text"
                id="compat-filter"
                class="compat-filter-input"
                placeholder="<?php esc_attr_e( 'Filter by model number…', 'snapserve' ); ?>"
                onkeyup="snapserveFilterModels(this.value)"
            />
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

        <p class="compat-no-results" id="compat-no-results" style="display:none;">
            <?php esc_html_e( 'No matching models found.', 'snapserve' ); ?>
        </p>

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


// Compatibility tab styles — loads on single product pages only
add_action( 'wp_head', function() {

    if ( ! is_product() ) return;
    ?>
    <style>
    .snapserve-compat-tab { padding: 8px 0; }
    .compat-header { margin-bottom: 20px; }
    .compat-title { font-size: 18px; font-weight: 700; color: #0A1E3F; margin: 0 0 8px; }
    .compat-desc { font-size: 14px; color: #5a6a7e; line-height: 1.6; max-width: 640px; }
    .compat-search-wrap { margin-bottom: 16px; }
    .compat-filter-input {
        width: 100%;
        max-width: 360px;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        color: #1a2332;
        outline: none;
        transition: border-color 0.2s;
    }
    .compat-filter-input:focus { border-color: #0A1E3F; }
    .compat-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .compat-model-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4f8;
        border: 1.5px solid #e2e8f0;
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #0A1E3F;
        font-family: monospace;
        letter-spacing: 0.04em;
        transition: background 0.2s, border-color 0.2s;
    }
    .compat-model-chip svg { stroke: #70B354; flex-shrink: 0; }
    .compat-model-chip:hover { background: #e8f0f8; border-color: #0A1E3F; }
    .compat-model-chip.hidden { display: none; }
    .compat-no-results { font-size: 14px; color: #94a3b8; padding: 16px 0; }
    .compat-disclaimer {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 12px;
        color: #94a3b8;
        line-height: 1.5;
        padding-top: 16px;
        border-top: 1px solid #f0f4f8;
        margin-top: 8px;
    }
    .compat-disclaimer svg { flex-shrink: 0; margin-top: 1px; stroke: #94a3b8; }
    </style>
    <?php
} );


// Compatibility tab JS — loads on single product pages only
add_action( 'wp_footer', function() {

    if ( ! is_product() ) return;
    ?>
    <script>
    function snapserveFilterModels( query ) {
        var q     = query.toLowerCase().trim();
        var chips = document.querySelectorAll( '.compat-model-chip' );
        var shown = 0;

        chips.forEach( function( chip ) {
            var model = chip.getAttribute( 'data-model' );
            if ( ! q || model.indexOf( q ) !== -1 ) {
                chip.classList.remove( 'hidden' );
                shown++;
            } else {
                chip.classList.add( 'hidden' );
            }
        } );

        var noResults = document.getElementById( 'compat-no-results' );
        if ( noResults ) {
            noResults.style.display = ( shown === 0 && q !== '' ) ? 'block' : 'none';
        }
    }
    </script>
    <?php
} );


/* ============================================
   WOOCOMMERCE — "FITS X MODELS" BADGE
   Shows on product cards in shop/category loop
   ============================================ */
add_action( 'woocommerce_after_shop_loop_item_title', function() {

    global $product;

    $compatible_models = get_post_meta( $product->get_id(), 'compatible_models', true );

    if ( empty( $compatible_models ) ) return;

    $models = array_filter( array_map( 'trim', explode( ',', $compatible_models ) ) );
    $count  = count( $models );

    if ( $count > 0 ) {
        echo '<p class="compat-count-badge">';
        echo '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#70B354" stroke-width="2.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> ';
        echo esc_html( sprintf(
            _n( 'Fits %d model', 'Fits %d models', $count, 'snapserve' ),
            $count
        ) );
        echo '</p>';
    }

}, 5 );

// Badge style — loads on shop/category pages only
add_action( 'wp_head', function() {

    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) return;
    ?>
    <style>
    .compat-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #5a6a7e;
        margin: 4px 0 0;
        font-weight: 500;
    }
    </style>
    <?php
} );


/* ============================================
   WOOCOMMERCE — 15% RESTOCKING FEE
   Logs an order note when a refund is issued.
   Waives fee automatically if refund reason
   contains: defective / wrong item / our error
   / damaged — per the Return & Refund Policy.
   ============================================ */
add_action( 'woocommerce_order_refunded', function( $order_id, $refund_id ) {

    $refund = wc_get_order( $refund_id );
    $order  = wc_get_order( $order_id );

    if ( ! $refund || ! $order ) return;

    $reason = strtolower( $refund->get_reason() );

    $skip_keywords = array( 'defective', 'wrong item', 'our error', 'damaged', 'incorrect item' );

    foreach ( $skip_keywords as $keyword ) {
        if ( strpos( $reason, $keyword ) !== false ) {
            $order->add_order_note(
                __( 'Restocking fee WAIVED — refund reason indicates seller error or defective item. Full refund issued per Return & Refund Policy.', 'snapserve' )
            );
            return;
        }
    }

    $refund_total = abs( $refund->get_total() );
    $fee_amount   = round( $refund_total * 0.15 / 0.85, 2 );

    $order->add_order_note(
        sprintf(
            __( '15%% restocking fee applied per Return & Refund Policy. Refund issued: $%s. Estimated fee deducted: $%s.', 'snapserve' ),
            number_format( $refund_total, 2 ),
            number_format( $fee_amount, 2 )
        )
    );

}, 10, 2 );


// widgets for footer
function ss_footer_widgets_init() {
    // Footer Top Widgets
    register_sidebar(array(
        'name'          => 'Footer Column 1',
        'id'            => 'footer_col_1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ));
    register_sidebar(array(
        'name'          => 'Footer Column 2',
        'id'            => 'footer_col_2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ));
    register_sidebar(array(
        'name'          => 'Footer Column 3',
        'id'            => 'footer_col_3',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ));
    register_sidebar(array(
        'name'          => 'Footer Column 4',
        'id'            => 'footer_col_4',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ));

    // Footer Bottom Widget
    register_sidebar(array(
        'name'          => 'Footer Bottom',
        'id'            => 'footer_bottom',
        'before_widget' => '<div class="footer-bottom-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6>',
        'after_title'   => '</h6>',
    ));
}
add_action('widgets_init', 'ss_footer_widgets_init');





// Add lazy loading to all images
function ss_lazy_load_images($content) {
    $content = preg_replace('/<img(.*?)>/', '<img$1 loading="lazy">', $content);
    return $content;
}
add_filter('the_content', 'ss_lazy_load_images');
add_filter('post_thumbnail_html', 'ss_lazy_load_images');
add_filter('widget_text', 'ss_lazy_load_images');





// SHORCODES
// // home -> products
// Shortcode to display featured products exactly like reference
function ss_featured_products_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'    => 'Featured Products',
        'subtitle' => 'Top selling and high demand parts',
        'limit'    => 8,
        'category' => '',
    ), $atts, 'ss_products');

    $args = array(
        'post_type'           => 'product',
        'posts_per_page'      => intval($atts['limit']),
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => 1,
        'suppress_filters'    => true,
    );

    $products = new WP_Query($args);
    ob_start();
    ?>
    <section class="ss-products">
        <div class="container">

            <div class="ss-section-head d-flex justify-content-between align-items-end">
                <div>
                    <h2><?php echo esc_html($atts['title']); ?></h2>
                    <p><?php echo esc_html($atts['subtitle']); ?></p>
                </div>
                <a href="<?php echo get_permalink( wc_get_page_id('shop') ); ?>" class="ss-link">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="ss-product-grid">
                <?php if ($products->have_posts()) : ?>
                    <?php while ($products->have_posts()) : $products->the_post(); global $product; ?>
                        <div class="ss-product-card">

                            <div class="ss-product-img">
    <a href="<?php the_permalink(); ?>">
        <?php
        $image_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
        if ( $image_url ) : ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
        <?php else : ?>
            <img src="https://placehold.co/300x150?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title_attribute(); ?>">
        <?php endif; ?>
        <?php if ($product->is_on_sale()) { ?>
            <span class="ss-badge">Hot</span>
        <?php } ?>
    </a>
</div>

                            <div class="ss-product-body">

                                <div class="ss-rating">
                                    <?php
                                        $average = round($product->get_average_rating());
                                        echo str_repeat('★', $average) . str_repeat('☆', 5 - $average);
                                    ?>
                                    <span>(<?php echo $product->get_rating_count(); ?>)</span>
                                </div>

                                <h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>

                                <!-- FIX 1: strip_tags removes the <a> links WooCommerce adds to category names -->
                                <p class="ss-product-meta"><?php echo strip_tags( wc_get_product_category_list($product->get_id(), ' • ') ); ?></p>

                                <div class="ss-product-bottom">
                                    <div>
                                        <!-- FIX 2: plain price output, avoids nested bdi/span breaking your .ss-price styles -->
                                        <span class="ss-price"><?php echo wc_price( $product->get_price() ); ?></span>
                                        <?php if ($product->is_on_sale()) : ?>
                                            <span class="ss-old-price"><?php echo wc_price($product->get_regular_price()); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="ss-add-cart" onclick="location.href='<?php echo esc_url($product->add_to_cart_url()); ?>'">
                                        <i class="bi bi-cart"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>

        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('ss_products', 'ss_featured_products_shortcode');


/* ============================================
   AJAX PRODUCT SEARCH
   ============================================ */
function ss_ajax_search_callback() {
    check_ajax_referer('ss-ajax-nonce', 'nonce');

    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if (empty($query)) {
        wp_send_json_success(array('results' => ''));
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        's'              => $query,
    );

    // Add meta query for compatibility and SKU
    $args['meta_query'] = array(
        'relation' => 'OR',
        array(
            'key'     => 'compatible_models',
            'value'   => $query,
            'compare' => 'LIKE',
        ),
        array(
            'key'     => '_sku',
            'value'   => $query,
            'compare' => 'LIKE',
        )
    );

    // Filter to make WP_Query search in title/content OR meta fields
    add_filter('posts_where', 'ss_search_where_filter', 10, 2);
    $products = new WP_Query($args);
    remove_filter('posts_where', 'ss_search_where_filter');

    ob_start();

    if ($products->have_posts()) {
        echo '<ul class="ss-search-results-list">';
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
            if (!$thumbnail) {
                $thumbnail = 'https://placehold.co/100x100?text=No+Image';
            }
            $categories = wc_get_product_category_list($product->get_id(), ', ');
            $price = $product->get_price_html();
            $short_desc = wp_trim_words($product->get_short_description(), 10);
            ?>
            <li class="ss-search-result-item">
                <a href="<?php the_permalink(); ?>">
                    <div class="ss-result-thumb">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title_attribute(); ?>">
                    </div>
                    <div class="ss-result-info">
                        <div class="ss-result-cat"><?php echo strip_tags($categories); ?></div>
                        <div class="ss-result-title"><?php the_title(); ?></div>
                        <div class="ss-result-desc"><?php echo esc_html($short_desc); ?></div>
                        <div class="ss-result-price"><?php echo $price; ?></div>
                    </div>
                </a>
            </li>
            <?php
        }
        echo '</ul>';
        echo '<div class="ss-search-view-all"><a href="' . esc_url(home_url('/?s=' . $query . '&post_type=product')) . '">View all results <i class="bi bi-arrow-right"></i></a></div>';
    } else {
        echo '<div class="ss-no-results">No products found for "' . esc_html($query) . '"</div>';
    }

    $results_html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success(array('results' => $results_html));
}
add_action('wp_ajax_ss_ajax_search', 'ss_ajax_search_callback');
add_action('wp_ajax_nopriv_ss_ajax_search', 'ss_ajax_search_callback');

/**
 * Filter to make WP_Query search in title/content OR meta fields
 */
function ss_search_where_filter($where, $query) {
    global $wpdb;

    if ($query->is_search() && !is_admin()) {
        // Find the 'AND' between search and meta_query and replace with 'OR'
        // This is a bit brittle but common for "Search OR Meta"
        // WP_Query normally produces: AND ( (post_title LIKE ...) ) AND ( (post_meta LIKE ...) )
        // We want: AND ( (post_title LIKE ...) OR (post_meta LIKE ...) )
        
        $where = preg_replace(
            '/\)\s+AND\s+\(/',
            ') OR (',
            $where,
            1 // only replace the first 'AND' which connects search and meta
        );
    }
    return $where;
}