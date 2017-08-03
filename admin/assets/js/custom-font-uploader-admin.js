jQuery(document).ready(function () {

    jQuery('.delete-font').live('click', function () {
        var del_key = jQuery(this).data().delete_font_key;
        var id = jQuery(this).data().fid;
        // alert(id+del_key);
        
        var data = {
            'action': 'delete_customfont',
            'del_key': del_key,
        };
        jQuery.post(cfu_ajax_object.ajax_url, data, function (response) {
            jQuery('#'+id).fadeOut("normal", function () {
                jQuery(this).remove();
            });
        });
    });

    jQuery(document).on('click', '.delete-googlefont', function () {
        var del_gkey = jQuery(this).data().delete_font_gkey;
        var gid = jQuery(this).data().fid;

        var data = {
            'action': 'delete_googlefont',
            'del_gkey': del_gkey,
        };
        jQuery.post(cfu_ajax_object.ajax_url, data, function (response) {
            jQuery('#'+gid).fadeOut("normal", function () {
                jQuery('#'+gid).remove();
            });
        });

    });

});

jQuery(document).ready(function () {

    jQuery("#googlefont-select").select2();

    jQuery("#googlefont-select").change(function () {
    var str = "";
    jQuery("#googlefont-select option:selected").each(function () {
        str += jQuery(this).text() + " ";
    });

    var href = 'https://fonts.googleapis.com/css?family=' + str;
    var cssLink = jQuery("<link rel= 'stylesheet' type='text/css' href='"+href+"'>");
    jQuery("head").append(cssLink);
    jQuery('.add_text').css('font-family', str);

    });
});
