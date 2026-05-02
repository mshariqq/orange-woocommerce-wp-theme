<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div class="ss-sticky-header">
<header class="ss-header">

    <!-- TOP STRIP -->
    <div class="ss-topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="ss-top-left">
                <span><i class="bi bi-truck"></i> Fast Shipping</span>
                <span class="ms-3"><i class="bi bi-shield-check"></i> Secure Checkout</span>
            </div>
            <div class="ss-top-right">
                <?php wp_nav_menu(array(
                    'theme_location' => 'topbar',
                    'menu_class'     => 'ss-top-menu',
                    'container'      => false,
                    'fallback_cb'    => false
                )); ?>
            </div>
        </div>
    </div>

    <!-- MAIN HEADER -->
    <div class="ss-main-header">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-6 col-lg-2">
                    <a href="<?php echo home_url(); ?>" class="ss-logo">
                        <?php if (has_custom_logo()) { the_custom_logo(); } else { bloginfo('name'); } ?>
                    </a>
                </div>

                <div class="col-12 col-lg-7 order-3 order-lg-2 mt-3 mt-lg-0">
                    <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="ss-ajax-search">
                        <div class="ss-search-box">
                            <i class="bi bi-search"></i>
                            <input type="search" name="s" placeholder="Enter part #, model #, or keyword..." 
								   required autocomplete="off">
                            <button type="submit">Search</button>
                        </div>
                        <div class="ss-search-results"></div>
                    </form>
                </div>

                <div class="col-6 col-lg-3 text-end order-2 order-lg-3">
                    <div class="ss-icons">
                        <a href="/my-account">
                            <i class="bi bi-person"></i>
                            <span>Account</span>
                        </a>
                        <a href="<?php echo wc_get_cart_url(); ?>" class="ss-cart">
                            <i class="bi bi-cart3"></i>
                            <span>Cart</span>
                            <b><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></b>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MOBILE HEADER -->
    <div class="ss-mobile-header d-lg-none">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?php echo home_url(); ?>" class="ss-logo">
                <?php if (has_custom_logo()) { the_custom_logo(); } else { bloginfo('name'); } ?>
            </a>
            <div class="ss-mobile-icons">
                <button class="ss-icon-btn" id="searchToggle"><i class="bi bi-search"></i></button>
                <a href="/my-account" class="ss-icon-btn"><i class="bi bi-person"></i></a>
                <a href="<?php echo wc_get_cart_url(); ?>" class="ss-icon-btn ss-cart">
                    <i class="bi bi-cart3"></i>
                    <b><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></b>
                </a>
                <button class="ss-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="ss-navbar navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="menu">
                <?php wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'navbar-nav ss-menu',
                    'container'      => false
                )); ?>
            </div>
        </div>
    </nav>

</header>
</div><!-- /.ss-sticky-header -->

<!-- SEARCH OVERLAY (fixed, intentionally outside sticky wrapper) -->
<div class="ss-search-overlay" id="searchOverlay">
    <div class="container">
        <div class="ss-search-overlay-header">
            <h5>Search Products</h5>
            <button type="button" class="ss-search-close" id="searchClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="get" action="<?php echo home_url('/'); ?>" class="ss-ajax-search">
            <div class="ss-search-box">
                <i class="bi bi-search"></i>
                <input type="search" name="s" placeholder="Search by model, brand, or part #..." autofocus autocomplete="off">
                <button type="submit">Search</button>
            </div>
            <div class="ss-search-results"></div>
        </form>
    </div>
</div>

<!-- MOBILE MENU -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h5>Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class'     => 'navbar-nav ss-menu',
            'container'      => false
        )); ?>
    </div>
</div>

<main>