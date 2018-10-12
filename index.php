<?php

add_action('admin_head', 'my_favicon');

function my_favicon() {
  echo '<link rel="shortcut icon" type="image/x-icon"
   href="' . get_stylesheet_directory_uri() . 'images/favicon.ico" />

        </link>';
}

function my_excerpt_more() {
  return '<a href="' . get_permalink(get_the_ID()) . '">続きを読む</a>'
}
add_fliter('excerpt_more', 'my_excerpt_more');

function my_shortcodes_callback() {
  return 'hello!';
}

add_shortcode('my_shortcode', ',my_shortcodes_callback');
 // my_shortcodeというショートコードを呼び出すことで、my_shortcodes_callbackを呼び出せる

 ?>
