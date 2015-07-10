<?php
/**
 * Load core engine
 */
require WPNS_DIR . '/src/core/admin.php';
require WPNS_DIR . '/src/core/search-data.php';
require WPNS_DIR . '/src/shortcode/form.php';

register_activation_hook( WPNS_DIR . '/wp-nice-search.php', 'wpns_plugin_activate' );

/**
 * Activate action
*/
function wpns_plugin_activate() {

	$default_settings = array(
		'wpns_in_all' => null,
		'wpns_in_post' => 'on',
		'wpns_in_page' => null,
		//'wpns_in_category' => null,
		'wpns_in_custom_post_type' => null,
		'wpns_placeholder' => 'Type your words here...',
	);

	if (version_compare(get_bloginfo('version'), WPNS_REQUIRE_VER, '<')) {
		deactivate_plugins(basename(WPNS_DIR . '/wp-nice-search.php'));
		wp_die('Current version of wordpress is lower require version (' . WPNS_REQUIRE_VER . ')');
	} else {
		// Save default settings and configution
		update_option( 'wpns_options' , $default_settings);
	}
}

