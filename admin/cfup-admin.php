<?php
//Creating menu page - Custom font admin options
add_action('admin_menu', 'cfup_admin_page');
function cfup_admin_page() {
	
	add_menu_page(__('Custom Font Uploader', 'cfup' ), __('Font Uploader', 'cfup'), 'manage_options', 'cfup-options', 'cfup_options_page', 'dashicons-editor-textcolor', 4 );

	add_submenu_page( 'cfup-options', __('Custom Font', 'cfup'), __('Custom Font', 'cfup'), 'manage_options', 'cfup-options');

	add_submenu_page( 'cfup-options', __('Google Font', 'cfup'), __('Google Font','cfup'), 'manage_options', 
		'cfup-google-options', 'cfup_google_options_page');
}
function cfup_options_page() {
	include 'cfup-customfont-settings.php';
}

function cfup_google_options_page() {
	require_once plugin_dir_path(dirname(__FILE__)).'/admin/cfup-googlefont-settings.php';
}
