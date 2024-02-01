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

    var previousScrollTop = 0;

    //fetchPosts();
    runFetchPosts();
    
    //fetchExcludePosts();

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
        var currentScrollTop = jQuery(this).scrollTop();
        var scrollHeight = jQuery(this)[0].scrollHeight;
        var clientHeight = jQuery(this).height();
        // Check if scrolled near the bottom (adjust the threshold as needed)
        if (currentScrollTop > previousScrollTop && currentScrollTop + clientHeight >= scrollHeight - 10) {
            // Scrolling down and near the bottom
            OnSelectScrollExPosts(jQuery('select[name="data[ex_posts][]"]'));
        }

        previousScrollTop = currentScrollTop;
    });

    jQuery('body').on('click', '.left-side-option.clone-button', function (e) {

        e.preventDefault();
        console.log("clone button clicked");

        var selectedOption = jQuery(this).parent();
        // Retrieve value and text of the selected option
        var selectedValue = selectedOption.val(); 

        // use String split method to split the string into an array

        var values = String(selectedValue).split("|");
        var id = values[0];
        var title = values[1];

        // add id in a hidden fields as a string #ex_posts_list
        var ex_posts_list = jQuery('#ex_posts_list').val();
        if(ex_posts_list == "") {
            ex_posts_list = id;
        } else {
            ex_posts_list = ex_posts_list + "," + id;
        }
        jQuery('#ex_posts_list').val(ex_posts_list);

        // Create a new option element with class "right-side-option remove-button"
        var clonedOption = jQuery('<option class="right-side-option remove-button" value="' + id + '">' + title + '- </option>');

        // Append the cloned option to the right-side select box
        jQuery('.right-side').append(clonedOption);

        // Disable the selected option on the left side
        jQuery('.button-id-' + id).prop('disabled', true);

        // Remove the clone button from the cloned option
        clonedOption.find('.clone-button').remove();
       
    });

    // Remove cloned option from the right-side select box
    jQuery('body').on('click', '.right-side-option.remove-button', function () {


        // get the current selected option
        var currentSelectedOption = jQuery(this);

        // get the id of the current selected option
        var selectedValue = currentSelectedOption.val();

        // remove id from hidden field
        var ex_posts_list = jQuery('#ex_posts_list').val();
        var ex_posts_list_array = ex_posts_list.split(",");
        var index = ex_posts_list_array.indexOf(selectedValue);
        if (index > -1) {
            ex_posts_list_array.splice(index, 1);
        }
        ex_posts_list = ex_posts_list_array.join(",");
        jQuery('#ex_posts_list').val(ex_posts_list);


        // Disable the selected option on the left side
        jQuery('.button-id-' + selectedValue).prop('disabled', false);

        console.log(selectedValue);

        // remove the selected option from the right-side select box
        currentSelectedOption.remove();

    });

    


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

    fetchPosts(currentPageNoPosts);

    // if ((optionLength == (currentPageNoExPosts * 100)) && (st > (totalheight - 500))) {
    //     fetchExcludePosts(currentPageNoExPosts);
    // }
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
    // var data = {
    //     action: 'hfcm-request',
    //     id: hfcm_localize.id,
    //     getPosts: true,
    //     postType: postType,
    //     taxonomy: taxonomy,
    //     s: searchQuery,
    //     page: page,
    //     security: hfcm_localize.security
    // };

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
