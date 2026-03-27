<?php get_header(); ?>

<main class="single-page">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <!-- HERO / TITLE -->
    <section class="ss-hero">
        <div class="ss-hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">

                <!-- LEFT CONTENT -->
                <div class="col-lg-6 ss-hero-left">
                    <h1 class="ss-hero-title">
                        <?php the_title(); ?>
                    </h1>
                    <?php if ($subtitle = get_post_meta(get_the_ID(), '_page_subtitle', true)) : ?>
                        <p class="ss-hero-sub"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <!-- RIGHT IMAGE -->
                <div class="col-lg-6 text-center">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="ss-hero-img-wrap">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="page-content container" style="margin-top:60px;">
        <div class="content-wrapper" style="background: rgba(255,255,255,0.95); padding:40px; border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,0.05);">
            <?php the_content(); ?>
        </div>
    </section>

    <?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>