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

function sage_get_excerpt($id = null, $limit = 35) {
  if ( isset( $post->ID ) && !$id ) $id = $post->ID;

  $content = explode(' ', get_post_field('post_content', $id), $limit);

  if (count($content) >= $limit) {
    array_pop($content);
    $content = sprintf('%s&hellip; <a class="more" href="%s">%s</a>',
      implode(" ",$content),
      get_the_permalink($id),
      __('Read More', 'sage')
    );
  } else {
    $content = implode(" ",$content);
  }

  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);

  return $content;
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

function sage_get_row_content ( $row, $args = array() ) {

  $output        = '';
  $layout        = $row['acf_fc_layout'];
  $scroll_target = $row['next_sibling_id'];
  $prefix        = $layout .'_';
  $section_id    = $row['section_id'] ? $row['section_id'] : uniqid($prefix);
  $section_title = $row['section_title'];
  $numberposts   = array_key_exists('numberposts', $args) ? $args['numberposts'] : '';

  $title_color   = $row['title_color'];
  $content_color = array_key_exists('content_color', $row) ? $row['content_color'] : '';
  $bg_color      = $row['background_color'];
  $bg_image      = $row['background_image'];
  $bg_size       = $row['background_size'];
  $bg_repeat     = $row['background_repeat'];
  $bg_position   = $row['background_position_x'] .' '. $row['background_position_y'];
  $bg_attachment = $row['background_attachment'];
  $section_style = '';

  if ($bg_color) $section_style .= sprintf('background-color: %s; ', $bg_color);
  if ($bg_image) {
    $section_style .= sprintf('background-image: url(%s); ', $bg_image);
    if ($bg_size) $section_style .= sprintf('background-size: %s; ', $bg_size);
    if ($bg_repeat) $section_style .= sprintf('background-repeat: %s; ', $bg_repeat);
    if ($bg_position) $section_style .= sprintf('background-position: %s; ', $bg_position);
    if ($bg_attachment) $section_style .= sprintf('background-attachment: %s; ', $bg_attachment);
  }

  if ($layout === 'contact' && $row['contact']) {

    $contacts = array();

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

  } elseif ($layout === 'portfolio' && $row['carousel']) {

    $carouselID = 'portfolio-carousel';
    $portfolio  = array();

    foreach ($row['carousel'] as $item) {
      $portfolio[] = <<<EOT

        <li>
            <div class="thumb-wrapper">
                <a href="{$item['url']}">
                    <img src="{$item['thumb']}" alt="{$item['title']}">
                </a>
            </div>
            <h3 class="title">{$item['title']}</h3>
        </li>
EOT;
    }

    $portfolio = implode('', $portfolio);

    $sections[] = <<<EOT

      <div id="{$carouselID}" class="portfolio-carousel section-content">
        <div class="jcarousel">
          <ul>{$portfolio}</ul>
        </div>
        <button type="button" class="jcarousel-control-prev">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#prev-thin"></use></svg>
        </button>
        <button type="button" class="jcarousel-control-next">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#next-thin"></use></svg>
        </button>
      </div>
EOT;

  } elseif ($layout === 'testimonials' && $row['carousel']) {

    $carouselID = 'testimonial-carousel';
    $testimonials = array();

    $i=-1;
    foreach ($row['carousel'] as $item) {
      $i++;
      $active = ($i === 0) ? 'active' : '';

      $testimonials[] = <<<EOT

      <li class="carousel-item {$active}">
        <blockquote class="quote">
          {$item['quote']}
          <footer class="author">
            <img class="thumb" src="{$item['thumb']['sizes']['thumbnail']}" alt="{$item['name']}">
            <span class="cite">
              <strong class="name">{$item['name']}</strong>
              <em class="position">{$item['position']}</em>
            </span>
          </footer>
        </blockquote>
      </li>
EOT;
    }

    $testimonials = implode('', $testimonials);

    return <<<EOT

    <div id="{$carouselid}" class="carousel slide" data-ride="carousel" data-interval="0">
      <ul class="carousel-inner" role="listbox">{$testimonials}</ul>

      <a class="left carousel-control" href="#{$carouselID}" role="button" data-slide="prev">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#left"></use></svg>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#{$carouselID}" role="button" data-slide="next">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#right"></use></svg>
        <span class="sr-only">Next</span>
      </a>

    </div>
EOT;

  } elseif ($layout === 'editor' && $row['content']) {

    $sections[] = sprintf('<div class="section-content">%s</div>', $row['content']);

  } elseif ($layout === 'blog') {


    if($numberposts) {

      $posts = get_posts(array(
        'post_type'   => 'post',
        'numberposts' => $numberposts,
        'offset'      => $args['offset'],
        'post_status' => 'publish'
      ));

      if ($posts) {

        foreach ($posts as $post) {
          $thumb   = get_the_post_thumbnail($post->ID, 'large');
          $title   = Titles\title($post->ID);
          $excerpt = sage_get_excerpt($post->ID);

          $sections[] = <<<EOT

          <li>
            <article>
              <div class="thumb">{$thumb}</div>
              <h3 class="title">{$title}</h3>
              <div class="excerpt">{$excerpt}</div>
            </article>
          </li>
EOT;
        }

      }

    } else {

      $load_p_mesg = __('Load More', 'sage');

      $sections[] = <<<EOT

      <div class="section-content">
        <ul class="article-list"></ul>
        <p class="load-p-control">
          <button type="button">
            <span class="spinner">
              <span class="bounce1"></span>
              <span class="bounce2"></span>
              <span class="bounce3"></span>
            </span>
            {$load_p_mesg}
          </button>
        </p>
      </div>
EOT;
    }

  }

  if ($numberposts) {
    return implode('', $sections);
  } else {

    $content_style        = $content_color ? sprintf('style="color: %s"', $content_color) : '';
    $section_title_html   = $section_title ? sage_get_heading('h2', $section_title) : '';
    $section_content_html = implode('', $sections);
    $down_link_style      = array();
    $down_icon_style      = array();

    if($bg_color) {
      $down_icon_style['fill'] = sage_complementary_color($bg_color);
      $down_link_style['background-color'] = $bg_color;
    } else {
      $down_icon_style['fill'] = '#464646';
      $down_link_style['background-color'] = '#ffffff';
    }

    array_walk($down_link_style, function(&$a, $b) { $a = "$b: $a"; });
    array_walk($down_icon_style, function(&$a, $b) { $a = "$b: $a"; });
    $down_link_style = implode('; ', $down_link_style);
    $down_icon_style = implode('; ', $down_icon_style);

    return <<<EOT
      <div id="{$section_id}" class="section section-{$layout}" style="{$section_style}">
        <div class="section-wrapper" {$content_style}>
          {$section_title_html}
          {$section_content_html}
        </div>
        <a href="#{$scroll_target}" class="nav-link scroll-btn" style="{$down_link_style}">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xlink:href="#down-thin" style="{$down_icon_style}"></use></svg>
        </a>
      </div>
EOT;

  }

}


