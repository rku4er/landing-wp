<?php

namespace Roots\Sage\Utils;

use Roots\Sage\Titles;


/**
 * Custom Excerpt
 * @param integer $limit Limit of words
 * @uses get_the_content()
 * @uses get_permalink()
 * @uses apply_filters()
 * @return void
 */

function sage_excerpt($limit = 35) {
  $content = explode(' ', get_the_content(), $limit);

  if (count($content) >= $limit) {
    array_pop($content);
    $content = sprintf('%s&hellip; <a class="more" href="%s">%s</a>',
      implode(" ",$content),
      get_permalink(),
      __('Read More', 'sage')
    );
  } else {
    $content = implode(" ",$content);
  }

  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);

  echo $content;
}


/**
 * Get the excerpt
 * @param mixed $post_id the post id (int|str, defaults to $post->id)
 * @return mixed
 * @uses get_the_excerpt()
 */

function sage_get_the_excerpt($post_id) {
    global $post;

    $save_post = $post;

    if ( isset( $post->ID ) && !$post_id )
        $post = get_post($post->ID);
    else
        $post = get_post($post_id);

    $output = get_the_excerpt();
    $post = $save_post;

    return $output;
}


/**
 * Get Theme Options Fields
 * @uses get_fields()
 * @return array
 */

function sage_get_options() {

    if( function_exists('get_fields') ) {

        $options = get_fields('option');

        return $options;
    }
}

/**
 * Return a custom field stored by the Advanced Custom Fields plugin
 *
 * @global $post
 * @param str $key The key to look for
 * @param mixed $id The post ID (int|str, defaults to $post->ID)
 * @uses get_field()
 * @return mixed
 */

function sage_get_field( $key, $id=false, $format=true ) {

  global $post;

  $key = trim( filter_var( $key, FILTER_SANITIZE_STRING ) );
  $result = '';

  if ( function_exists( 'get_field' ) ) {
    if ( isset( $post->ID ) && !$id )
      $result = get_field( $key, $post->ID, $format);
    else
      $result = get_field( $key, $id, $format );

  }

  return $result;

}


/**
 * Shortcut for 'echo _get_field()'
 * @param str $key The meta key
 * @param mixed $id The post ID (int|str, defaults to $post->ID)
 * @uses sage_get_field()
 * @return void
 */

function sage_the_field( $key, $id=false ) {

  echo sage_get_field( $key, $id );

}


/**
 * Get a sub field of a Repeater field
 * @param str $key The meta key
 * @return mixed
 * @uses get_sub_field()
 */

function sage_get_sub_field( $key, $format=true ) {

    if ( function_exists( 'get_sub_field' ) &&  get_sub_field( $key ) ) {

        return get_sub_field( $key, $format );

    }


}


/**
 * Shortcut for 'echo _get_sub_field()'
 * @param str $key The meta key Value to return if there's no value for the custom field $key
 * @param str $format Whether to enable output content formating
 * @uses sage_get_sub_field()
 * @return void
 */

function sage_the_sub_field( $key, $format=true ) {

  echo sage_get_sub_field( $key, $format );

}


/**
 * Check if a given field has a sub field
 * @param str $key The meta key
 * @param mixed $id The post ID
 * @return bool
 * @uses has_sub_field()
 */

function sage_has_sub_field( $key, $id=false ) {

  if ( function_exists('has_sub_field') )

    return has_sub_field( $key, $id );

  else

    return false;

}


/**
 * Flexible Layout content
 */

