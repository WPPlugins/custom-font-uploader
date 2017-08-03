<?php

$allowedFontFormats = array('ttf', 'otf', 'woff');
$allowedFontSize = 15;
$wpAllowedMaxSize = wp_max_upload_size();
$wpAllowedMaxSizeToMB = $wpAllowedMaxSize / 1048576;
if ($wpAllowedMaxSizeToMB < $allowedFontSize) {
    $allowedFontSize = $wpAllowedMaxSizeToMB;
}
$allowedFontSizeinBytes = $allowedFontSize * 1024 * 1024; // 10 MB to bytes


$flag = 0;
$uploadOk = 1;
if (isset($_POST['submit-cfup-font']) && wp_verify_nonce($_POST['browsefont-nonce'], 'cfup-font')) {
    
    $font_name = sanitize_text_field($_POST['font_name']);
    // $font_file_name = $_FILES['font_file']['name'];
    //Sanitize the filename (See note below)
    $remove_these = array(' ','`','"','\'','\\','/');
    $font_file_name = str_replace($remove_these, '', $_FILES['font_file']['name']);

    $font_file_details = pathinfo($_FILES['font_file']['name']);
    $file_extension = strtolower($font_file_details['extension']); 
    $font_size = $_FILES['font_file']['size'];
    $fontUploadFinalMsg = '';
    $fontUploadFinalStatus = 'updated';

    /* code for uploading fonts to wp-content/uploads/wbcom_fonts/fonts directory */
    $dir = ABSPATH . 'wp-content/uploads/wbcom_fonts/fonts';
    $font_ext = basename($_FILES['font_file']['name']);
    $upload_dir = $dir . "/" . basename($_FILES['font_file']['name']);

    // Allow certain file formats
    if( $file_extension != "woff" && $file_extension != "otf" && $file_extension != "ttf" ) {

        echo "<p style='border-left: 5px solid red; width: 50%; text-align: left; background: #fff none repeat scroll 0 0; 
                  border-left: 3px solid red; padding-bottom: 5px; padding-top: 5px;'>";

        echo "Sorry, your file was not uploaded. only woff, otf, & ttf files are allowed.";
        $uploadOk = 0;
    }

    // if everything is ok, try to upload file
  else {
    
    if ( !file_exists($upload_dir) && move_uploaded_file($_FILES['font_file']['tmp_name'], $upload_dir ) )  {
        $flag = 1;
        ?>
        <p class="file-success-msg"> 
        <?php echo "The file " . basename($_FILES['font_file']['name']) . " has been uploaded and enqueued"; ?></p>
        <?php

        //Save details in db
        $fonts = get_option('font_file_name');

        if (empty($fonts)) {
            $fonts = array();
            $fonts[$font_name] = $font_file_name;
            update_option('font_file_name', json_encode($fonts));
        } else {
            $fonts = json_decode($fonts, true);
            $fonts[$font_name] = $font_file_name;
            update_option('font_file_name', json_encode($fonts));
        }

    } else {
        echo "<p style='border-left: 5px solid red; width: 50%; text-align: left; background: #fff none repeat scroll 0 0; 
                  border-left: 5px solid red; padding-bottom: 5px; padding-top: 5px;'>"; 
        echo "The file '".$font_file_name."' already enqueued !! Please try with another file";
        echo "</p>";
        }
    } 
 
}

?>

<!--settings for enqueuing font with browse and uploads approach-->
<div id="wpbody" role="main">
    <div id="wpbody-content" aria-label="Main content" tabindex="0">
        <div class="wrap">
            <h1><?php _e( 'Custom Font', 'cfup' ); ?></h1>

            <table class="wp-list-table widefat fixed bookmarks">
                <thead>
                    <tr>
                        <th><?php _e( 'Please upload font file format of type :ttf,tf,woff ', 'cfup' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>

                            <!-- Code for php -->
                            <?php if (!empty($fontUploadFinalMsg)){ ?>
                                <div class="<?php echo $fontUploadFinalStatus; ?>" id="message">
                                    <p><?php echo $fontUploadFinalMsg ?></p>                 
                                </div>
                            <?php }?>

                            <p align="right"><input type="button" name="open_add_font" onClick="open_add_font();" class="button-primary" value="<?php _e( 'Add Fonts', 'cfup' ); ?>" /><br/></p>

                            <div id="font-upload" style="display:none;">
                                <form action="admin.php?page=cfup-options" id="open_add_font_form" method="post" enctype="multipart/form-data">
                                    <table class="cfup_form">
                                        <tr>
                                            <td width="175"><?php _e( 'Font Name', 'cfup' ); ?></td>
                                            <td><input type="text" name="font_name" value="" maxlength="20" style="width:200px;" required>                 </td>
                                        </tr>   
                                        <tr>    
                                            <td><?php _e( 'Font File', 'cfup' ); ?></td>
                                            <td><input type="file" id="font_file" name="font_file" value="" accept=".woff,.ttf,.otf" required><br/>
                                                <em>Accepted Font Format :<?php echo join(',', $allowedFontFormats); ?> | Font Size: Upto 20 MB</em><br/>

                                            </td>
                                        </tr>
                                        <tr>        
                                            <td>&nbsp;

                                            </td>
                                            <td>
                                                <?php wp_nonce_field('cfup-font','browsefont-nonce');?>
                                                <input type="submit" name="submit-cfup-font" id="submit-cfup-font" class="button-primary" value="<?php _e( 'Upload', 'cfup' ); ?>" />
                                                <div id="font_upload_message" class=""></div>
                                                <p>
                                                <?php _e( 'By clicking on Upload, you confirm that you have rights to use this font.', 
                                                'cfup' ); ?></p>
                                            </td>
                                        </tr>
                                    </table>    
                                </form>
                                <br/><br/>
                            </div>

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
                                $font_name = get_option('font_file_name');
                                $font_name = json_decode($font_name, true);
                                ?>

                                <?php
                                    if ( !empty($font_name) ) {
                                    $sn = 0;
                                    foreach ($font_name as $key => $unserial_font):
                                        $sn++
                                        ?>
                                        <tr id="delete-font-<?php echo strtolower(preg_replace('/\s+/', '', $key)); ?>">
                                            <td><?php echo $sn; ?></td>
                                            <td><?php echo $key; ?></td>
                                            <td><a class = "delete-font" data-fid="delete-font-<?php echo strtolower(preg_replace('/\s+/', '', $key)); ?>" data-delete_font_key="<?php echo $key; ?>"href="javascript:void(0)">Delete</a></td>
                                        </tr>

                                    <?php endforeach; ?>
                            <?php } else {?>
                                <tr>
                                    <td colspan="3"><?php _e( 'There is no font found . Please click on add font for adding any font .', 
                                        'cfup' );?></td>
                                </tr>

                            <?php }?>      

                            </table>

                            <script>
                                function open_add_font() {
                                    jQuery('#font-upload').toggle('fast');
                                }
                            </script>
                            <br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>

    </div><!-- wpbody-content -->
</div>