/**
 * Creates flexible content instanse
 * @param mixed $name Flexible content name
 * @return 'string'
 * @uses sage_init_flexible_content( $name )
 */

function sage_flexible_content($field_name = 'sections') {

  if (!$field_name) return;

  $field_data = sage_get_field( $field_name );

  // check if the flexible content field exists
  if( !$field_data ) return sprintf('<div class="page-content container">%s</div>', apply_filters('the_content', get_the_content()));

  // loop through the rows of data
  $content = array();
  $i = 0;

  foreach ( $field_data as $field ) {

    // pass next sibling id
    $index           = ++$i;
    $next_sibling = isset($field_data[$index]) ? $field_data[$index] : array('section_id' => 'contact-form');

    $field['next_sibling_id'] = array_key_exists('section_id', $next_sibling) ? $next_sibling['section_id'] : '-1';

    // collect layout content
    $content[] = sage_get_row_content($field);

  }

  return implode('', $content);

}


/**
 *  Header Logo
 */

function sage_header_logo() {

    $tag = (is_front_page()) ? 'h1' : 'strong'; ?>

    <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo get_bloginfo('name'); ?>">
      <?php if (has_site_icon()) : ?>
        <img class="brand-img" src="<?php echo get_site_icon_url(190); ?>" alt="<?php echo get_bloginfo('name'); ?>">
      <?php endif; ?>
      <div class="brand-text">
        <<?php echo $tag; ?>><?php echo get_bloginfo('title'); ?></<?php echo $tag; ?>>
        <p><?php echo get_bloginfo('description'); ?></p>
      </div>
    </a>

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

    if($options['content_info']) printf('<div class="info">%s</div>',
        $options['content_info']
    );
}


