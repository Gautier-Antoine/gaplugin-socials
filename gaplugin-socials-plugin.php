<?php
/*
Plugin Name: Socials-GA
Plugin URI: https://github.com/Pepite61/gaplugin-socials
Description: Plugin for your socials media
Version: 0.00.01

Requires at least: 5.2
Requires PHP: 7.2

Author: GAUTIER Antoine
Author URI: gautierantoine.com
Text Domain: share-socials-text
Domain Path: /languages
License:     GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Socials-GA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or any later version.

Socials-GA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Socials-GA.
If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

  defined('ABSPATH') or die('You cannot enter');
  add_filter(
    'rest_authentication_errors',
    function( $result ) {
      if ( true === $result || is_wp_error( $result ) ) {
          return $result;
      }
      if ( ! is_user_logged_in() ) {
          return new WP_Error(
              'rest_not_logged_in',
              __( 'You are not currently logged in', 'share-socials-text' ),
              array( 'status' => 401 )
          );
      }
      return $result;
    }
  );
  if (!class_exists('GAPlugin\AdminPage')){
    require_once 'includes/AdminPage.php';
  }
  require_once 'includes/Follow.php';
  require_once 'includes/Share.php';

  register_uninstall_hook( __FILE__, ['GAPlugin\Follow', 'removeOptions']);
  register_uninstall_hook( __FILE__, ['GAPlugin\Share', 'removeOptions']);

  add_action(
    'init',
    function () {
      GAPlugin\Follow::register();
      GAPlugin\Share::register();
    }
  );
