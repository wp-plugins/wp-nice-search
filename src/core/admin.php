<?php
/**
 * Create admin page and register script for plugin
 * @package wpns
 * @author Duy Nguyen
 */

class WPNS_Admin {
	/**
	 * @var string $page_title Holds text title of plugin which displayed in browser bar
	 */
	protected $page_title = 'Nice Search';

	/**
	 * @var string $menu_title Holds text which name of menu
	 */
	protected $menu_title = 'Nice Search';

	/**
	 * @var string $capability The capability required for menu to be displayed to the user
	 */
	protected $capability = 'manage_options';

	/**
	 * @var string $menu_slug A unique slug name to refer to plugin menu
	 */
	protected $menu_slug = 'wpns-nice-search-menu';

	/**
	 * @var array $settings A array holds default values and updated values
	 */
	protected $settings = array(
		'wpns_in_all' => null,
		'wpns_in_post' => 'on',
		'wpns_in_page' => null,
		//'wpns_in_category' => null,
		'wpns_in_custom_post_type' => null,
		'wpns_placeholder' => 'Type your words here...',
	);

	/**
	 * Initiliaze
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'wpns_plugin_script' ) );
		add_action( 'admin_menu', array( &$this, 'wpns_add_plugin_page' ) );
		add_action( 'admin_init', array( &$this, 'wpns_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'wpns_admin_script' ) );
		$this->wpns_get_options( 'wpns_options' );
	}

	/**
	 * function callback to enqueue scripts and style
	 */
	public function wpns_plugin_script() {
		wp_enqueue_style( 'wpns-style', WPNS_URL . 'assist/css/style.min.css', array(), WPNS_PLUGIN_VER );
		wp_enqueue_style( 'wpns-fontawesome', WPNS_URL . 'assist/css/font-awesome.min.css', array(), WPNS_PLUGIN_VER );
	}

	/**
	 * Add script to admin
	 */
	public function wpns_admin_script() {
		wp_enqueue_script( 'wpns-admin-script', WPNS_URL . 'assist/js/admin.js', array('jquery'), WPNS_PLUGIN_VER );
	}

	/**
	 * function callback to add plugin page in plugins menu
	 */
	public function wpns_add_plugin_page() {
		add_options_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array( &$this, 'wpns_html_plugin_page' ) );
	}

	/**
	 * function callback to render html for plugin page
	 */
	public function wpns_html_plugin_page() {
		include WPNS_DIR . '/templates/admin.php';
	}

	/**
	 * Register and define the settings
	 */
	public function wpns_admin_init() {
		register_setting( 'wpns_options', 'wpns_options', array( &$this, 'wpns_validate_options' ) );

		add_settings_section( 'wpns_group_1', '', array( &$this, 'wpns_section_1'), $this->menu_slug );
		add_settings_field( 'wpns_checkbox', 'Search In', array( &$this, 'wpns_setting_checkbox' ), $this->menu_slug, 'wpns_group_1' );

		add_settings_section( 'wpns_group_2', '', array( &$this, 'wpns_section_2'), $this->menu_slug );
		add_settings_field( 'wpns_text', 'Placeholder Text', array( &$this, 'wpns_setting_text' ), $this->menu_slug, 'wpns_group_2' );
	}

	/**
	 * Draw the section header group 1
	 */
	public function wpns_section_1() {
		echo '<h3>Global settings</h3>';
	}

	/**
	 * Display and fill the field group 1
	 */
	public function wpns_setting_checkbox() {
		?>
		<fieldset>
			<label>
				<input type="checkbox" id="chk_all" name="wpns_options[wpns_in_all]" <?php echo ($this->settings['wpns_in_all'] == 'on') ? 'checked' : ''; ?> />
				All
			</label>
			<br>
			<label>
				<input type="checkbox" class="chk_items" name="wpns_options[wpns_in_post]" <?php echo ($this->settings['wpns_in_post'] == 'on') ? 'checked' : ''; ?> />
				Post
			</label>
			<br>
			<label>
				<input type="checkbox" class="chk_items" name="wpns_options[wpns_in_page]" <?php echo ($this->settings['wpns_in_page'] == 'on') ? 'checked' : ''; ?> />
				Page
			</label>
			<br>
			<!--label>
				<input type="checkbox" class="chk_items" name="wpns_options[wpns_in_category]" <?php echo ($this->settings['wpns_in_category'] == 'on') ? 'checked' : ''; ?> />
				Category
			</label>
			<br-->
			<label>
				<input type="checkbox" class="chk_items" name="wpns_options[wpns_in_custom_post_type]" <?php echo ($this->settings['wpns_in_custom_post_type'] == 'on') ? 'checked' : ''; ?> />
				Custom post type
			</label>
			<br>
		</fieldset>
		<?php
	}

	/**
	 * Draw the section header group 2
	 */
	public function wpns_section_2() {
		echo '<h3>Form Design</h3>';
	}

	/**
	 * Display and fill the field group 2
	 */
	public function wpns_setting_text() {
		// get option value from database
/*		$options = get_option( 'wpns_options' );
		var_dump($options);*/
		$text_string = $this->settings['wpns_placeholder'];
		// echo the field
		echo '<input type="text" id="wpns_placeholder" name="wpns_options[wpns_placeholder]" value="' . $text_string . '"/>';
	}

	/**
	 * Validation options callback function
	 * @param mix $input Holds values of option fields
	 *
	 * @return mix $valid
	 */
	public function wpns_validate_options( $input ) {

		$valid = array();
		$valid['wpns_placeholder'] = preg_replace( '/[^a-zA-Z]/', '', $input['wpns_placeholder'] );

		// checkbox value
		$valid['wpns_in_all'] = $input['wpns_in_all'];
		$valid['wpns_in_post'] = $input['wpns_in_post'];
		$valid['wpns_in_page'] = $input['wpns_in_page'];
		//$valid['wpns_in_category'] = $input['wpns_in_category'];
		$valid['wpns_in_custom_post_type'] = $input['wpns_in_custom_post_type'];

		return $valid;
	}

	/**
	 * Get option values from database
	 * @var string $name Holds option name
	 *
	 * @return array $options
	 */
	public function wpns_get_options( $name = '' ) {
		$options = get_option( $name );
		$this->settings = $options;
	}

} // end class WPNS_Admin

new WPNS_Admin;