/**
 *  Section header
 */

function sage_get_heading($tag = 'h1', $title = null) {

  $title = $title ? $title : Titles\title();

  return sprintf('<%1$s class="section-title">%2$s</%1$s>', $tag, $title);

}


/**
 * Adjust brightness
 * @param string $hex Hex formatted color
 * @param number $steps Hex formatted color
 * @return 'string'
 * @uses sage_adjust_brightness( '#9d9d9d', 100 );
 */

function sage_adjust_brightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}


/**
 * Get closest color name
 * @param string $rgb Hex formatted color
 * @return 'string'
 * @uses sage_closest_color( '#b2b2b2' )
 */

function sage_closest_color($hex) {
    // these are not the actual rgb values
    $colors = array('WHITE' => '#FFFFFF', 'BRAND' => '#E5921B', 'BLACK' => '#000000');

    $deviation = PHP_INT_MAX;
    $closestColor = "";
    foreach ($colors as $name => $rgbColor) {
      $diff = sage_color_diff($rgbColor, $hex);
        if ( $diff < $deviation) {
            $deviation = $diff;
            $closestColor = $name;
        }

    }
    return $closestColor;

}


/**
 * Get two hex colors diff
 * @param string $hex1 Hex formatted color
 * @param string $hex2 Hex formatted color
 * @return 'number'
 * @uses sage_color_diff( '#9d9d9d', '#a3a3a3' )
 */

function sage_color_diff($hex1, $hex2) {
    $rgb1 = sage_hex2rgb($hex1);
    $rgb2 = sage_hex2rgb($hex2);

    return abs($rgb1[0] - $rgb2[0]) + abs($rgb1[1] - $rgb2[1]) + abs($rgb1[2] - $rgb2[2]) ;
}


/**
 * Convert hex to rgb color
 * @param string $hex Hex formatted color
 * @return 'array'
 * @uses sage_hex2rgb( '#9d9d9d' )
 */

function sage_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}


/**
 * Get complementary color
 * @param string $hex Hex formatted color
 * @return 'string'
 * @uses sage_complementary_color( '#9d9d9d' )
 */

function sage_complementary_color($hex) {
  $output = '';
  $color_name = sage_closest_color($hex);

  if ($color_name === 'WHITE') {
    $output = sage_adjust_brightness($hex, -50);

  } elseif ($color_name === 'BLACK') {
    $output = sage_adjust_brightness($hex, 100);

  } elseif ($color_name === 'BRAND') {
    $output = sage_adjust_brightness($hex, 50);

  }

  return $output;

}


/*
 * Page background
 */
function sage_apply_page_bg() {
  $options       = sage_get_options();
  $bg_color      = $options['page_bg_color'];
  $bg_image      = wp_get_attachment_url($options['page_bg_image']);
  $bg_size       = $options['page_bg_size'];
  $bg_repeat     = $options['page_bg_repeat'];
  $bg_position   = $options['page_bg_position_x'] .' '. $options['page_bg_position_y'];
  $bg_attachment = $options['page_bg_attachment'];
  $style         = array();

  if ($bg_color) $style['background-color'] = $bg_color;
  if ($bg_image) {
    $style['background-image'] = sprintf('url(%s)', $bg_image);
    if ($bg_size) $style['background-size'] = $bg_size;
    if ($bg_repeat) $style['background-repeat'] = $bg_repeat;
    if ($bg_position) $style['background-position'] = $bg_position;
    if ($bg_attachment) $style['background-attachment'] = $bg_attachment;
  }

  array_walk($style, function(&$a, $b) { $a = "$b: $a"; });

  return implode('; ', $style);
}
