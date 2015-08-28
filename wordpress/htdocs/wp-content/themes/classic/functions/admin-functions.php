<?php





/*


photographyThemes Framework Version & Theme Version





*/


function photography_version(){





    $photography_framework_version = "1.0.7";





    $theme_data = wp_get_theme(TEMPLATEPATH . '/style.css');


    $theme_version = $theme_data['Version'];





    echo '<meta name="generator" content="'. get_option('photography_themename').' '. $theme_version .'" />' ."\n";


    echo '<meta name="generator" content="photography Framework Version '. $photography_framework_version .'" />' ."\n";


   


}


add_action('wp_head','photography_version');






/*





Get Video


This function gets the embed code from the custom field


Parameters: 


        $key = Custom field key eg. "embed"


        $width = Set width manually without using $type


        $height = Set height manually without using $type


*/





function photography_get_embed($key, $width, $height, $class = 'video', $id = null) {





  if(empty($id))


    {


    global $post;


    $id = $post->ID;


    } 


    





$custom_field = get_post_meta($id, $key, true);





if ($custom_field) : 





    $org_width = $width;


    $org_height = $height;


    


    // Get custom width and height


    $custom_width = get_post_meta($id, 'width', true);


    $custom_height = get_post_meta($id, 'height', true);    


    


    // Set values: width="XXX", height="XXX"


    if ( !$custom_width ) $width = 'width="'.$width.'"'; else $width = 'width="'.$custom_width.'"';


    if ( !$custom_height ) $height = 'height="'.$height.'"'; else $height = 'height="'.$custom_height.'"';


    $custom_field = stripslashes($custom_field);


    $custom_field = preg_replace( '/width="([0-9]*)"/' , $width , $custom_field );


    $custom_field = preg_replace( '/height="([0-9]*)"/' , $height , $custom_field );    





    // Set values: width:XXXpx, height:XXXpx


    if ( !$custom_width ) $width = 'width:'.$org_width.'px'; else $width = 'width:'.$custom_width.'px';


    if ( !$custom_height ) $height = 'height:'.$org_height.'px'; else $height = 'height:'.$custom_height.'px';


    $custom_field = stripslashes($custom_field);


    $custom_field = preg_replace( '/width:([0-9]*)px/' , $width , $custom_field );


    $custom_field = preg_replace( '/height:([0-9]*)px/' , $height , $custom_field );    





    $output = '';


    $output .= '<div class="'. $class .'">' . $custom_field . '</div>';


    


    return $output; 


    


endif;





}





// Show menu in header.php


// Exlude the pages from the slider


function photography_show_pagemenu( $exclude="" ) {


    // Split the featured pages from the options, and put in an array


    if ( get_option('photography_ex_featpages') ) {


        $menupages = get_option('photography_featpages');


        $exclude = $menupages . ',' . $exclude;


    }


    


    $pages = wp_list_pages('sort_column=menu_order&title_li=&echo=0&depth=1&exclude='.$exclude);


    $pages = preg_replace('%<a ([^>]+)>%U','<a $1><span>', $pages);


    $pages = str_replace('</a>','</span></a>', $pages);


    echo $pages;


}





// Get the style path currently selected


function photography_style_path() {


    $style = $_REQUEST[style];


    if ($style != '') {


        $style_path = $style;


    } else {


        $stylesheet = get_option('photography_alt_stylesheet');


        $style_path = str_replace(".css","",$stylesheet);


    }


    if ($style_path == "default")


      echo 'images';


    else


      echo 'styles/'.$style_path;


}





// Get the style path currently selected


function get_page_id($page_name){


    global $wpdb;


    $page_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."' AND post_status = 'publish' AND post_type = 'page'");


    return $page_name;


}





//Short Codes


function photography_post_insert_shortcode($attr) {





    // Allow plugins/themes to override the default gallery template.


    $output = apply_filters('insert', '', $attr);


    if ( $output != '' )


        return $output;





    extract(shortcode_atts(array(


        'name'      => null,


        'id'         => null,


        'before'    => '',


        'after'     => ''


    ), $attr));





    $id = intval($id);


    


    global $wpdb;


    if($name == ''){


    $query = "SELECT post_content FROM $wpdb->posts WHERE id = $id";


    } 


    else


    {


       $query = "SELECT post_content FROM $wpdb->posts WHERE post_name = '$name'";   


    }


    


    $result = $wpdb->get_var($query);


    


    if(!empty($result)){


        $result = wpautop( $result, $br = 1 ); 


        return $before . $result . $after;


    }


    else


        return;





}





add_shortcode('insert', 'photography_post_insert_shortcode');  // use "[page]" in a post








?>