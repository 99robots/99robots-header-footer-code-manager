// Toggle switch
jQuery('.nnr-switch input').on('click', function() {
	var t = jQuery( this ),
		togvalue = t.is( ':checked' ) ? 'on' : 'off',
		scriptid = t.data( 'id' ),
		data = {
			action: 'hfcm-request',
			toggle: true,
			id: scriptid,
			togvalue: togvalue,
			security: hfcm_ajax.security
		};

	jQuery.post(
		ajaxurl,
		data
	);
});

// Delete confirmation
jQuery('.snippets .delete > a').on('click', function() {
	var name = jQuery(this).parents('.name').find('> strong').text();
	return confirm( 'Snippet name: ' + name + '\n\nAre you sure you want to delete this snippet?');
});