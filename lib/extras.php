<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Utils;

/**
 * Add <body> classes
 */

add_filter('body_class', __NAMESPACE__ . '\\sage_body_class');

function sage_body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }
  return $classes;
}


/**
 * Custom excerpt length
 */

function sage_excerpt_length( $length ) {
    return 35;
}
add_filter( 'excerpt_length', __NAMESPACE__ . '\\sage_excerpt_length', 999 );


/**
 * Clean up the_excerpt()
 */

add_filter('excerpt_more', __NAMESPACE__ . '\\sage_excerpt_more');

function sage_excerpt_more() {
  return '&hellip; <a class="more" href="' . get_permalink() . '">' . __('Read More', 'sage') . '</a>';
}


/**
 * Filtering the Wrapper: Custom Post Types
 */

add_filter('sage/wrap_base', __NAMESPACE__ . '\\sage_wrap_base_cpts');

function sage_wrap_base_cpts($templates) {
    $cpt = get_post_type();
    if ($cpt) {
       array_unshift($templates, __NAMESPACE__ . 'base-' . $cpt . '.php');
    }
    return $templates;
}


/**
 * Search Filter
 */

add_action('pre_get_posts', __NAMESPACE__ . '\\sage_search_filter');

function sage_search_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', array('post', 'page'));
    }
  }
}


/**
 * Login Image
 * @todo set proper image size
 */

add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\sage_login_logo' );

function sage_login_logo() {

  if (has_site_icon()) {

    $size = 95;
    $logo_src = get_site_icon_url($size);

     ?><style type="text/css">
        body.login div#login h1 a {
          background-image: url(<?php echo $logo_src; ?>);
          background-size: contain;
        }
        .login h1 a {
          height: <?php echo $size; ?>px !important;
          width: <?php echo $size; ?>px !important;
        }
    </style>
<?php }}


/**
 * Gravity Forms Field Choice Markup Pre-render
 */

add_filter( 'gform_field_choice_markup_pre_render', __NAMESPACE__ . '\\sage_choice_render', 10, 4 );

function sage_choice_render($choice_markup, $choice, $field, $value){
    if ( $field->get_input_type() == 'radio' || 'checkbox' ) {
        $choice_markup = preg_replace("/(<li[^>]*>)\s*(<input[^>]*>)\s*(<label[^>]*>)\s*([\w\s]*<\/label>\s*<\/li>)/", '$1$3$2$4', $choice_markup);
        return $choice_markup;
    }
    return $choice_markup;
}


/**
 * Custom HTML
 */

add_action( 'get_header', __NAMESPACE__ . '\\sage_custom_html', 999 );

function sage_custom_html(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_html'];
    echo $editor_content ? $editor_content : '';
}


/**
 * Custom CSS
 */

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\sage_custom_css', 999 );

function sage_custom_css(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_css'];
    wp_add_inline_style( 'sage_css', $editor_content );
}


/**
 * Custom JS
 */

add_action( 'wp_footer', __NAMESPACE__ . '\\sage_custom_js', 999 );

function sage_custom_js(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_js'];
    echo $editor_content ? '<script>'. $editor_content .'</script>' : '';
}


/**
 * Remove image attributes
 */

add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'the_content', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'get_avatar', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );

function sage_remove_thumbnail_dimensions( $html ) {
    // Loop through all <img> tags
    if (preg_match_all('/<img[^>]+>/ims', $html, $matches)) {
        foreach ($matches as $match) {
            // Replace all occurences of width/height
            $clean = preg_replace('/(width|height)=["\'\d%\s]+/ims', "", $match);
            // Replace with result within html
            $html = str_replace($match, $clean, $html);
        }
    }
    return $html;
}


/**
 * Register the html5 figure-non-responsive code fix.
 */

add_filter( 'img_caption_shortcode', __NAMESPACE__ . '\\sage_img_caption_shortcode_filter', 10, 3 );

function sage_img_caption_shortcode_filter($dummy, $attr, $content) {
  $atts = shortcode_atts( array(
      'id'      => '',
      'align'   => 'alignnone',
      'width'   => '',
      'caption' => '',
      'class'   => '',
  ), $attr, 'caption' );

  $atts['width'] = (int) $atts['width'];
  if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
      return $content;

  if ( ! empty( $atts['id'] ) )
      $atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';

  $class = trim( 'wp-caption figure ' . $atts['align'] . ' ' . $atts['class'] );

  if ( current_theme_supports( 'html5', 'caption' ) ) {
      return '<figure ' . $atts['id'] . 'style="max-width: ' . (int) $atts['width'] . 'px;" class="' . esc_attr( $class ) . '">'
      . do_shortcode( $content ) . '<figcaption class="wp-caption-text figure-caption">' . $atts['caption'] . '</figcaption></figure>';
  }

  // Return nothing to allow for default behaviour!!!
  return '';

}


/**
 * Allow upload SVG
 */

add_filter('upload_mimes', __NAMESPACE__ . '\\sage_mime_types');

function sage_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}


/**
 * Set prev/next posts links classes
 */

add_filter('previous_posts_link_attributes', __NAMESPACE__ . '\\prev_posts_link_attributes');
add_filter('next_posts_link_attributes', __NAMESPACE__ . '\\next_posts_link_attributes');

function prev_posts_link_attributes() {
    return 'class="prev-posts-link"';
}

function next_posts_link_attributes() {
    return 'class="next-posts-link"';
}


/*
 * Custom site icon size. Used for site logo
 */

add_filter( 'site_icon_image_sizes', __NAMESPACE__ . '\\prefix_custom_site_icon_size' );

function prefix_custom_site_icon_size( $sizes ) {
   $sizes[] = 95;

   return $sizes;
}


add_filter( 'site_icon_meta_tags', __NAMESPACE__ . '\\prefix_custom_site_icon_tag' );

function prefix_custom_site_icon_tag( $meta_tags ) {
   $meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="95x95" />', esc_url( get_site_icon_url( null, 64 ) ) );

   return $meta_tags;
}


/**
 * AJAX Blog posts
 */

add_action('wp_ajax_nopriv_get_posts', __NAMESPACE__ . '\\sage_get_posts');
add_action('wp_ajax_get_posts', __NAMESPACE__ . '\\sage_get_posts');

function sage_get_posts(){
  $output = array();
  $postcount = wp_count_posts('post');
  $width = (isset($_POST['width'])) ? $_POST['width'] : '0';
  $offset = (isset($_POST['offset'])) ? $_POST['offset'] : '0';

  if ($width >= 1200) $numberposts = '3';
  elseif ($width >= 768) $numberposts = '2';
  else $numberposts = '1';

  $status = ($numberposts + $offset >= $postcount->publish) ? 'full' : '';

  $args = array(
      'numberposts' => $numberposts,
      'offset'      => $offset
  );

  $field = 'sections';
  $frontpage_id = get_option('page_on_front');
  $field_data = Utils\sage_get_field( $field, $frontpage_id );

  if ($field_data) {

    foreach ( $field_data as $field ) {

      if($field['acf_fc_layout'] === 'blog') {
        $output['content'] = Utils\sage_get_row_content($field, $args);
        $output['status'] = $status;
      }

    }

  }

  echo json_encode($output);

  wp_die();
}
