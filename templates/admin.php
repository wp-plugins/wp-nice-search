<div class="wrap">
	<h2>WP NICE SEARCH SETTINGS <span class="dashicons dashicons-admin-settings"></span></h2>
	<form method="POST" action="options.php">
		<?php 
			settings_fields( 'wpns_options' );
			do_settings_sections( $this->menu_slug );
		?>
		<p class="submit">
			<input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
		</p>
	</form>
	<style type="text/css">
		.dashicons-admin-settings { font-size: 23px; line-height: 29px; }
	</style>
</div>