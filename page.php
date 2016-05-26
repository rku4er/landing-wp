<?php use Roots\Sage\Utils; ?>

<?php while (have_posts()) : the_post(); ?>

  <?php echo Utils\sage_flexible_content(); ?>

<?php endwhile; ?>
