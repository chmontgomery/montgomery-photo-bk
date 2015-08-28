<?php 





// Custom fields for WP write panel


// This code is protected under Creative Commons License: http://creativecommons.org/licenses/by-nc-nd/3.0/





function photographythemes_meta_box_content() {


    global $post;


    $photography_metaboxes = get_option('photography_custom_template');     


    $output = '';


    $output .= '<table class="photography_metaboxes_table">'."\n";


    foreach ($photography_metaboxes as $photography_id => $photography_metabox) {


    if(        


                $photography_metabox['type'] == 'text' 


    OR      $photography_metabox['type'] == 'select' 


    OR      $photography_metabox['type'] == 'checkbox' 


    OR      $photography_metabox['type'] == 'textarea'


    OR      $photography_metabox['type'] == 'radio'


    )


            $photography_metaboxvalue = get_post_meta($post->ID,$photography_metabox["name"],true);


            


            if ($photography_metaboxvalue == "" || !isset($photography_metaboxvalue)) {


                $photography_metaboxvalue = $photography_metabox['std'];


            }


            if($photography_metabox['type'] == 'text'){


            


                $output .= "\t".'<tr>';


                $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_id.'">'.$photography_metabox['label'].'</label></th>'."\n";


                $output .= "\t\t".'<td><input class="photography_input_text" type="'.$photography_metabox['type'].'" value="'.$photography_metaboxvalue.'" name="photographythemes_'.$photography_metabox["name"].'" id="'.$photography_id.'"/>';


                $output .= '<span class="photography_metabox_desc">'.$photography_metabox['desc'].'</span></td>'."\n";


                $output .= "\t".'<td></td></tr>'."\n";  


                              


            }


            


            elseif ($photography_metabox['type'] == 'textarea'){


            


                $output .= "\t".'<tr>';


                $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_metabox.'">'.$photography_metabox['label'].'</label></th>'."\n";


                $output .= "\t\t".'<td><textarea class="photography_input_textarea" name="photographythemes_'.$photography_metabox["name"].'" id="'.$photography_id.'">' . $photography_metaboxvalue . '</textarea>';


                $output .= '<span class="photography_metabox_desc">'.$photography_metabox['desc'].'</span></td>'."\n";


                $output .= "\t".'<td></td></tr>'."\n";  


                              


            }





            elseif ($photography_metabox['type'] == 'select'){


                       


                $output .= "\t".'<tr>';


                $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_id.'">'.$photography_metabox['label'].'</label></th>'."\n";


                $output .= "\t\t".'<td><select class="photography_input_select" id="'.$photography_id.'" name="photographythemes_'. $photography_metabox["name"] .'">';


                $output .= '<option value="">Select to return to default</option>';


                


                $array = $photography_metabox['options'];


                


                if($array){


                


                    foreach ( $array as $id => $option ) {


                        $selected = '';


                       


                                                       


                        if($photography_metabox['default'] == $option && empty($photography_metaboxvalue)){$selected = 'selected="selected"';} 


                        else  {$selected = '';}


                        


                        if($photography_metaboxvalue == $option){$selected = 'selected="selected"';}


                        else  {$selected = '';}  


                        


                        $output .= '<option value="'. $option .'" '. $selected .'>' . $option .'</option>';


                    }


                }


                


                $output .= '</select><span class="photography_metabox_desc">'.$photography_metabox['desc'].'</span></td></td><td></td>'."\n";


                $output .= "\t".'</tr>'."\n";


            }


            


            elseif ($photography_metabox['type'] == 'checkbox'){


            


                if($photography_metaboxvalue == 'true') { $checked = ' checked="checked"';} else {$checked='';}





                $output .= "\t".'<tr>';


                $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_id.'">'.$photography_metabox['label'].'</label></th>'."\n";


                $output .= "\t\t".'<td><input type="checkbox" '.$checked.' class="photography_input_checkbox" value="true"  id="'.$photography_id.'" name="photographythemes_'. $photography_metabox["name"] .'" />';


                $output .= '<span class="photography_metabox_desc" style="display:inline">'.$photography_metabox['desc'].'</span></td></td><td></td>'."\n";


                $output .= "\t".'</tr>'."\n";


            }


            


            elseif ($photography_metabox['type'] == 'radio'){


            


                $array = $photography_metabox['options'];


            


            if($array){


            


            $output .= "\t".'<tr>';


            $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_id.'">'.$photography_metabox['label'].'</label></th>'."\n";


            $output .= "\t\t".'<td>';


            


                foreach ( $array as $id => $option ) {


                              


                    if($photography_metaboxvalue == $option) { $checked = ' checked';} else {$checked='';}





                        $output .= '<input type="radio" '.$checked.' value="' . $id . '" class="photography_input_radio"  id="'.$photography_id.'" name="photographythemes_'. $photography_metabox["name"] .'" />';


                        $output .= '<span class="photography_input_radio_desc" style="display:inline">'. $option .'</span><div class="photography_spacer"></div>';


                    }


                    $output .=  '</td></td><td></td>'."\n";


                    $output .= "\t".'</tr>'."\n";    


                 }


            }


            


            elseif($photography_metabox['type'] == 'upload')


            {


            


                $output .= "\t".'<tr>';


                $output .= "\t\t".'<th class="photography_metabox_names"><label for="'.$photography_id.'">'.$photography_metabox['label'].'</label></th>'."\n";


                $output .= "\t\t".'<td class="photography_metabox_fields">'. photographythemes_uploader_custom_fields($post->ID,$photography_metabox["name"],$photography_metabox["default"],$photography_metabox["desc"]);


                $output .= '</td>'."\n";


                $output .= "\t".'</tr>'."\n";


                


            }


        }


    


    $output .= '</table>'."\n\n";


    echo $output;


}





