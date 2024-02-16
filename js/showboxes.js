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
//jQuery('#loader').show();

var currentPageNoPosts = 1;
var currentPageNoExPosts = 1;

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

    // Initialize variables
    var searchQuery, postType, taxonomy, page = 1;
    var previousScrollTop = 0;

    
    // Initialize select2 for the post type 'page'
    var selectIdPage = 'lazy-load-page';
    var postTypePage = 'page';
    initializeDynamicSelect2(selectIdPage, ajaxurl, postTypePage, taxonomy, searchQuery);

    // Initialize select2 for the post type 'post'
    var selectIdPost = 'lazy-load-select';
    var postTypePost = 'post';
    initializeDynamicSelect2(selectIdPost, ajaxurl, postTypePost, taxonomy, searchQuery);

    function initializeDynamicSelect2(selectId, ajaxurl, postType, taxonomy, searchQuery) {
        jQuery('#' + selectId).select2({
            ajax: {
                type: 'POST',
                url: ajaxurl,
                data: function (params) {
                    var query = {
                        q: params.term,
                        page: params.page || 1,
                        per_page: 5, // Adjust per_page to the desired number of items
                        action: 'hfcm-request-example',
                        id: hfcm_localize.id,
                        getPosts: true,
                        postType: postType,
                        taxonomy: taxonomy,
                        s: searchQuery,
                        security: hfcm_localize.security,
                        runFetchPosts: true
                    };
    
                    // Query parameters will be ?q=[term]&page=[page]
                    return query;
                },
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    var selectize_result = data.selectize_posts;
                    return {
                        results: selectize_result.map(function (repo) {
                            return { id: repo.value, text: repo.text };
                        }),
                        pagination: {
                            more: selectize_result.length === 5
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateSelection: function (selectedRepo) {
                // Customize the appearance of the selected item
                return $('<span style="color: #2271B1;">').text(selectedRepo.text);
            }
        });
    }
    




    // hide loader
    jQuery('#loader').hide();


    jQuery('input[name="s"]').keyup(delay(function (e) {
        fetchPosts();
    }, 500));
    jQuery('input[name="ex_s"]').keyup(delay(function (e) {
        fetchExcludePosts();
    }, 500));

    $('#s_pages select, #s_categories select, #c_posttype select, #s_tags select').select2();

    jQuery('select[name="data[s_posts][]"]').scroll(function (event) {
        OnSelectScrollPosts(jQuery('select[name="data[s_posts][]"]'));
    });

    // jQuery('select[name="data[ex_posts][]"]').scroll(function (e) {
    //     var currentScrollTop = jQuery(this).scrollTop();
    //     var scrollHeight = jQuery(this)[0].scrollHeight;
    //     var clientHeight = jQuery(this).height();
    //     // Check if scrolled near the bottom (adjust the threshold as needed)
    //     if (currentScrollTop > previousScrollTop && currentScrollTop + clientHeight >= scrollHeight - 10) {
    //         // Scrolling down and near the bottom
    //         OnSelectScrollExPosts(jQuery('select[name="data[ex_posts][]"]'));
    //     }

    //     previousScrollTop = currentScrollTop;
    // });

     
});



function OnSelectScrollPosts(selectObj) {
    var st = jQuery(selectObj).scrollTop();
    var optionLength = selectObj.find("option").length;
    var optionHeight = selectObj.find("option").height()
    var totalheight = optionLength * optionHeight;
  
    if ((optionLength == (currentPageNoPosts * 100)) && (st > (totalheight - 500))) {
        fetchPosts(currentPageNoPosts);
    }
}
function OnSelectScrollExPosts(selectObj) {
    var st = jQuery(selectObj).scrollTop();
    var optionLength = selectObj.find("option").length;
    var optionHeight = selectObj.find("option").height()
    var totalheight = optionLength * optionHeight;

    //fetchPosts(currentPageNoPosts);
    //runFetchSelectize(currentPageNoPosts);
}

function runFetchPosts(page = 1) {
    if(page == 1) {
        currentPageNoPosts = 1;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();
    var disabledOptions = [];
    jQuery('select[name="excluded_posts"] option').each(function() {
        disabledOptions.push(jQuery(this).val());
    });
    var data = {
        action: 'hfcm-request-example',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security,
        runFetchPosts: true
    };
   
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        async:false,
        success: function (postData) {
            console.log("success");
            console.log(postData);
            var options = {
                plugins: ['remove_button'],
                options: postData.posts,
                items: postData.selected,
            };
            jQuery('#loader').hide();

            
            if(page == 1) {
                jQuery('select[name="data[ex_posts][]"]').html(postData.posts);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(postData.posts);
            }
            jQuery('#loader').hide();
            currentPageNoPosts += 1;
        }
    });
}   

function runFetchSelectize(page = 1) {
    if(page == 1) {
        currentPageNoPosts = 1;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();

    // Initial request for the first 5 repositories
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            q: '',
            page: page,
            per_page: 5, // Adjust per_page to the desired number of items
            action: 'hfcm-request-example',
            id: hfcm_localize.id,
            getPosts: true,
            postType: postType,
            taxonomy: taxonomy,
            s: searchQuery,
            security: hfcm_localize.security,
            runFetchPosts: true
        },
        dataType: 'json',
        success: function (data) {
            var selectize_result = data.selectize_posts;
            // Process the initial results and populate the select2 dropdown
            jQuery('#lazy-load-select').select2({
                data: selectize_result.map(function (repo) {
                    return { id: repo.value, text: repo.text };
                })
            });
            jQuery('#loader').hide();
            currentPageNoPosts += 1;
        }
    });
}




function fetchPosts(page = 1) {

    if(page == 1) {
        currentPageNoPosts = 1;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();
    var disabledOptions = [];
    jQuery('select[name="excluded_posts"] option').each(function() {
        disabledOptions.push(jQuery(this).val());
    });
    var data = {
        action: 'hfcm-request-example',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security,
        disabledOptions: disabledOptions
    };


    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        async:false,
        success: function (postData) {
            console.log("success");
            console.log(postData);
            var options = {
                plugins: ['remove_button'],
                options: postData.posts,
                items: postData.selected,
            };
            jQuery('#loader').hide();

            
            if(page == 1) {
                jQuery('select[name="data[ex_posts][]"]').html(postData.posts);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(postData.posts);
            }
            jQuery('#loader').hide();
            currentPageNoPosts += 1;
        }
    });
}
function fetchExcludePosts(page = 1) {

    currentPageNoPosts = 1;
    currentPageNoExPosts = 1;


    if(page == 1) {
        currentPageNoExPosts = 1;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="ex_s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();
    var disabledOptions = [];
    jQuery('select[name="excluded_posts"] option').each(function() {
        disabledOptions.push(jQuery(this).val());
    });		
   	
    var data = {
        action: 'hfcm-request-example',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security,
        disabledOptions: disabledOptions
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        async:false,
        success: function (postData) {

            // make empty the select box
            jQuery('select[name="data[ex_posts][]"]').empty();

            var options = {
                plugins: ['remove_button'],
                options: postData.posts,
                items: postData.selected,
            };
            jQuery('#loader').hide();
            if(page == 1) {
                jQuery('select[name="data[ex_posts][]"]').html(postData.posts);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(postData.posts);
            }
            jQuery('#loader').hide();
            currentPageNoExPosts += 1;
        }
    });
}
