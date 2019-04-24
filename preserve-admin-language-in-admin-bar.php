<?php
/**
Plugin Name: Polylang add-on: Preserve admin language in admin bar
Plugin Description:
Author: Aucor Oy
Version: 1.0.1
Author URI: https://www.aucor.fi
*/

/**
 * Checks if current view may require admin bar language switching
 *
 * The function is based on is_admin_bar_showing() but cannot use it directly
 * as `is_embed()` inside that function causes notices in this hook.
 *
 * @see https://developer.wordpress.org/reference/functions/is_admin_bar_showing/
 */
function preserve_admin_language_is_needed() {

  global $show_admin_bar, $pagenow;

  // for all these types of requests, we never want an admin bar.
  if (defined( 'XMLRPC_REQUEST' ) || defined( 'DOING_AJAX' ) || defined( 'IFRAME_REQUEST' ) || (function_exists('wp_is_json_request') && wp_is_json_request())) {
      return false;
  }

  if (!isset($show_admin_bar)) {
    if (!is_user_logged_in() || 'wp-login.php' == $pagenow) {
      $show_admin_bar = false;
    } else {
      $show_admin_bar = _get_admin_bar_pref();
    }
  }

  /**
   * Filters whether to show the admin bar.
   *
   * Returning false to this hook is the recommended way to hide the admin bar.
   * The user's display preference is used for logged in users.
   *
   * @since 3.1.0
   *
   * @param bool $show_admin_bar Whether the admin bar should be shown. Default false.
   */
  $show_admin_bar = apply_filters('show_admin_bar', $show_admin_bar);
  return $show_admin_bar;

}

/**
 * If user has picked their preferred language, keep admin bar in that locale.
 *
 * @param string $mofile path to the MO file
 * @param string $domain text domain
 *
 * @return string $mofile
 */
function preserve_admin_language_in_admin_bar($mofile, $domain) {

  if (preserve_admin_language_is_needed() && $domain == 'default' ) {

    $user_id = get_current_user_id();
    $user_language = get_user_meta($user_id, 'user_lang', true);

    if ($user_language !== '') {
      $user_language = get_option('WPLANG');
    }

    $mofile = WP_LANG_DIR . "/$user_language.mo";

  }

  return $mofile;

}
add_filter('load_textdomain_mofile', 'preserve_admin_language_in_admin_bar', 10, 2);
