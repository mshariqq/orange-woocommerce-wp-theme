<?php
/*
Template Name: Premium Full Width
Description: Modern, full-width page template with breadcrumbs and premium layout.
*/
get_header();
?>

<main class="page-fullwidth">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <!-- Hero Section with Breadcrumbs -->
    <section class="page-hero">
        <div class="container">

            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <a href="<?php echo home_url(); ?>">Home</a> &raquo;
                <span><?php the_title(); ?></span>
            </nav>

            <!-- Page Title -->
            <h1 class="page-title"><?php the_title(); ?></h1>

        </div>
    </section>

    <!-- Main Content -->
    <section class="page-content">
        <div class="container">
            <div class="content-wrapper">

                <!-- Single Premium Card for all Gutenberg content -->
                <div class="content-card">
                    <?php the_content(); ?>
                </div>

            </div>
        </div>
    </section>

    <?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>