function sage_get_row_content ( $row ) {

    $sections      = array();
    $layout        = $row['acf_fc_layout'];
    $prefix        = $layout .'_';
    $section_id    = uniqid($prefix);
    $section_title = $row['title'];

    if ($layout === 'editor' && $row['content']) {
      $sections[] = sprintf('<div class="section-content">%s</div>', $row['content']);
    } elseif ($layout === 'contact' && $row['contact']) {

      foreach ($row['contact'] as $item) {
        $contacts[] = <<<EOT
          <li>
            <span class="hexagon">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-{$item['icon']}">
                <use xlink:href="#{$item['icon']}"></use>
              </svg>
            </span>
            <h2 class="title">{$item['title']}</h2>
            <div class="content">{$item['content']}</div>
          </li>
EOT;
      }
      $sections[] = sprintf('<div class="section-content"><ul class="contacts">%s</ul></div>', implode('', $contacts));
    }

    $section_title_html = ($section_title) ? sprintf('<h3 class="section-title">%s</h3>', $section_title) : '';
    $section_content_html = implode('', $sections);

    return <<<EOT
      <div id="{$section_id}" class="section-layout-{$layout}">
        {$section_title_html}
        {$section_content_html}
      </div>
EOT;

}


/**
 * Creates flexible content instanse
 * @param mixed $name Flexible content name
 * @return 'string'
 * @uses sage_init_flexible_content( $name )
 */

function sage_flexible_content($field_name = 'extra_content') {

    if (!$field_name) return;

    $field_data = sage_get_field( $field_name );

    // check if the flexible content field exists
    if( !$field_data ) return;

    // loop through the rows of data
    while ( have_rows($field_name) ) : $row = the_row();

        // setup data to pass
        $row_data = get_row($row);

        // collect layout content
        return sage_get_row_content($row_data);

    endwhile;
}


/**
 *  Header Logo
 */

function sage_header_logo() {

    $tag = (is_front_page()) ? 'h1' : 'strong'; ?>

    <div class="navbar-brand-wrapper">
      <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo get_bloginfo('name'); ?>">
        <?php if (has_site_icon()) : ?>
          <img class="brand-img" src="<?php echo get_site_icon_url(190); ?>" alt="<?php echo get_bloginfo('name'); ?>">
        <?php endif; ?>
        <div class="brand-text">
          <<?php echo $tag; ?>><?php echo get_bloginfo('title'); ?></<?php echo $tag; ?>>
          <p><?php echo get_bloginfo('description'); ?></p>
        </div>
      </a>
    </div>

<?php }


/**
 *  Header navbar class
 *  @todo adjust to new position modes(normal/sticky)
 */

function sage_header_navbar_class() {

    $options = sage_get_options();

    $navbar_position = $options['navbar_position'];
    $navbar_class = $navbar_position ? 'navbar-' . $navbar_position : 'navbar-static';

    echo $navbar_class;

}


/**
 * Footer info
 */

function sage_info() {
    $options = sage_get_options();

    if($options['content-info']) printf('<div class="info">%s</div>',
        $options['content-info']
    );
}


/**
 *  Page header
 */

function sage_page_header($post_id=false) {

  global $post;

  if (isset($post->ID) && !$post_id) $post_id = $post->ID;

  $thumb_id = get_post_thumbnail_id( $post_id );
  $src = wp_get_attachment_image_src($thumb_id, 'full');

  $style = array(
    'background-image'  => 'url('. $src[0] .')'
  );

  array_walk($style, function(&$a, $b) { $a = "$b: $a"; });

  $title = sage_get_field('title_text') ? sage_get_field('title_text') : Titles\title();
  $tag = is_front_page() ? 'h2' : 'h1';
  ?>

  <div class="page-header" style="<?php echo implode('; ', $style); ?>">
    <<?php echo $tag; ?> class="title"><?php echo $title; ?></<?php echo $tag; ?>>
  </div>

  <?php
}


/**
 *  Apply background
 */
function sage_bind_page_bg() {
  $options = sage_get_options();
  $id = $options['page_background'];
  $src = wp_get_attachment_image_src($id, 'full');

  if ($src) {
    $style = array(
      'background-image'  => 'url('. $src[0] .')'
    );

    array_walk($style, function(&$a, $b) { $a = "$b: $a"; });

    echo implode('; ', $style);
  }
}
