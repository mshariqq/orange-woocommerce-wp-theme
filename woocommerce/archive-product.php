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
                <span class="count"><?php woocommerce_result_count(); ?></span>
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
            <aside class="shop-filters">

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
                        ?>

                        <div class="product-card">

    <!-- IMAGE -->
    <div class="product-image">
        <a href="<?php the_permalink(); ?>">
            <?php echo $product->get_image(); ?>
        </a>

        <?php if ($product->is_on_sale()) : ?>
            <span class="badge">Sale</span>
        <?php endif; ?>
    </div>

    <!-- BODY -->
    <div class="product-info">

        <!-- RATING -->
        <div class="rating">
            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
            <span>(<?php echo $product->get_review_count(); ?>)</span>
        </div>

        <!-- TITLE -->
        <h3 class="title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- META (CATEGORY) -->
        <p class="product-meta">
            <?php
            echo wc_get_product_category_list($product->get_id(), ', ');
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
               
               🛒
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