<?php
/**
Plugin Name: Polylang add-on: Preserve admin language in admin bar
Plugin Description: 
Author: Aucor Oy
Version: 1.0.0
Author URI: http://www.aucor.fi
*/
function preserve_admin_language_in_admin_bar($mofile, $domain) {
  if( is_admin_bar_showing() && !is_admin() && $domain == 'default' ) {
    $user_id = get_current_user_id();

    $user_language = get_user_meta( $user_id, 'user_lang', true);
    
    if ( $user_language !== '' ) {
      $user_language = get_option('WPLANG');
    }
    
    $mofile = WP_LANG_DIR . "/$user_language.mo";
  }

  return $mofile;
}
add_filter('load_textdomain_mofile', 'preserve_admin_language_in_admin_bar', 10, 2);
