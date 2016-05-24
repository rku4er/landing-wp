<?php use Roots\Sage\Utils; ?>

<?php while (have_posts()) : the_post(); ?>

  <?php Utils\sage_page_header(); ?>

  <div class="page-content container">
    <?php get_template_part('templates/content', 'page'); ?>
  </div>

<?php endwhile; ?>
