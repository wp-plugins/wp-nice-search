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
			$t = "post_type NOT IN ('revision', '_pods_pod', 'attachment', 'acf-field', 'acf-field-group', 'nav_menu_item')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type = 'post'";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type = 'page'";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'post', '_pods_pod', 'page', 'attachment', 'acf-field', 'acf-field-group', 'nav_menu_item')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == null ) {
			$t = "post_type IN ('post', 'page')";
		}

		if ( $settings['wpns_in_post'] == null && $settings['wpns_in_page'] == 'on' && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'post', '_pods_pod', 'attachment', 'acf-field', 'acf-field-group', 'nav_menu_item')";
		}

		if ( $settings['wpns_in_post'] == 'on' && $settings['wpns_in_page'] == null && $settings['wpns_in_custom_post_type'] == 'on' ) {
			$t = "post_type NOT IN ('revision', 'page', '_pods_pod', 'attachment', 'acf-field', 'acf-field-group', 'nav_menu_item')";
		}


		$keyword = $_POST['keyword'];
		if ($keyword == '') {
			echo '<div class="wpns_results_list">No Posts were found</div>';
		} else {
			$keyword = str_replace(' ', '%', $keyword);

			$sql = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND ($t) AND (post_title LIKE '%$keyword%')";
			//$sql = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND ($t) AND (post_content LIKE '%$keyword%' OR post_title LIKE '%$keyword%')";
			$results = $wpdb->get_results($sql);
			if ( $settings['wpns_items_featured'] == 'on' && $settings['chk_items_meta'] == 'on' ) {
				echo $this->wpns_render_results_list_meta_featured($results);
			} elseif ( $settings['wpns_items_featured'] == 'on' ) {
				echo $this->wpns_render_results_list_featured($results);	
			} elseif( $settings['chk_items_meta'] == 'on' ) {
				echo $this->wpns_render_results_list_meta($results);
			} else {
				echo $this->wpns_render_results_list($results);
			}
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

				$results_str .= '<li><span class="wpns-items-pre">'. $type_icon . '</span> <span class="list-item-box"><a href="' . $url . '">' . $title . '</a></span></li>';
			}
			$results_str .= '</ul>';
		} else {
			$results_str = '<div class="wpns_results_list">No Posts were found</div>';
		}

		return $results_str;

	}

	/**
	 * Layout of results list with featured images
	 * @param array $data A array is passed to get data from database
	 * 
	 * @return string $results_str
	 */

	public function wpns_render_results_list_featured(array $data) {
		$results_str = '';
		if (!empty($data)) {
			$results_str .= '<ul class="wpns_results_list">';
			foreach ($data as $key => $val) {
				$id = $val->ID;
				$url = get_permalink($id);
				$title = $val->post_title;
				$type = $val->post_type;
				$featured_url = wp_get_attachment_thumb_url( get_post_thumbnail_id( $id ) );
				if (!$featured_url) {
					$featured_url = WPNS_URL . 'assist/images/no_photo.jpg';
				}

				$results_str .= '<li><span class="wpns-items-pre"><img src="'. $featured_url . '" alt="items-featured" /></span> <span class="list-item-box"><a href="' . $url . '">' . $title . '</a></span></li>';
			}
			$results_str .= '</ul>';
		} else {
			$results_str = '<div class="wpns_results_list">No Posts were found</div>';
		}

		return $results_str;

	}

	/**
	 * Layout of results list with meta section
	 * @param array $data A array is passed to get data from database
	 * 
	 * @return string $results_str
	 */

	public function wpns_render_results_list_meta(array $data) {
		$results_str = '';
		if (!empty($data)) {
			$results_str .= '<ul class="wpns_results_list">';
			foreach ($data as $key => $val) {
				$id = $val->ID;
				$url = get_permalink( $id );
				$title = $val->post_title;
				$type = $val->post_type;
				$date = get_the_date( 'd M, Y', $id );
				$author = get_user_meta( $val->post_author );

				if ( $author['first_name'][0] == '' && $author['last_name'][0] == '' ) {
					$author_name = $author['nickname'][0];
				} else {
					$author_name = $author['first_name'][0] . ' ' . $author['last_name'][0];
				}

				$terms = $this->custom_taxonomies_terms_links( $id );

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

				$results_str .= '<li><span class="wpns-items-pre">'. $type_icon . '</span> <span class="list-item-box"><a href="' . $url . '">' . $title . '</a>';
				$results_str .= '<br><span class="wpns-meta"><i><span class="wpns-author">' . $author_name . '</span> / <span class="wpns-date">' . $date . '</span><span class="wpns-cate">' . $terms . '</span></i></span></span>';
				$results_str .=  '</li>';
			}
			$results_str .= '</ul>';
		} else {
			$results_str = '<div class="wpns_results_list">No Posts were found</div>';
		}

		return $results_str;

	}

	/**
	 * Layout of results list with meta section
	 * @param array $data A array is passed to get data from database
	 * 
	 * @return string $results_str
	 */

	public function wpns_render_results_list_meta_featured(array $data) {
		$results_str = '';
		if (!empty($data)) {
			$results_str .= '<ul class="wpns_results_list">';
			foreach ($data as $key => $val) {
				$id = $val->ID;
				$url = get_permalink( $id );
				$title = $val->post_title;
				$type = $val->post_type;
				$date = get_the_date( 'd M, Y', $id );
				$author = get_user_meta( $val->post_author );

				if ( $author['first_name'][0] == '' && $author['last_name'][0] == '' ) {
					$author_name = $author['nickname'][0];
				} else {
					$author_name = $author['first_name'][0] . ' ' . $author['last_name'][0];
				}

				$terms = $this->custom_taxonomies_terms_links( $id );

				$featured_url = wp_get_attachment_thumb_url( get_post_thumbnail_id( $id ) );
				if (!$featured_url) {
					$featured_url = WPNS_URL . 'assist/images/no_photo.jpg';
				}

				$results_str .= '<li><span class="wpns-items-pre"><img src="'. $featured_url . '" alt="items-featured" /></span> <span class="list-item-box"><a href="' . $url . '">' . $title . '</a>';
				$results_str .= '<br><span class="wpns-meta"><i><span class="wpns-author">' . $author_name . '</span> / <span class="wpns-date">' . $date . '</span><span class="wpns-cate">' . $terms . '</span></i></span></span>';
				$results_str .=  '</li>';
			}
			$results_str .= '</ul>';
		} else {
			$results_str = '<div class="wpns_results_list">No Posts were found</div>';
		}

		return $results_str;

	}	

	// get taxonomies terms links
	public function custom_taxonomies_terms_links( $post_id ){
		// get post by post id
		$post = get_post( $post_id );

		// get post type by post
		$post_type = $post->post_type;

		// get post type taxonomies
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$out = array();

		foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){

			// get the terms related to post
			$terms = get_the_terms( $post->ID, $taxonomy_slug );

			if ($terms != false) {
				if ( !empty( $terms ) ) {
					foreach ( $terms as $key => $term ) {
						if ($term->taxonomy != 'post_tag') {
							$out[] = $term->name . '<span class="comma">, </span>';
						}
					}
				}
				$flag = true;
			}

		}

		if ( isset($flag) ) {
			array_unshift( $out, ' / ');
		}

		return implode('', $out );
	}

} // end class WPNS_SEARCH_DATA

new WPNS_SEARCH_DATA;
