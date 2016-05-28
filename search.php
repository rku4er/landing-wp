<?php use Roots\Sage\Utils; ?>

<div class="page-content container">
  <?php echo Utils\sage_get_heading('h1'); ?>

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
