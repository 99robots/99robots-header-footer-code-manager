jQuery('.nnr-switch input').click( function()  {
	var t = jQuery( this ),
		togvalue = t.is( ':checked' ) ? 'on' : 'off',
		scriptid = t.data( 'id' );
										
	jQuery.ajax({
		url: hfcm_ajax.url, 
		data: {
			page: 'hfcm-request-handler',
			toggle: true,
			id: scriptid,
			togvalue: togvalue,
			security: hfcm_ajax.security
		}
	});
});