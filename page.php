<?php use Roots\Sage\Utils; ?>

<?php while (have_posts()) : the_post(); ?>

  <div class="page-content container">
    <?php echo Utils\sage_flexible_content(); ?>
  </div>

<?php endwhile; ?>
