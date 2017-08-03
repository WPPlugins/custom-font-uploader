<?php 

//Create upload folder
add_action('init', 'cfup_create_folder');
function cfup_create_folder() {
	$cfup_upload = wp_upload_dir();
	$cfup_upload_dir = $cfup_upload['basedir'];
	$cfup_upload_dir = $cfup_upload_dir . '/wbcom_fonts/fonts/';
	if (!file_exists($cfup_upload_dir)) {
		mkdir( $cfup_upload_dir, 0755, true );
	}
}

//Funtion for deleting fonts using upload method

add_action('wp_ajax_delete_customfont', 'delete_customfont');
add_action('wp_ajax_nopriv_delete_customfont', 'delete_customfont');

function delete_customfont() {
    $fontsDBData = get_option('font_file_name', true);
    $fontsData = json_decode($fontsDBData, true);
    $delckey = sanitize_text_field( $_POST['del_key'] );
    $key_to_delete = isset($delckey) ? $delckey : '';

    $f_path = isset($_FILES['font_file']['name']) ? $_FILES['font_file']['name'] : '';
    $font_ext = basename($f_path);
    $dir = ABSPATH . 'wp-content/uploads/wbcom_fonts/fonts';
    $upload_dir = $dir . "/" . basename($f_path);
    unlink(realpath($upload_dir . $fontsData[$key_to_delete]));
    unset($fontsData[$key_to_delete]);
    $updateData = update_option('font_file_name', json_encode($fontsData));
    $fontUploadFinalStatus = 'updated';
    $fontUploadFinalMsg = 'Font Deleted';
    die();
}

//Function for deleting fonts using google fonts
add_action('wp_ajax_delete_googlefont', 'delete_googlefont');
add_action('wp_ajax_nopriv_delete_googlefont', 'delete_googlefont');

function delete_googlefont() {
    $gfontsDBData = get_option('googlefont_file_name', true);
    $gfontsdata = json_decode($gfontsDBData, true);
    $del_gkey = sanitize_text_field( $_POST['del_gkey'] );
    $gkey_to_delete = isset($del_gkey) ? $del_gkey: '';
    unset($gfontsdata[$gkey_to_delete]);
    $updatedbdata = update_option('googlefont_file_name', json_encode($gfontsdata));
    $gfontUploadFinalStatus = 'updated';
    $gfontUploadFinalMsg = 'Font Deleted';
    die();
}

// Get google fonts through google api and pass it in curl

function wbcom_get_fonts() {
    $api_key = 'AIzaSyCA0cvCET3ICQWkmMF8lM9NFN7DwL3WPvY';
    $api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

    // Collect the args
    $params = array(
        'key' => sanitize_text_field($api_key)
    );

    // Generate the URL
    $url = add_query_arg($params, esc_url_raw($api_url));
    // Make API request
    $response = wp_remote_get(esc_url_raw($url));

    // Check the response code
    $response_code = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);
    if (is_wp_error($response)) {
        return curl($url);
    }
    if (200 != $response_code && !empty($response_message)) {
        return new WP_Error($response_code, $response_message);
    } elseif (200 != $response_code) {
        return new WP_Error($response_code, 'Unknown error occurred');
    } else {
        return wp_remote_retrieve_body($response);
    }
}

function curl($url) {
    $curl = curl_init(esc_url_raw($url));

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, '');
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($curl);
    if (0 !== curl_errno($curl) || 200 !== curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
        $response = null;
    } // end if
    curl_close($curl);

    return $response;
}