function photographythemes_uploader_custom_fields($pID,$id,$std,$desc){





    // Start Uploader


    $upload = get_post_meta( $pID, $id, true);


    $uploader .= '<input class="photography_input_text" name="'.$id.'" type="text" value="'.$upload.'" />';


    $uploader .= '<div class="clear"></div>'."\n";


    $uploader .= '<input type="file" name="attachement_'.$id.'" />';


    $uploader .= '<input type="submit" class="button button-highlighted" value="Save" name="save"/>';


    $uploader .= '<span class="photography_metabox_desc">'.$desc.'</span></td>'."\n".'<td class="photography_metabox_image"><a href="'. $upload .'"><img src=" '.$upload.' " width="150" height="80" alt="" /></a>';





return $uploader;


}





function photographythemes_metabox_insert() {


    global $globals;


    $photography_metaboxes = get_option('photography_custom_template');     


    $pID = $_POST['post_ID'];


    $counter = 0;


                       


     $files = array();


     $errors = array();


                


    foreach ($photography_metaboxes as $photography_metabox) { // On Save.. this gets looped in the header response and saves the values submitted


    if($photography_metabox['type'] == 'text' OR $photography_metabox['type'] == 'select' OR $photography_metabox['type'] == 'checkbox' OR $photography_metabox['type'] == 'textarea' ) // Normal Type Things...


        {


            $var = "photographythemes_".$photography_metabox["name"];


            if (isset($_POST[$var])) {            


                if( get_post_meta( $pID, $photography_metabox["name"] ) == "" )


                    add_post_meta($pID, $photography_metabox["name"], $_POST[$var], true );


                elseif($_POST[$var] != get_post_meta($pID, $photography_metabox["name"], true))


                    update_post_meta($pID, $photography_metabox["name"], $_POST[$var]);


                elseif($_POST[$var] == "") {


                   delete_post_meta($pID, $photography_metabox["name"], get_post_meta($pID, $photography_metabox["name"], true));


                }


            }


            elseif(!isset($_POST[$var]) && $photography_metabox['type'] == 'checkbox') { 


                update_post_meta($pID, $photography_metabox["name"], 'false'); 


            }      


            else {


                if ($_POST['action'] == 'editpost'){


                  delete_post_meta($pID, $photography_metabox["name"], get_post_meta($pID, $photography_metabox["name"], true)); // Deletes check boxes OR no $_POST


                }


            }    


        }


  


    elseif($photography_metabox['type'] == 'upload') // So, the upload inputs will do this rather


        {    





                $uploaddir = WP_CONTENT_DIR . '/photography_uploads/' ;


                $loc = WP_CONTENT_URL .'/photography_uploads/';


                


                if(!is_dir($uploaddir)){


                    mkdir($uploaddir,0755);


                }


               


                // Error Tracking - Dir was not created 


                if (!is_dir($uploaddir)) {


                        $error = array('name' => '', 'error' => 'folder_not_created');


                        $errors[] = $error;            


                }


                else 


                {


                $dir = opendir($uploaddir);


                $id = $photography_metabox['name'];





                  if(isset($_FILES['attachement_'.$id]) && !empty($_FILES['attachement_'.$id]['name'])) 


                  {


                      if(eregi('image/', $_FILES['attachement_'.$id]['type']))


                      {


                        while($file = readdir($dir)) { if ($file != "." && $file != "..") { array_push($files,$file); }} closedir($dir);


                        $name = $_FILES['attachement_'.$id]['name'];


                        $file_name = substr($name,0,strpos($name,'.'));


                        $file_name = str_replace(' ','_',$file_name);


                         


                        $_FILES['attachement_'.$id]['name'] = $loc . ceil(count($files) + 1).'-'. $file_name .''.strrchr($name, '.');


                        $uploadfile = $uploaddir . basename($_FILES['attachement_'.$id]['name']);


                    


                         if(move_uploaded_file($_FILES['attachement_'.$id]['tmp_name'], $uploadfile)) {


                         


                         $uploaded_file = $_FILES['attachement_'.$id]['name'];


                                  


                          if (isset($uploaded_file)) {    


                              if( get_post_meta( $pID, $id ) == "" )


                              {


                                  add_post_meta($pID, $id, $uploaded_file, true );


                              }


                              elseif($uploaded_file != get_post_meta($pID, $id, true))


                              {


                                  update_post_meta($pID, $id, $uploaded_file);


                              }


                              elseif($uploaded_file == "")


                              {


                                delete_post_meta($pID, $id, get_post_meta($pID, $id, true));


                              }    


                             }


                          } 


                    }


                     else 


                     {


                            $error = array('name' => $_FILES['attachement_'.$id]['name'], 'error' => 'invalid_file');


                            $errors[] = $error;


                        }


                  }


               elseif(!empty($_POST[$id]) && !isset($uploaded_file)){


                    update_post_meta($pID, $id, $_POST[$id]);


               }


               elseif (empty($_POST[$id]) && !isset($uploaded_file) && $_POST['action'] == 'editpost') {   // Upload input is empty - delete the value


                  delete_post_meta($pID, $id, get_post_meta($pID, $id, true));


               } 


        }





    }


   // Error Tracking - File upload was not an Image


   update_option('photography_upload_custom_errors',$errors);


}


}





