<?php

/*
Plugin Name: WP Books Manager
Plugin URI:  https://moveup.media
Description: Plugin for managing books, genres and shortcode for displaying the most recent books.
Version:     1.0
Author:      Jhonatan Jacinto
Author URI:  https://moveup.media
Text Domain: wp-books-manager-moveup
Domain Path: /languages
License:     GPLv2 or later
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'WPBM_VERSION', '1.0' );
define( 'WPBM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once WPBM_PLUGIN_DIR . 'includes/class-wp-book-manager.php';

add_action( 'plugins_loaded', function() {
    (new WP_Book_Manager())->register_hooks();
} );