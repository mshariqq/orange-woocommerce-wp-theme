<?php
defined('ABSPATH') || exit;
get_header();
?>

<div class="shop-modern">

    <div class="container">

        <!-- HEADER -->
        <div class="shop-topbar">
            <div class="left">
                <h1>
                    Search results for: "<?php echo get_search_query(); ?>"
                </h1>

                <div class="shop-meta">
                    <span class="count">
                        <?php echo $wp_query->found_posts . ' products'; ?>
                    </span>
                    <button class="ss-filter-toggle d-lg-none" data-bs-toggle="collapse" data-bs-target="#shopFilters">
                        <i class="bi bi-funnel"></i> Filters
                    </button>
                </div>
            </div>

            <div class="right">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>

        <div class="shop-layout">

            <!-- SIDEBAR -->
            <aside class="shop-filters collapse d-lg-block" id="shopFilters">

                <div class="filter-box">
                    <h4>Search</h4>
                    <?php get_product_search_form(); ?>
                </div>

                <div class="filter-box">
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
                $brands = get_terms( array( 'taxonomy' => 'pa_brand', 'hide_empty' => true ) );
                if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
                <div class="filter-box">
                    <h4>Brands</h4>
                    <ul>
                        <?php
                        wp_list_categories([
                            'taxonomy' => 'pa_brand',
                            'title_li' => '',
                        ]);
                        ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="filter-box">
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

                            if (!$product) continue;
                        ?>

                        <div class="product-card">

                            <div class="product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    if ( has_post_thumbnail() ) {
                                        echo $product->get_image(); 
                                    } else {
                                        echo '<img src="https://placehold.co/200x150?text=No+Image" alt="'.get_the_title() .'" />';
                                    }
                                    ?>
                                </a>

                                <?php if ($product->is_on_sale()) : ?>
                                    <span class="badge">Sale</span>
                                <?php endif; ?>
                            </div>

                            <div class="product-info">

                                <?php echo ss_get_rating_html( $product ); ?>

                                <h3 class="title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="product-meta">
                                    <?php
                                    echo wc_get_product_category_list($product->get_id(), ', ');
                                    ?>
                                </p>

                                <div class="product-bottom">

                                    <div class="price">
                                        <?php echo $product->get_price_html(); ?>
                                    </div>

                                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                                       data-quantity="1"
                                       class="add-cart ajax_add_to_cart"
                                       data-product_id="<?php echo $product->get_id(); ?>"
                                       data-product_sku="<?php echo $product->get_sku(); ?>"
                                       aria-label="Add to cart">
                                       🛒
                                    </a>

                                </div>

                            </div>

                        </div>

                        <?php endwhile; ?>

                    </div>

                    <div class="pagination">
                        <?php the_posts_pagination(); ?>
                    </div>

                <?php else : ?>

                    <p>No products found for "<?php echo get_search_query(); ?>"</p>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php get_footer(); ?>