function photographythemes_meta_box() {


    if ( function_exists('add_meta_box') ) {


        //add_meta_box('photographythemes-settings',get_option('photography_themename').' Custom Settings','photographythemes_meta_box_content','post','normal');


        //add_meta_box('photographythemes-settings',get_option('photography_themename').' Custom Settings','photographythemes_meta_box_content','page','normal');


    }


}





function photographythemes_header_inserts(){


?>


<script type="text/javascript">





    jQuery(document).ready(function(){


        jQuery('form#post').attr('enctype','multipart/form-data');


        jQuery('form#post').attr('encoding','multipart/form-data');


        jQuery('.photography_metaboxes_table th:last, .photography_metaboxes_table td:last').css('border','0');


        var val = jQuery('input#title').attr('value');


        if(val == ''){ 


        jQuery('.photography_metabox_fields .button-highlighted').after("<em class='photography_red_note'>Please add a Title before uploading a file</em>");


        };


        <?php //Errors


        $upload_errors = get_option('photography_upload_custom_errors');


        if(!empty($upload_errors)){


         $error_shown == false;


         foreach($upload_errors as $error)


            {


                 if($error['error'] == 'folder_not_created' && $error_shown != true){


                    ?>


                    jQuery('form#post').before('<div class="updated fade"><p>photographyThemes: <strong>Oh No!</strong> We don\'t have the permissions to create the upload folder on your server. Please create it manually: <em>/wp-content/<strong>photography_uploads</strong></em>. Thanks!</strong></p></div>');


                    <?php


                $error_shown = true;


                }


                if($error['error'] == 'invalid_file' ){


                ?>


                 jQuery('form#post').before('<div class="updated fade"><p>photographyThemes: <strong><?php echo $error['name']; ?></strong> is not a valid image file, please try another file.</p></div>');


                 <?php


                }


            }


           delete_option('photography_upload_custom_errors');


        } ?>


    


    });





</script>


<style type="text/css">


.photography_input_text { margin:0 0 10px 0; background:#f4f4f4; color:#444; width:80%; font-size:11px; padding: 5px;}


.photography_input_select { margin:0 0 10px 0; background:#f4f4f4; color:#444; width:60%; font-size:11px; padding: 5px;}


.photography_input_checkbox { margin:0 10px 0 0; }


.photography_input_radio { margin:0 10px 0 0; }


.photography_input_radio_desc { font-size: 12px; color: #666 ; }


.photography_spacer { display: block; height:5px}


.photography_metabox_desc { font-size:10px; color:#aaa; display:block}


.photography_metaboxes_table{ border-collapse:collapse; width:100%}


.photography_metaboxes_table tr:hover th,


.photography_metaboxes_table tr:hover td { background:#f8f8f8}


.photography_metaboxes_table th,


.photography_metaboxes_table td{ border-bottom:1px solid #ddd; padding:10px 10px;text-align: left; vertical-align:top}


.photography_metabox_names { width:20%}


.photography_metabox_fields { width:70%}


.photography_metabox_image { text-align: right;}


.photography_red_note { margin-left: 5px; color: #c77; font-size: 10px;}


.photography_input_textarea { width:80%; height:120px;margin:0 0 10px 0; background:#f0f0f0; color:#444;font-size:11px;padding: 5px;}


</style>


<?php


}


add_action('admin_menu', 'photographythemes_meta_box');


add_action('admin_head', 'photographythemes_header_inserts');


add_action('save_post', 'photographythemes_metabox_insert');


?>