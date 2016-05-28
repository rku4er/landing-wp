<?php use Roots\Sage\Utils; ?>

<div class="page-content container">
  <?php echo Utils\sage_get_heading('h1'); ?>
  <?php get_template_part('templates/content-single', get_post_type()); ?>
</div>
