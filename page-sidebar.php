<?php
/*
Template Name: Premium Page with Sidebar
Description: Modern page template with right sidebar, breadcrumbs, and premium layout.
*/
get_header();
?>

<main class="page-with-sidebar">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <!-- Hero Section -->
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

    <!-- Content Section + Sidebar -->
    <section class="page-content container">
        <div class="row">

            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="content-wrapper">

                    <!-- Premium Card around entire content -->
                    <div class="content-card">
                        <?php the_content(); ?>
                    </div>

                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="sidebar-wrapper">
                    <?php if (is_active_sidebar('page-sidebar')) :
                        dynamic_sidebar('page-sidebar');
                    else : ?>
                        <div class="sidebar-card">
                            <h3>About</h3>
                            <p>Add widgets here via Appearance → Widgets</p>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>

        </div>
    </section>

<?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>