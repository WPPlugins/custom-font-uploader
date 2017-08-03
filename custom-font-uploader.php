<?php
/**
 * Plugin Name: Custom Font Uploader
 * Plugin URI: https://wbcomdesigns.com/
 * Description: This plugin by Wbcom Designs allows you to upload custom font-family using browse and upload as well as using google font-family and have it enqueued in your site.
 * Version: 1.0.0
 * Author: Wbcom Designs
 * Author URI: http://wbcomdesigns.com
 * Text Domain: cfup
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

//Defining constants
$uploads_dir = wp_upload_dir();
$cons = array(
    'CUSTOM_FONT_UPLOADER_PLUGIN_PATH' => plugin_dir_path(__FILE__),
    'CUSTOM_FONT_UPLOADER_PLUGIN_URL' => plugin_dir_url(__FILE__),
    'CUSTOM_FONT_UPLOADER_UPLOADS_DIR_URL' => $uploads_dir['baseurl'] . '/wbcom_fonts/fonts/',
);
foreach ($cons as $con => $value) {
    define($con, $value);
}

//Include needed files
$include_files = array(
    'inc/cfup-functions.php',
    'inc/cfup-scripts.php',
    'admin/cfup-admin.php',
    'webfonts.php',
);
foreach ($include_files as $include_file) {
    require_once plugin_dir_path(__FILE__).$include_file;
}

add_action( 'init', 'cfup_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function cfup_load_textdomain() {

    $locale = apply_filters( 'plugin_locale', get_locale(), 'cfup' );
    load_textdomain( 'cfup', 'languages/cfup'.'-' . $locale . '.mo' );
    load_plugin_textdomain( 'cfup', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}


//Custom Font Uploader Plugin Activation
register_activation_hook(__FILE__, 'custom_font_plugin_activation_check');

function custom_font_plugin_activation_check() {
    $response = wbcom_get_fonts();
    update_option('cfupgooglefonts_data', $response);
}

//Custom Font Uploader Plugin Deactivation
register_deactivation_hook(__FILE__, 'custom_font_plugin_deactivation_check');

function custom_font_plugin_deactivation_check() {
    
}

//Plugin uninstall Hook
register_uninstall_hook(__FILE__, 'custom_font_plugin_uninstall_hook');

function custom_font_plugin_uninstall_hook() {
  // if uninstall hook is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN') && !defined( 'ABSPATH') ) {
    die;
}

$option_name1 = 'custom_font_data';
$option_name2 = 'cfupgooglefonts_data';
$option_name3 = 'font_file_name';
$option_name4 = 'googlefont_file_name';
 
delete_option($option_name1);
delete_option($option_name2);
delete_option($option_name3);
delete_option($option_name4);
}


//Settings link for custom font panel
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'custom_font_admin_page_link');

function custom_font_admin_page_link($links) {
    $page_link = array('<a href="' . admin_url('admin.php?page=cfup-options') . '">Settings</a>');
    return array_merge($links, $page_link);
}