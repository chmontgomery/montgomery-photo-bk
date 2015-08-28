<?php







function photography_options(){







// VARIABLES







$themename = "photography-wp";







$manualurl = 'http://www.wordpress.org/';







$shortname = "photography";







$GLOBALS['template_path'] = get_bloginfo('template_directory');















//Access the WordPress Categories via an Array







$photography_categories = array();  







$photography_categories_obj = get_categories('hide_empty=0');















foreach ($photography_categories_obj as $photography_cat) {















    $photography_categories[$photography_cat->cat_ID] = $photography_cat->cat_name;}















$categories_tmp = array_unshift($photography_categories, "Select a category:");    















//Access the WordPress Pages via an Array















$photography_pages = array();







$photography_pages_obj = get_pages('sort_column=post_parent,menu_order');    















foreach ($photography_pages_obj as $photography_page) {















    $photography_pages[$photography_page->ID] = $photography_page->post_name; }







$photography_pages_tmp = array_unshift($photography_pages, "Select a page:");       















//Stylesheets Reader







$alt_stylesheet_path = TEMPLATEPATH . '/styles/';







$alt_stylesheets = array();















if ( is_dir($alt_stylesheet_path) ) {















    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 















        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {















            if(stristr($alt_stylesheet_file, ".css") !== false) {















              $alt_stylesheets[] = $alt_stylesheet_file;















            }







        }    







    }















}























//More Options















$all_uploads_path = home_url() . '/wp-content/uploads/';







$all_uploads = get_option('photography_uploads');







$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");















// THIS IS THE DIFFERENT FIELDS















$options = array();   















$options[] = array( "name" => "General Settings",















                    "type" => "heading");













$options[] = array( "name" => "Custom Logo",















					"desc" => "Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png)",







					"id" => $shortname."_logo",







					"std" => "",







					"type" => "upload");   





$options[] = array( "name" => "Custom Favicon",







					"desc" => "Upload a 16px x 16px Png/Gif image that will represent your wephotographyte's favicon.",







					"id" => $shortname."_custom_favicon",







					"std" => "",







					"type" => "upload"); 



$options[] = array( "name" => "Tracking Code",







					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",







					"id" => $shortname."_google_analytics",







					"std" => "",







					"type" => "textarea");        



$options[] = array( "name" => "RSS URL",







					"desc" => "Enter your preferred RSS URL. (Feedburner or other)",







					"id" => $shortname."_feedburner_url",







					"std" => "",







					"type" => "text");



$options[] = array( "name" => "Custom CSS",







                    			"desc" => "Quickly add some CSS to your theme by adding it to this block.",







					"id" => $shortname."_custom_css",







					"std" => "",







					"type" => "textarea");



$options[] = array( "name" => "Extra Scripts for header",







					"desc" => "You can add extra scripts in here to add at the head section'",







					"id" => $shortname."_exscripts",







					"type" => "textarea");   



$options[] = array( "name" => "Slideshow Options",



				 	"type" => "heading"); 











$options[] = array( "name" => "Choose a Slideshow Effect",



					"desc" => "Choose a effect for changing slides.",



					"id" => $shortname."_slideshow_effect",



					"options" => array('random', 'sliceDown', 'sliceDownLeft', 'sliceUp', 'sliceUpLeft', 'sliceUpDown', 'sliceUpDownLeft', 'fold', 'fade', 'slideInRight', 'slideInLeft', 'boxRandom', 'boxRain', 'boxRainReverse', 'boxRainGrow', 'boxRainGrowReverse' ),



					"std" => "random",



					"type" => "select");







$options[] = array(  







	"name" => "Slideshow Pause time",







	"desc" => "Fill the Pause time of the slide before changing in milliseconds. Default is 7000.",







	"id" => $shortname."_slideshow_pausetime",







	"std" => "",







	"type" => "text");













$options[] = array( "name" => "Footer Options",















				 "type" => "heading"); 











$options[] = array(  



	"name" => "Footer Text",



	"desc" => "Please Enter footer text.",



	"id" => $shortname."_ftext",



	"std" => "",



	"type" => "textarea");





$options[] = array( "name" => "Color Picker",

				 "type" => "heading"); 			

 wp_enqueue_script('jscolor.js', get_bloginfo( 'stylesheet_directory' ) . '/js/jscolor.js'); //color picker				

					

$options[] = array("name" => "Body Background Color",

        "desc" => "Set the background color of the body. ",

        "id" => $shortname."_body_background_color",

        "type" => "color-picker",

        "std" => ""); 

$options[] = array("name" => "Body Text Color",

        "desc" => "Set the color of the body text. ",

        "id" => $shortname."_body_text_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Link Color",

        "desc" => "This color will be used for every link on the website. ",

        "id" => $shortname."_link_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Link Hover Color",

        "desc" => "This color will be the global hover color for all links including the nav menu links. ",

        "id" => $shortname."_link_hover_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Top Border Color",

        "desc" => "Set the top most border color of the website. ",

        "id" => $shortname."_wrap_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Site Title Color",

        "desc" => "Set the color of the website title/logo. ",

        "id" => $shortname."_logo_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Nav Menu Links Color",

        "desc" => "Set the nav menu link items color. ",

        "id" => $shortname."_nav_link_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Header and Sub Headers Color",

        "desc" => "Set the headings color for all heading tags (h1,h2,h3,h4,h5,h6). ",

        "id" => $shortname."_heading_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Meta Text Color",

        "desc" => "Set the meta text color. ",

        "id" => $shortname."_global_meta_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Border Color",

        "desc" => "Set the border color used all over the website(input fields, textarea, dashed borders etc.). ",

        "id" => $shortname."_global_border_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Button Background Color",

        "desc" => "Set the buttons background color. ",

        "id" => $shortname."_global_button_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Button Background Hover Color",

        "desc" => "Set the buttons hover background color. ",

        "id" => $shortname."_global_button_hover_color",

        "type" => "color-picker",

        "std" => "");

$options[] = array("name" => "Global Button Text Color",

        "desc" => "Set the buttons Text color. ",

        "id" => $shortname."_global_button_text_color",

        "type" => "color-picker",

        "std" => "");





update_option('photography_template',$options);      

update_option('photography_themename',$themename);   

update_option('photography_shortname',$shortname);

update_option('photography_manual',$manualurl);



// photography Metabox Options

/*

$photography_metaboxes = array(

		"image" => array (

		"name"		=> "image",

		"default" 	=> "",

		"label" 	=> "Image",

		"type" 		=> "upload",

		"desc"      => "Enter the URL for image to be used by the Dynamic Image resizer."

		)

    );

update_option('photography_custom_template',$photography_metaboxes);      

*/

/*

function photography_update_options(){

        $options = get_option('photography_template',$options);  

        foreach ($options as $option){

            update_option($option['id'],$option['std']);

        }   

}

function photography_add_options(){

        $options = get_option('photography_template',$options);  

        foreach ($options as $option){

            update_option($option['id'],$option['std']);

        }   

}

//add_action('switch_theme', 'photography_update_options'); 

if(get_option('template') == 'photographyframework'){       

    photography_add_options();

} // end function 

*/

}

add_action('init','photography_options');  

?>