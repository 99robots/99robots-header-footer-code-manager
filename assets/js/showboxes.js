// function to show dependent dropdowns for "Site Display" field.
function hfcm_showotherboxes(type) {
	var header = '<option value="header">'+ hfcm_localize.header +'</option>',
		before_content = '<option value="before_content">'+ hfcm_localize.before_content +'</option>',
		after_content = '<option value="after_content">'+ hfcm_localize.after_content +'</option>',
		footer = '<option value="footer">'+ hfcm_localize.footer +'</option>',
		all_options = header + before_content + after_content + footer;
	
	if (type == 's_pages') {
		jQuery('#s_pages, #locationtr').show();
		jQuery('#data_location').html( all_options );
		jQuery('#s_categories, #s_tags, #c_posttype, #lp_count, #s_posts').hide();
	} else if (type == 's_posts') {
		jQuery('#s_posts, #locationtr').show();
		jQuery('#data_location').html( all_options );
		jQuery('#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count').hide();
	} else if (type == 's_categories') {
		jQuery('#s_categories, #locationtr').show();
		jQuery('#data_location').html( header + footer );
		jQuery('#s_pages, #s_tags, #c_posttype, #lp_count, #s_posts').hide();
	} else if (type == 's_custom_posts') {
		jQuery('#c_posttype, #locationtr').show();
		jQuery('#data_location').html( all_options );
		jQuery('#s_categories, #s_tags, #s_pages, #lp_count, #s_posts').hide();
	} else if (type == 's_tags') {
		jQuery('#data_location').html( all_options );
		jQuery('#s_tags, #locationtr').show();
		jQuery('#s_categories, #s_pages, #c_posttype, #lp_count, #s_posts').hide();
	} else if (type == 'latest_posts') {
		jQuery('#data_location').html( header + footer );
		jQuery('#s_pages, #s_categories, #s_tags, #c_posttype, #s_posts').hide();
		jQuery('#lp_count, #locationtr').show();
	} else if (type == 'manual') {
		jQuery('#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #locationtr, #s_posts').hide();
	} else {
		jQuery('#data_location').html( header + footer);
		jQuery('#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #s_posts').hide();
		jQuery('#locationtr').show();
	} 
}