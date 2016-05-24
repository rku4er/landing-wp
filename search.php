<?php use Roots\Sage\Utils; ?>

<?php Utils\sage_page_header(); ?>

<div class="page-content container">

  <?php if (have_posts()) : ?>

    <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('templates/content'); ?>
    <?php endwhile; ?>

    <?php the_posts_navigation(); ?>

  <?php else : ?>

    <div class="alert alert-warning">
      <?php _e('Sorry, no results were found.', 'sage'); ?>
    </div>

    <?php get_search_form(); ?>

  <?php endif; ?>

</div>
