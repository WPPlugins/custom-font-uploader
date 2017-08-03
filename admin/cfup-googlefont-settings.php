<?php

// ***************settings for enqueuing font with google fonts approach***********************

$optionsName = 'googlefonts_options';

/**
 * @var string $localizationDomain Domain used for localization
 */
$localizationDomain = "googlefonts";

/**
 * @var array $options Stores the options for this plugin
 */
$options = array();

$cfupgf_data_option_name = "cfupgooglefonts_data";

$cfupgf_fonts_file = 'webfonts.php';

$cfupgf_notices = array();

$cfupgf_filename = 'cfup-general-settings.php';
$cfupgf_fonts = get_option('cfupgooglefonts_data');
if (empty($cfupgf_fonts)) {
    // Make request
    $response = wbcom_get_fonts();
    // Print error if error, otherwise print information
//            if (is_wp_error($response)) {
//                echo 'The following error occurred when contacting font api: ' . wp_strip_all_tags($response->get_error_message());
//            } else {
        update_option('cfupgooglefonts_data', $response);
    //}
} else {
    $cfupgf_fonts = json_decode($cfupgf_fonts, true);
}

$custom_font_data = array();
if (!empty($cfupgf_fonts)) {
    if (isset($cfupgf_fonts['items'])) {
        foreach ($cfupgf_fonts['items'] as $key => $cfupgf_font) { //saving font-family in DB with name of font-family 
            $custom_font_data[$cfupgf_font['family']] = array(
                'font-family' => $cfupgf_font['family'],
                'font-file' => $cfupgf_font['files']
            );
        }
    }
}
update_option('custom_font_data', json_encode($custom_font_data));
$get_custom_font_data = get_option('custom_font_data', true);


if (isset($_POST['submit-google-fonts']) && wp_verify_nonce($_POST['google-fonts-nonce'], 'cfup-googlefont')) {

            $font = sanitize_text_field($_POST['font']);

            $option = isset($font) ? $font : false;
            if ($option) {

                echo "<div id='message' class='gfupdate'> The font you have selected :" . $font . ' has been uploaded and enqueued on to your site</div>';  // Displaying Selected Value
                echo "<input id ='file' type='hidden' value = '".$custom_font_data[$font]['font-file']['regular']."'>";
                 echo "<input id ='file-name' type='hidden' value = '".$custom_font_data[$font]['font-family']."'>";
            }

            // Save details in DB

        $gfonts = get_option('googlefont_file_name');

        if (empty($gfonts)) {
            $gfonts[$custom_font_data[$font]['font-family']] = $custom_font_data[$font]['font-file']['regular'];
            update_option('googlefont_file_name', json_encode($gfonts));
        } else {
            $gfonts = json_decode($gfonts, true);
            $gfonts[$custom_font_data[$font]['font-family']] = $custom_font_data[$font]['font-file']['regular'];
            update_option('googlefont_file_name', json_encode($gfonts));
        }

} else {?>

                <?php echo '<div class="update-nag notice"><p>'
  . sprintf(__('Note: please select font to enqueue it in your site.', 'cfup'))
  . '</p></div>';?>

      <?php  } ?>
      
<div id="wpbody" role="main">
	<div id="wpbody-content" aria-label="Main content" tabindex="0">
	<div class="wrap"> 
            <table class="googletbl" width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>

                        <h1><?php _e('Control Panel For Google Fonts', 'cfup'); ?></h1>
                        <p><?php _e('This control panel gives you the ability to control how your Google Fonts fonts are displayed.', 'cfup'); ?> 
                            <?php _e('Thanks for using Custom Fonts, and we hope you like this plugin.', 'cfup'); ?> <br /></p>

                        <hr />

                        <form action="admin.php?page=cfup-google-options" method="post" id="googlefonts_options">

                            <h2><?php _e( 'Select Fonts', 'cfup' );?></h2>
                            <div class= "gfont">
                                <p><?php _e('After selecting and saving font from dropdown it will enqueue particular font in your site', 'cfup') ?></p>
                                <select name="font" id="googlefont-select" class="webfonts-select" required>
                                    <option value="">--Select--</option>
                                    <?php
                                    //Load the list of fonts from the Google API or local cache
                                    $cfupgf_fonts = get_option('custom_font_data', true);
                                    $cfupgf_fonts = json_decode($cfupgf_fonts, true);
                                    foreach ($cfupgf_fonts as $key => $cfupgf_font) {
                                        ?>

                                        <option value='<?php echo $cfupgf_font['font-family']; ?>'><?php echo $cfupgf_font['font-family']; ?></option>

                                    <?php } ?>

                                </select>

                                <p class="submit">
                                <?php wp_nonce_field('cfup-googlefont','google-fonts-nonce');?>
                                    <input id="submit-cfup-general-settings" name="submit-google-fonts" class="button button-primary" value="<?php _e( 'Save Font', 'cfup' ); ?>" type="submit">
                                </p>
                            </div>

                            <!--html for previewing fonts-->
                        <div class="font-preview-section">

                            <h2 class="add_text"><?php _e( 'H2 tags Preview', 'cfup' );?> </h2>
                            <h3 class="add_text"><?php _e( 'H3 tags Preview', 'cfup' );?> </h3>
                            <p class="add_text"><?php _e( 'Lorem ipsum dolor sit amet, vide paulo vidisse ex quo, vis dolor pertinax praesent id. No principes disputationi sea, mutat inermis delicatissimi id sed. Est semper moderatius no, et tamquam accommodare his. Wisi numquam scripserit in vix, sumo mandamus moderatius at vim..', 'cfup' );?>    <i><?php _e( 'fast looking italic text?', 'cfup' ); ?></i></p>
                       </div>

                        </form>
                    </td>
                </tr>
            </table>

      <!--Table structure for deleting google fonts-->
			<table cellspacing="0" class="wp-list-table widefat fixed bookmarks">
			        <thead>
			            <tr>
			                <th width="20"><?php _e( 'Sn', 'cfup' ); ?></th>
			                <th><?php _e( 'Font', 'cfup' ); ?></th>
			                <th width="100"><?php _e( 'Delete', 'cfup' ); ?></th>
			            </tr>
			        </thead>
			        <!--               php code for listing font-families here-->

			        <?php
			        $googlefonts_name = get_option('googlefont_file_name');
			        $googlefonts_name = json_decode($googlefonts_name, true);
			        ?>

			        <?php
			        if (!empty($googlefonts_name)) {
			            $sn = 0;
			            foreach ($googlefonts_name as $key => $googlefont_name):
			                $sn++;
			                ?>
			                <tr id="delete_googlefont-<?php echo strtolower(preg_replace('/\s+/', '', $key)); ?>">
			                    <td><?php echo $sn; ?></td>
			                    <td><?php echo $key; ?></td>
			                    <td><a class = "delete-googlefont" data-fid="delete_googlefont-<?php echo strtolower(preg_replace('/\s+/', '', $key)); ?>" data-delete_font_gkey="<?php echo $key; ?>"href="javascript:void(0)">Delete</a></td>
			                </tr>

			            <?php endforeach; ?>
			    <?php } else {?>
			        <tr>
			            <td colspan="3"><?php _e( 'There is no font found . Please click on add font for adding any font .', 
			                'cfup' );?></td>
			            </tr>
			    <?php }?>      

			</table>

        </div>
	</div>
		<div class="clear"></div>
</div>