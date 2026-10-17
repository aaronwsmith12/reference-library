<?php
/**
 * Plugin Name: Reference Library
 * Description: A searchable reference library with custom entries, topics, sources, and live AJAX search.
 * Version: 0.1.0
 * Author: Aaron Smith
 * License: MIT
 * Text Domain: reference-library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RL_VERSION', '0.1.0' );
define( 'RL_PATH', plugin_dir_path( __FILE__ ) );
define( 'RL_URL', plugin_dir_url( __FILE__ ) );

require_once RL_PATH . 'includes/post-types.php';
require_once RL_PATH . 'includes/shortcode.php';