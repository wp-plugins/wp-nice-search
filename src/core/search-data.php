<?php
/**
 * Register script for ajax script and handle request search ajax
 * @package wpns
 * @author Duy Nguyen
 */
class WPNS_SEARCH_DATA {
	/**
	 * Initiliaze
	 */
	function __construct() {

		add_action( 'template_redirect', array( &$this, 'wpns_register_script') );

		// enable ajax for logged-in user
		add_action( 'wp_ajax_wpns_search_ajax', array( &$this, 'wpns_search_data') );

		// enabled ajax for visitors user
		add_action( 'wp_ajax_nopriv_wpns_search_ajax', array( &$this, 'wpns_search_data') );
	}

	/**
	 * Add script for ajax request
	 */
	public function wpns_register_script() {

		wp_enqueue_script( 'wpns_ajax_search', WPNS_URL . 'assist/js/search.js', array('jquery'), '', true );

		$protocol = isset( $_SERVER['HTTPS']) ? 'https://' : 'http://';

		$params = array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $protocol )
		);
		wp_localize_script( 'wpns_ajax_search', 'wpns_ajax_url', $params );
	}

	/**
	 * Fetch data from database and return a string response ajax request
	 */
	public function wpns_search_data() {
		global $wpdb;
		$settings = get_option( 'wpns_options' );
		
		if ( $settings['wpns_in_all'] == 'on' || ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == 'on' )) {
			$t = "post_type NOT IN ('revision', '_pods_pod')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type = 'post'";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type = 'page'";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'post', '_pods_pod', 'page')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type IN ('post', 'page')";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'post', '_pods_pod')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'page', '_pods_pod')";
		}


		$keyword = $_POST['keyword'];
		if ($keyword == '') {
			echo '<div class="wpns_results_list">No Posts were found</div>';
		} else {
			$keyword = str_replace(' ', '%', $keyword);

			$sql = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND ($t) AND (post_content LIKE '%$keyword%' OR post_title LIKE '%$keyword%')";
			$results = $wpdb->get_results($sql);
			echo $this->wpns_render_results_list($results);
		}

		wp_die();
	}

	/**
	 * @return string $results_str
	 */

	public function wpns_render_results_list(array $data) {
		$results_str = '';
		if (!empty($data)) {
			$results_str .= '<ul class="wpns_results_list">';
			foreach ($data as $key => $val) {
				$id = $val->ID;
				$url = get_permalink($id);
				$title = $val->post_title;
				$type = $val->post_type;
				switch ($type) {
					case 'post':
						$type_icon = '<i class="fa fa-file-text-o"></i>';
						break;
					case 'page':
						$type_icon = '<i class="fa fa-file-powerpoint-o"></i>';
						break;
					default:
						$type_icon = '<i class="fa fa-file-code-o"></i>';
						break;
				}

				$results_str .= '<li>'. $type_icon . ' <a href="' . $url . '">' . $title . '</a></li>';
			}
			$results_str .= '</ul>';
		} else {
			$results_str = '<div class="wpns_results_list">No Posts were found</div>';
		}

		return $results_str;

	}	
} // end class WPNS_SEARCH_DATA
new WPNS_SEARCH_DATA;
