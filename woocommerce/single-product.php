<?php
defined( 'ABSPATH' ) || exit;
get_header();
global $product;
?>


<style>
	.ss-stock-badge .ss-stock-in{
		margin-left: 40px !important;
	}
</style>

<div class="ssp-page">
    <div class="container">

        <!-- BREADCRUMB -->
        <div class="ssp-breadcrumb">
            <?php woocommerce_breadcrumb(); ?>
        </div>

        <!-- HERO -->
        <div class="ssp-hero">

            <!-- LEFT: GALLERY -->
            <div class="ssp-gallery-col">
                <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="ssp-summary-col">
                <div class="ssp-summary">

                    <!-- BADGES -->
                    <div class="ssp-badges">
                        <?php if ( $product->is_on_sale() ) : ?>
                            <span class="ssp-badge ssp-badge--sale">On Sale</span>
                        <?php endif; ?>
                        <?php if ( $product->is_in_stock() ) : ?>
                            <span class="ssp-badge ssp-badge--stock">In Stock</span>
                        <?php else : ?>
                            <span class="ssp-badge ssp-badge--out">Out of Stock</span>
                        <?php endif; ?>
                        <?php if ( has_term( 'oem', 'product_tag', $product->get_id() ) ) : ?>
                            <span class="ssp-badge ssp-badge--oem">OEM</span>
                        <?php endif; ?>
                    </div>

                    <?php do_action( 'woocommerce_single_product_summary' ); ?>

                </div>
            </div>

        </div>

        <!-- TABS -->
        <div class="ssp-tabs-wrap">
            <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
        </div>

    </div>
</div>

<?php get_footer(); ?>