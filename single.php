<?php use Roots\Sage\Utils; ?>

<?php Utils\sage_page_header(); ?>

<div class="page-content container">
  <?php get_template_part('templates/content-single', get_post_type()); ?>
</div>
