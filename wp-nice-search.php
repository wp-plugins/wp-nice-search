<?php
/**
 * Plugin Name: WP Nice Search
 * Description: Live search for wordpress
 * Version: 1.0.4
 * Author: Duy Nguyen
 * Author URI: duywp.com
 * License: GPL v2
 */

define( 'WPNS_DIR', dirname(__FILE__) );

define( 'WPNS_URL', plugin_dir_url( __FILE__ ) );

define( 'WPNS_PLUGIN_VER', '1.0.3');

define( 'WPNS_REQUIRE_VER', '3.9');

require WPNS_DIR . '/src/loader.php';
