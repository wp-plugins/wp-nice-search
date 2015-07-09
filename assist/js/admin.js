/**
 * Handle all action of user on html elements in plugin admin page
 */
 jQuery(document).ready(function($){
 	var $ = jQuery;
 	$('#chk_all').click(function(){
 		if ($(this).is(':checked')) {
 			$('.chk_items').removeAttr('checked');
 		}
 	});

 	$('.chk_items').click(function(){
 		$('#chk_all').removeAttr('checked');
 	});
 });