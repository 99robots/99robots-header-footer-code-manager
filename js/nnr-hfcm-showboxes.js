// function to show dependent dropdowns for "Site Display" field.

function hfcm_showotherboxes(type)
{
    var header = '<option value="header">' + hfcm_localize.header + '</option>',
        before_content = '<option value="before_content">' + hfcm_localize.before_content + '</option>',
        after_content = '<option value="after_content">' + hfcm_localize.after_content + '</option>',
        footer = '<option value="footer">' + hfcm_localize.footer + '</option>',
        all_options = header + before_content + after_content + footer;

    if (type == 'All') {
        jQuery('#ex_pages, #ex_posts,  #locationtr').show();
        hfcm_remember_loc(header + footer);
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

function hfcm_remember_loc(new_html)
{
    var tmp = jQuery('#data_location option:selected').val();
    jQuery('#data_location').html(new_html);
    jQuery('#data_location option[value="' + tmp + '"]').prop('selected', true);
}

function hfcmCopyToClipboard(elem)
{
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;

    // must use a temporary form element for the selection and copy
    target = document.getElementById(targetId);
    if (!target) {
        var target = document.createElement("textarea");
        target.style.position = "absolute";
        target.style.left = "-9999px";
        target.style.top = "0";
        target.id = targetId;
        document.body.appendChild(target);
    }
    target.textContent = elem.getAttribute('data-shortcode');
    elem.textContent = "Copied!";

    setTimeout(
        function () {
            elem.textContent = "Copy";
        }, 2000
    );
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    target.remove();
    return succeed;
}

function nnr_confirm_delete_snippet()
{
    return confirm("Are you sure you want to delete this snippet?");
}

// init selectize.js
jQuery('#loader').show();
jQuery(
    function ($) {

        // var nnr_hfcm_data = {
        //     action: 'hfcm-request',
        //     id: hfcm_localize.id,
        //     get_posts: true,
        //     security: hfcm_localize.security
        // };

        // $.post(
        //     ajaxurl,
        //     nnr_hfcm_data,
        //     function (new_data) {
        //         var all_posts = $.merge([{text: "", value:""}], new_data.posts);
        //         var options = {
        //             plugins: ['remove_button'],
        //             options: all_posts,
        //             items: new_data.selected
        //         };
        //         $('#loader').hide();
        //         $('#s_posts select').selectize(options);
        //         var options = {
        //             plugins: ['remove_button'],
        //             options: new_data.posts,
        //             items: new_data.excluded
        //         };
        //         $('#loader').hide();
        //         $('#ex_posts select').selectize(options);
        //     },
        //     'json', // ajax result format
        // );

        // selectize all <select multiple> elements
        // $('#s_pages select, #s_categories select, #c_posttype select, #s_tags select, #ex_pages select').selectize(
        //     {
        //         plugins: ['remove_button']
        //     }
        // );

        // Initialize the CodeMirror editor
        if ($('#nnr_newcontent').length) {
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    //mode: 'javascript',
                }
            );
            var editor = wp.codeEditor.initialize($('#nnr_newcontent'), editorSettings);
        }


        var copyButton = document.getElementById("hfcm_copy_shortcode");
        if (copyButton) {
            copyButton.addEventListener("click", function () {
                hfcmCopyToClipboard(copyButton);
            });
        }

     
        // Initialize variables
        var searchQuery, postType, taxonomy, page = 1;
        var previousScrollTop = 0;


        // Initialize select2 for the post type 'post'
        var selectIdPage = 'lazy-load-s-posts';
        var postTypePage = 'post';
        initializeDynamicSelect2(selectIdPage, ajaxurl, postTypePage, taxonomy, searchQuery);

        // Initialize select2 for the post type 'page'
        var selectIdPage = 'lazy-load-s-pages';
        var postTypePage = 'page';
        initializeDynamicSelect2(selectIdPage, ajaxurl, postTypePage, taxonomy, searchQuery);



        // Initialize select2 for the post type 'page'
        var selectIdPage = 'lazy-load-page';
        var postTypePage = 'page';
        initializeDynamicSelect2(selectIdPage, ajaxurl, postTypePage, taxonomy, searchQuery);

        // Initialize select2 for the post type 'post'
        var selectIdPost = 'lazy-load-post';
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


    }
);
