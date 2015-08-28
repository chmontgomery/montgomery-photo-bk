<?php
// Options panel stylesheet
function photographythemes_admin_head() { 
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/functions/admin-style.css" media="screen" />';
}

// Load different stylesheet
function photographythemes_wp_head() { 
    //Styles
     $style = $_REQUEST[style];
     if ($style != '') {
          echo '<link href="'. get_bloginfo('template_directory') .'/styles/'. $style . '.css" rel="stylesheet" type="text/css" />'."\n"; 
     } else { 
          $stylesheet = get_option('photography_alt_stylesheet');
          if($stylesheet != ''){
               echo '<link href="'. get_bloginfo('template_directory') .'/styles/'. $stylesheet .'" rel="stylesheet" type="text/css" />'."\n";         
          }
     } 
     
      // Custom.css insert
      echo '<link href="'. get_bloginfo('template_directory') .'/custom.css" rel="stylesheet" type="text/css" />'."\n";   
      
     // Favicon
    if(get_option('photography_custom_favicon') != ''){
        echo '<link rel="shortcut icon" href="'.  get_option('photography_custom_favicon')  .'"/>'."\n";
     }    
     
     // Custom CSS block in Backend
    $custom_css = get_option('photography_custom_css');
    if($custom_css != '')
        {
            $output = '<style type="text/css">'."\n";
            $output .= $custom_css . "\n";
            $output .= '</style>'."\n";
            echo $output;
        }
        
    //Decode
     $decode = $_REQUEST['decode'];
     if ($decode == 'true') 
		photography_option_output(); 

	// Localization
	load_theme_textdomain(photographythemes);	

}

// Add Encrypted setting field to footer for debug purposes
function photography_option_output(){

    $data = get_option('photography_settings_encode');
    echo '<meta name="generator" content="' . $data . '" />';

}


// Use legacy comments on versions before WP 2.7
add_filter('comments_template', 'legacy_comments');
function legacy_comments($file) {
    if(!function_exists('wp_list_comments')) : // WP 2.7-only check
        $file = TEMPLATEPATH . '/comments-legacy.php';
    endif;
    return $file;
}



?>