// function to show dependent dropdowns for "Site Display" field.

function hfcm_showotherboxes(type) {
    var header = '<option value="header">' + hfcm_localize.header + '</option>',
        before_content = '<option value="before_content">' + hfcm_localize.before_content + '</option>',
        after_content = '<option value="after_content">' + hfcm_localize.after_content + '</option>',
        footer = '<option value="footer">' + hfcm_localize.footer + '</option>',
        all_options = header + before_content + after_content + footer;

    if (type == 'All') {
        jQuery('#ex_pages, #ex_posts,  #locationtr').show();
        hfcm_remember_loc(all_options);
        jQuery('#s_categories, #s_pages, #s_tags, #c_posttype, #lp_count, #s_posts').hide();
    } else if (type == 's_pages') {
        jQuery('#s_pages, #locationtr').show();
        hfcm_remember_loc(all_options);
        jQuery('#s_categories, #s_tags, #ex_pages, #ex_posts,  #c_posttype, #lp_count, #s_posts').hide();
    } else if (type == 's_posts') {
        jQuery('#s_posts, #locationtr').show();
        hfcm_remember_loc(all_options);
        jQuery('#s_pages, #s_categories, #ex_pages, #ex_posts,  #s_tags, #c_posttype, #lp_count').hide();
    } else if (type == 's_categories') {
        jQuery('#s_categories, #locationtr').show();
        hfcm_remember_loc(all_options);
        jQuery('#s_pages, #s_tags, #c_posttype, #ex_pages, #ex_posts,  #lp_count, #s_posts').hide();
    } else if (type == 's_custom_posts') {
        jQuery('#c_posttype, #locationtr').show();
        hfcm_remember_loc(all_options);
        jQuery('#s_categories, #s_tags, #s_pages, #ex_pages, #ex_posts,  #lp_count, #s_posts').hide();
    } else if (type == 's_tags') {
        hfcm_remember_loc(all_options);
        jQuery('#s_tags, #locationtr').show();
        jQuery('#s_categories, #s_pages, #c_posttype, #ex_pages, #ex_posts,  #lp_count, #s_posts').hide();
    } else if (type == 'latest_posts') {
        hfcm_remember_loc(all_options);
        jQuery('#s_pages, #s_categories, #s_tags, #ex_pages, #ex_posts,  #c_posttype, #s_posts').hide();
        jQuery('#lp_count, #locationtr').show();
    } else if (type == 'manual') {
        jQuery('#s_pages, #s_categories, #s_tags,#ex_pages, #ex_posts,  #c_posttype, #lp_count, #locationtr, #s_posts').hide();
    } else {
        hfcm_remember_loc(header + footer);
        jQuery('#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #s_posts').hide();
        jQuery('#locationtr').show();
    }
}

function hfcm_remember_loc(new_html) {
    var tmp = jQuery('#data_location option:selected').val();
    jQuery('#data_location').html(new_html);
    jQuery('#data_location option[value="' + tmp + '"]').prop('selected', true);
}

// init selectize.js
jQuery('#loader').show();

var currentPageNoPosts = 0;
var currentPageNoExPosts = 0;

function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

jQuery(function ($) {
    fetchPosts();
    fetchExcludePosts();

    jQuery('input[name="s"]').keyup(delay(function (e) {
        fetchPosts();
    }, 500));
    jQuery('input[name="ex_s"]').keyup(delay(function (e) {
        fetchExcludePosts();
    }, 500));

    $('#s_pages select, #s_categories select, #c_posttype select, #s_tags select, #ex_pages select').selectize();

    jQuery('select[name="data[s_posts][]"]').scroll(function (event) {
        OnSelectScrollPosts(jQuery('select[name="data[s_posts][]"]'));
    });
    jQuery('select[name="data[ex_posts][]"]').scroll(function (e) {
        OnSelectScrollExPosts(jQuery('select[name="data[ex_posts][]"]'));
    });
});

function OnSelectScrollPosts(selectObj) {
    var st = jQuery(selectObj).scrollTop();
    var optionLength = selectObj.find("option").length;
    var optionHeight = selectObj.find("option").height()
    var totalheight = optionLength * optionHeight;
    console.log(totalheight, st, optionLength);
    if ((optionLength == (currentPageNoPosts * 100)) && (st > (totalheight - 500))) {
        fetchPosts(currentPageNoPosts);
    }
}
function OnSelectScrollExPosts(selectObj) {
    var st = jQuery(selectObj).scrollTop();
    var optionLength = selectObj.find("option").length;
    var optionHeight = selectObj.find("option").height()
    var totalheight = optionLength * optionHeight;

    if ((optionLength == (currentPageNoExPosts * 100)) && (st > (totalheight - 500))) {
        fetchExcludePosts(currentPageNoExPosts);
    }
}

function fetchPosts(page = 0) {
    if(page == 0) {
        currentPageNoPosts = 0;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="s"]').val();
    var postType = jQuery('select[name="filter_post_type"]').val();
    var taxonomy = jQuery('select[name="filter_taxonomy"]').val();
    var data = {
        action: 'hfcm-request',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        async:false,
        success: function (postData) {
            var options = {
                plugins: ['remove_button'],
                options: postData.posts,
                items: postData.selected,
            };
            jQuery('#loader').hide();
            if(page == 0) {
                jQuery('select[name="data[s_posts][]"]').html(postData.posts);
            } else {
                jQuery('select[name="data[s_posts][]"]').append(postData.posts);
            }
            jQuery('#loader').hide();
            currentPageNoPosts += 1;
        }
    });
}
function fetchExcludePosts(page = 0) {
    if(page == 0) {
        currentPageNoExPosts = 0;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="ex_s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();
    var data = {
        action: 'hfcm-request',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        async:false,
        success: function (postData) {
            var options = {
                plugins: ['remove_button'],
                options: postData.posts,
                items: postData.selected,
            };
            jQuery('#loader').hide();
            if(page == 0) {
                jQuery('select[name="data[ex_posts][]"]').html(postData.posts);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(postData.posts);
            }
            jQuery('#loader').hide();
            currentPageNoExPosts += 1;
        }
    });
}
