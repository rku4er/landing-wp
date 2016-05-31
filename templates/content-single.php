<?php use Roots\Sage\Titles; ?>

<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class('section-content'); ?>>
    <?php the_content(); ?>
    <?php wp_link_pages(array(
      'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'),
      'after' => '</p></nav>')); ?>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
