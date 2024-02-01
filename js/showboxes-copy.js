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

    fetchPosts();
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

        // Disable the selected option on the left side
        jQuery('.button-id-' + selectedValue).prop('disabled', false);

        console.log(selectedValue);

        // remove the selected option from the right-side select box
        currentSelectedOption.remove();

        return;



        var clonedOption = $(this).parent(); // Get the <option> element
        var originalOption = clonedOption.data('original'); // Get the original option

        // get the value of the current selected option
        var selectedValue = clonedOption.val();

        // Enable the original option on the left side
        jQuery('.left-side').find('option[value="' + originalOption.val() + '"]').prop('disabled', false);

        // Remove the cloned option from the right-side select box
        clonedOption.remove();
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

function fetchPosts(page = 1) {

    if(page == 1) {
        currentPageNoPosts = 1;
    }
    jQuery('#loader').show();
    var searchQuery = jQuery('input[name="s"]').val();
    var postType = jQuery('select[name="ex_filter_post_type"]').val();
    var taxonomy = jQuery('select[name="ex_filter_taxonomy"]').val();
    var data = {
        action: 'hfcm-request-example',
        id: hfcm_localize.id,
        getPosts: true,
        postType: postType,
        taxonomy: taxonomy,
        s: searchQuery,
        page: page,
        security: hfcm_localize.security
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

            // get all values from <select class="nnr-wraptext right-side" name="excluded_posts" multiple>
            var excludedPosts = jQuery('select[name="excluded_posts"]').val();
            // print all values of each options exists in <select class="nnr-wraptext right-side" name="excluded_posts" multiple>
            
            var disabledOptions = [];
            jQuery('select[name="excluded_posts"] option').each(function() {
                console.log("Each Option Value");
                console.log(jQuery(this).val());

                disabledOptions.push(jQuery(this).val());
            });		
            console.log("disabledOptions");
            console.log(disabledOptions);	

            // Check and modify HTML content based on disabledOptions
if (Array.isArray(postData.posts)) {
    postData.posts = postData.posts.map(function (value) {
        var postID = value.value.split('|')[0];

        // Check if the postID is in disabledOptions
        if (disabledOptions.indexOf(postID) > -1) {
            console.log("Found disabled post ID: " + postID);

            // Optionally, you can also add a class to the option for styling purposes
            return {
                value: value.value,
                text: value.text + ' + ', // Modify text content
                disabled: true
            };
        } else {
            return value;
        }
    });
}

// Update the HTML content dynamically
var selectOptions = '';
jQuery(postData.posts).each(function (index, value) {
    selectOptions += '<option class="left-side-option clone-button button-id-' + value.value.split('|')[0] + '" value="' + value.value + '"';
    
    console.log("value.disabled");
    console.log(value);
    console.log(value.disabled);
    
    if (value.disabled) {
        selectOptions += ' disabled';
    }
    selectOptions += '>' + value.text + '</option>';
});


        
            if(page == 1) {
                jQuery('select[name="data[ex_posts][]"]').html(selectOptions);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(selectOptions);
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
        console.log("Each Option Value");
        console.log(jQuery(this).val());

        disabledOptions.push(jQuery(this).val());
    });		
    console.log("disabledOptions");
    console.log(disabledOptions);	


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


            

            // Check and modify HTML content based on disabledOptions
            if (Array.isArray(postData.posts)) {
                postData.posts = postData.posts.map(function (value) {
                    var postID = value.value.split('|')[0];

                    // Check if the postID is in disabledOptions
                    if (disabledOptions.indexOf(postID) > -1) {
                        console.log("Found disabled post ID: " + postID);

                        // Optionally, you can also add a class to the option for styling purposes
                        return {
                            value: value.value,
                            text: value.text + ' + ', // Modify text content
                            disabled: true
                        };
                    } else {
                        return value;
                    }
                });
            }

            // Update the HTML content dynamically
            var selectOptions = '';
            jQuery(postData.posts).each(function (index, value) {
                selectOptions += '<option class="left-side-option clone-button button-id-' + value.value.split('|')[0] + '" value="' + value.value + '"';
                
                console.log("value.disabled");
                console.log(value);
                console.log(value.disabled);
                
                // checl value.value.split('|')[0] is in disabledOptions array

                // Function to check if a value is present in the array
                var isValuePresent = (element) => element === value.value.split('|')[0];

                // Using find to check if a value is present
                var foundValue = disabledOptions.find(isValuePresent);


                if (foundValue !== undefined) {
                    selectOptions += ' disabled';
                  } 

                // if (value.disabled) {
                //     selectOptions += ' disabled';
                // }
                selectOptions += '>' + value.text + '</option>';
            });





            if(page == 1) {
                jQuery('select[name="data[ex_posts][]"]').html(selectOptions);
            } else {
                jQuery('select[name="data[ex_posts][]"]').append(selectOptions);
            }
            jQuery('#loader').hide();
            currentPageNoExPosts += 1;
        }
    });
}
