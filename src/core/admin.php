<?php
/**
 * Create admin page and register script for plugin
 * @package wpns
 * @author Duy Nguyen
 */

class WPNS_Admin {

	/**
	 * Initiliaze
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'wpns_plugin_script' ) );
		$this->wpns_load_textdomain();
	}

	/**
	 * function callback to enqueue scripts and style
	 */
	public function wpns_plugin_script() {
		wp_enqueue_style( 'wpns-style', WPNS_URL . 'assist/css/style.min.css' );
		wp_enqueue_style( 'wpns-fontawesome', WPNS_URL . 'assist/css/font-awesome.min.css' );
	}

	/**
	 * Loading text domain for plugin
	 */
	public function wpns_load_textdomain() {
		load_plugin_textdomain( 'wp-nice-search', false, WPNS_DIR . '/languages' );
	}

} // end class WPNS_Admin

new WPNS_Admin;
