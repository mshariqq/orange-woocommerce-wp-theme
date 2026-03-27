<?php
defined( 'ABSPATH' ) || exit;
global $product;
?>

<div <?php wc_product_class( 'col', $product ); ?>>
    <div class="card product-card h-100 position-relative border-0 shadow-sm">
        <!-- Sale Badge -->
        <?php if ( $product->is_on_sale() ) : ?>
            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Sale</span>
        <?php endif; ?>

        <!-- Product Image -->
        <a href="<?php the_permalink(); ?>">
            <?php echo woocommerce_get_product_thumbnail( 'medium' ); ?>
        </a>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title product-title"><?php the_title(); ?></h5>
            <p class="card-text product-price"><?php echo $product->get_price_html(); ?></p>
            <?php woocommerce_template_loop_add_to_cart( array(
                'class' => 'btn btn-orange mt-auto w-100',
                'text'  => 'Add to Cart',
            ) ); ?>
        </div>
    </div>
</div>