<?php
defined('ABSPATH') || exit;
get_header();
?>

<div class="shop-modern">

    <div class="container">

        <!-- HEADER -->
        <div class="shop-topbar">
            <div class="left">
                <h1><?php woocommerce_page_title(); ?></h1>
                <div class="shop-meta">
                    <span class="count"><?php woocommerce_result_count(); ?></span>
                    <button class="ss-filter-toggle d-lg-none" data-bs-toggle="collapse" data-bs-target="#shopFilters">
                        <i class="bi bi-funnel"></i> Filters
                    </button>
                </div>
            </div>

            
			<div class="right">
    <div class="ss-sort">

        <label>Sort by:</label>

        <div class="ss-sort-dropdown">

            <div class="ss-sort-selected">
                Default sorting
                <span class="arrow">⌄</span>
            </div>

            <ul class="ss-sort-options">
                <li data-value="menu_order">Default sorting</li>
                <li data-value="popularity">Popularity</li>
                <li data-value="rating">Top rated</li>
                <li data-value="date">Latest</li>
                <li data-value="price">Price: Low → High</li>
                <li data-value="price-desc">Price: High → Low</li>
            </ul>

            <!-- KEEP ORIGINAL (hidden for WooCommerce functionality) -->
            <form class="woocommerce-ordering" method="get">
                <select name="orderby" class="orderby">
                    <option value="menu_order" selected>Default sorting</option>
                    <option value="popularity">Popularity</option>
                    <option value="rating">Top rated</option>
                    <option value="date">Latest</option>
                    <option value="price">Price: Low to High</option>
                    <option value="price-desc">Price: High to Low</option>
                </select>
                <input type="hidden" name="paged" value="1">
            </form>

        </div>
    </div>
</div>
        </div>

        <div class="shop-layout">

            <!-- SIDEBAR (CUSTOM UI) -->
            <aside class="shop-filters collapse d-lg-block" id="shopFilters">

<!--                 <div class="filter-box">
                    <h4>Search</h4>
                    <?php get_product_search_form(); ?>
                </div> -->

                <div class="filter-box mb-md-4">
                    <h4>Categories</h4>
                    <ul>
                        <?php
                        wp_list_categories([
                            'taxonomy' => 'product_cat',
                            'title_li' => '',
                        ]);
                        ?>
                    </ul>
                </div>

                <?php
                $brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true ) );
                if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
                <div class="filter-box mb-md-4">
                    <h4>Brands</h4>
                    <ul>
                        <?php
                        wp_list_categories([
                            'taxonomy' => 'product_brand',
                            'title_li' => '',
                        ]);
                        ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="filter-box mb-md-4">
                    <h4>Filter by Price</h4>
                    <?php the_widget('WC_Widget_Price_Filter'); ?>
                </div>

                <div class="filter-box">
                    <h4>Top Rated</h4>
                    <?php the_widget('WC_Widget_Top_Rated_Products'); ?>
                </div>

            </aside>

            <!-- PRODUCTS -->
            <div class="shop-products">

                <?php if (have_posts()) : ?>

                    <div class="products-grid">

                        <?php while (have_posts()) : the_post();
                            global $product;
                        ?>

                        <div class="product-card">

    <!-- IMAGE -->
    <div class="product-image">
        <a href="<?php the_permalink(); ?>">
            <?php 
            if ( has_post_thumbnail() ) {
                echo $product->get_image(); 
            } else {
                // echo wc_placeholder_img( 'woocommerce_thumbnail' );
                echo '<img src="https://placehold.co/200x150?text=No+Image" alt="'.get_the_title() .'" />';
            }
            ?>
        </a>

        <?php if ($product->is_on_sale()) : ?>
            <span class="badge">Sale</span>
        <?php endif; ?>
    </div>

    <!-- BODY -->
    <div class="product-info">

        <!-- RATING -->
        <?php echo ss_get_rating_html( $product ); ?>

        <!-- TITLE -->
        <h3 class="title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- META (BRAND) -->
        <p class="product-brand">
            <?php
            $brands = get_the_terms($product->get_id(), 'product_brand');
            if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
                $brand_names = wp_list_pluck( $brands, 'name' );
                echo esc_html( implode( ', ', $brand_names ) );
            } else {
                echo esc_html__( 'Generic Brand', 'snapserve' );
            }
            ?>
        </p>

        <!-- BOTTOM -->
        <div class="product-bottom">

            <!-- PRICE -->
            <div class="price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- CART BUTTON -->
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
               data-quantity="1"
               class="add-cart ajax_add_to_cart"
               data-product_id="<?php echo $product->get_id(); ?>"
               data-product_sku="<?php echo $product->get_sku(); ?>"
               aria-label="Add to cart">
               
               <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				  <path d="M3 3H5L6.6 12.59C6.7 13.2 7.23 13.65 7.85 13.65H17.4C17.95 13.65 18.43 13.27 18.56 12.74L20.2 6.74C20.37 6.12 19.9 5.5 19.25 5.5H6.21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				  <circle cx="9" cy="20" r="1.5" fill="white"/>
				  <circle cx="17" cy="20" r="1.5" fill="white"/>
				</svg>
            </a>

        </div>

    </div>

</div>

                        <?php endwhile; ?>

                    </div>

                    <div class="pagination">
                        <?php woocommerce_pagination(); ?>
                    </div>

                <?php else : ?>
                    <p>No products found</p>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php get_footer(); ?>