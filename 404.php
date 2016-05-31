<?php use Roots\Sage\Utils; ?>

<div class="container page-content">

  <?php echo Utils\sage_get_heading('h1', __('Page not found', 'sage')); ?>

  <div class="section-content">
    <div class="alert alert-warning">
      <?php _e('Sorry, but the page you were trying to view does not exist.', 'sage'); ?>
    </div>

    <div class="text-xs-center">
      <h2><?php _e('Please, try search...', 'sage'); ?></h2>
      <?php get_search_form(); ?>
    </div>
  </div>

</div>
