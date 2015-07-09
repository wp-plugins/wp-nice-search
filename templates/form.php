<div id="wpns-search-wrap">
	<form id="wpns_search_form" method="POST" action="#">
		<div class="wpns-input-box">
			<input type="text" id="wpns_search_input" name="wpns_search" autocomplete="off" placeholder="<?php echo $settings['wpns_placeholder']; ?>">
			<i id="wpns_search_icon" class="fa fa-search"></i>
			<img style="display: none;" id="wpns_loading_search" src="<?php echo WPNS_URL . 'assist/images/loading.gif'; ?>" />
		</div>
	</form>
	<div id="wpns_results_box" style="display: none;">
		<h4>Search results: </h4>
	</div>
</div>