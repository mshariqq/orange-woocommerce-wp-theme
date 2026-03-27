<?php
/*
Template Name: Full Canvas
Description: Modern, full-width page template with breadcrumbs and premium layout.
*/
get_header();
?>

<main class="ss-page">

<?php
if (have_posts()) :
    while (have_posts()) : the_post();
        the_content();
    endwhile;
endif;
?>

</main>


<?php get_footer(); ?>