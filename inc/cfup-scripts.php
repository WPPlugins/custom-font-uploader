<?php 

/**************************** Enqueue JS Scripts ****************************************/

/**
 * Register the JavaScript for the frontend area.
 *
 * @since    1.0.0
 */

add_action('wp_enqueue_scripts', 'cfup_enqueue_custom_fonts');
function cfup_enqueue_custom_fonts(){
    wp_enqueue_style( 'cfup-uploaded_fonts-css', CUSTOM_FONT_UPLOADER_PLUGIN_URL.'assets/css/uploaded_fonts.css' );
 } 

 add_action('wp_enqueue_scripts', 'cfup_enqueue_google_fonts');
function cfup_enqueue_google_fonts(){
    wp_enqueue_style( 'cfup-googlefonts-css', CUSTOM_FONT_UPLOADER_PLUGIN_URL.'assets/css/googlefonts.css' );
 }



/**************************************************************************/

add_action( 'wp_enqueue_scripts', 'cfup_google_fonts_enqueue' );

function cfup_google_fonts_enqueue() {

  $cfup_google_fonts_options = get_option( 'googlefont_file_name' );
  $cfup_google_fonts = json_decode($cfup_google_fonts_options);


    // Google api url
  $googleapis_url = 'http://fonts.googleapis.com/css?family=';

     // Check if ssl is activated and switch to https
  if ( is_ssl() ) {
    $googleapis_url = str_replace( 'http:', 'https:', $googleapis_url );
  }

  // Enquire only the selected fonts
  if ( isset( $cfup_google_fonts ) ) {

    foreach ( $cfup_google_fonts as $cfup_google_font_key => $cfup_google_font ) {

      wp_register_style( 'font-style-' . $cfup_google_font_key,  $googleapis_url . $cfup_google_font_key );
      wp_enqueue_style( 'font-style-' . $cfup_google_font_key );
    }

  }

}


/**************************** Enqueue JS Scripts ****************************************/

/**
 * Register the JavaScript for the admin area.
 *
 * @since    1.0.0
 */

add_action( 'admin_enqueue_scripts', 'cfup_enqueue_custom_styles' );
function cfup_enqueue_custom_styles() {
    
    wp_enqueue_style( 'cfup-cfup-css', CUSTOM_FONT_UPLOADER_PLUGIN_URL.'assets/css/cfup.css' );
}

add_action( 'admin_enqueue_scripts', 'cfup_enqueue_select2css' );
function cfup_enqueue_select2css() {

  wp_enqueue_style( 'cfup-select2css', CUSTOM_FONT_UPLOADER_PLUGIN_URL.'admin/assets/css/select2.css');
}

add_action( 'admin_enqueue_scripts', 'cfup_enqueue_select2js' );
function cfup_enqueue_select2js() {

  wp_enqueue_script( 'cfup-select2js', CUSTOM_FONT_UPLOADER_PLUGIN_URL.'admin/assets/js/select2.js' );
}




add_action('admin_enqueue_scripts', 'enqueue_scripts');
function enqueue_scripts() {

		wp_enqueue_script('custom-font-uploader-admin', plugins_url( 'admin\assets\js\custom-font-uploader-admin.js' , dirname(__FILE__ )) , array('jquery'), '1.0.0', false);

		wp_localize_script('custom-font-uploader-admin', 'cfu_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

  //enqueue fonts
add_action( 'wp_head', 'cfup_custom_fonts_enqueue' );
function cfup_custom_fonts_enqueue() {
   $cfup_custom_fonts_options = get_option( 'font_file_name' );
   if( !empty( $cfup_custom_fonts_options ) ) {
      $cfup_custom_fonts_options = json_decode($cfup_custom_fonts_options);
      $custom_css = '';
      $custom_css .= '<style type="text/css" id="custom_font">';
      foreach ($cfup_custom_fonts_options as  $custom_fontname => $cfup_custom_font) {

          $file_path = CUSTOM_FONT_UPLOADER_PLUGIN_PATH . 'assets/css/uploaded_fonts.css';
          $css = '@font-face {';
          $css .= "\n";
          $css .= '   font-family: ' . $custom_fontname . ';';
          $css .= "\n";
          $css .= '   src: url(' . CUSTOM_FONT_UPLOADER_UPLOADS_DIR_URL . $cfup_custom_font . ');';
          $css .= "\n";

          $css .= '   font-weight: normal;';
          $css .= "\n";
          $css .= '}';

         $custom_css .= $css;   
      }

      $custom_css .= '</style>';

      echo $custom_css;
   }
}