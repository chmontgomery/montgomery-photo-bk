<?php



//Start photographyThemes Functions - Please refrain from editing this file.
$functions_path = TEMPLATEPATH . '/functions/';
$includes_path = TEMPLATEPATH . '/includes/';

// Options panel variables and functions
require_once ($functions_path . 'admin-setup.php');

// Custom functions and plugins
require_once ($functions_path . 'admin-functions.php');

// Thumbnails
add_theme_support('post-thumbnails');
set_post_thumbnail_size(958, 9999);
add_image_size('thumb', 280, 280, true);
add_image_size('thumb-m', 420, 420, true);

// Custom fields 

// require_once ($functions_path . 'admin-custom.php');
// More photographyThemes Page
require_once ($functions_path . 'admin-theme-page.php');

// Admin Interface!
require_once ($functions_path . 'admin-interface.php');

// Options panel settings
require_once ($includes_path . 'theme-options.php'); // What we do!

//Custom Theme Fucntions
require_once ($includes_path . 'theme-functions.php'); 

//Custom Comments
require_once ($includes_path . 'theme-comments.php'); 

// Load Javascript in wp_head
require_once ($includes_path . 'theme-js.php');

// Widgets  & Sidebars
require_once ($includes_path . 'sidebar-init.php');
require_once ($includes_path . 'theme-widgets.php');

// Taxonomy Pagi
require_once ($includes_path . 'taxanomy-pagi.php');
add_action('wp_head', 'photographythemes_wp_head');
add_action('admin_menu', 'photographythemes_add_admin');
add_action('admin_head', 'photographythemes_admin_head');    

//Custom Comments
require_once ($includes_path . 'theme-comments.php'); 

function add_stylesheet(){
     	wp_enqueue_style('stylesheet', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('nivo-style', get_template_directory_uri() . '/css/slider/nivo-slider.css');
	wp_enqueue_style('nivo-default', get_template_directory_uri() . '/css/slider/default/default.css');
	wp_enqueue_style('skeleton-grid', get_template_directory_uri() . '/css/skeleton.css');
	wp_enqueue_style('dynamic-css', get_template_directory_uri() . '/includes/dynamic-css.php');
}

function theme_scripts_method() {
	wp_enqueue_script('nivo', get_template_directory_uri() . '/js/jquery.nivo.slider.pack.js', array('jquery'));
	wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js',	array('jquery'));
}

add_action('wp_print_styles', 'add_stylesheet');
add_action('wp_enqueue_scripts', 'theme_scripts_method');



function new_excerpt_length($length) {
	return 100;
}
add_filter('excerpt_length', 'new_excerpt_length');
function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit+ 1));
 if(count($words) > $word_limit) {
  array_pop($words);
  //add a ... at last article when more than limitword count
  echo implode(' ', $words)."..."; } else {
 //otherwise
 echo implode(' ', $words); }
}

// Registering Menus For Theme
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
	register_nav_menus(
		array(
			'main-nav-menu' => __( 'Header' ),
	)
	);

register_post_type( 'portfolios',
		array(
		'labels' => array(
		'name' => __( 'Portfolio' ),
		'singular_name' => __( 'Portfolio' ),
		'all_items' => 'Portfolio Items',
		'add_new' => 'Add New Portfolio Item',
		'add_new_item' => __( 'Add New Portfolio Item' ),
		'edit' => __( 'Edit' ),
		'edit_item' => __( 'Edit Portfolio Item' ),
		'new_item' => __( 'New Portfolio Item' ),
		'view' => __( 'View Portfolio Item' ),
		'view_item' => __( 'View Portfolio Item' ),
		'search_items' => __( 'Search Portfolio Items' ),
		'not_found' => __( 'No Portfolio Items found' ),
		'not_found_in_trash' => __( 'No Portfolio Items found in Trash' ),
		'parent' => __( 'Parent Portfolio Item' )
		),
'public' => true,
'supports' => array('thumbnail','title','editor'),
'rewrite' => array( 'slug' => 'portfolios', 'with_front' => true ),
'query_var' => true,
'exclude_from_search' => false,
'show_ui' => true,
'capability_type' => 'post'
		)
	);
register_taxonomy('gallery', 'portfolios', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( 'gallery', 'taxonomy general name' ),
			'singular_name' => _x( 'gallery', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Galleries' ),
			'all_items' => __( 'All Galleries' ),
			'parent_item' => __( 'Parent Gallery' ),
			'parent_item_colon' => __( 'Parent Gallery:' ),
			'edit_item' => __( 'Edit Gallery' ),
			'update_item' => __( 'Update Gallery' ),
			'add_new_item' => __( 'Add New Gallery' ),
			'new_item_name' => __( 'New GalleryName' ),
			'menu_name' => __( 'Galleries' )
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'gallery', 
			'with_front' => true, 
			'hierarchical' => true 
		),
	));

register_post_type( 'slide',
		array(
		'labels' => array(
		'name' => __( 'Slides' ),
		'singular_name' => __( 'Slide' ),
		'add_new' => __( 'Add New' ),
		'add_new' => 'Add New Slide',
		'add_new_item' => __( 'Add New Slide' ),
		'edit' => __( 'Edit' ),
		'edit_item' => __( 'Edit Slide' ),
		'new_item' => __( 'New Slide' ),
		'view' => __( 'View Slide' ),
		'view_item' => __( 'View Slides' ),
		'search_items' => __( 'Search Slides' ),
		'not_found' => __( 'No Slides found' ),
		'not_found_in_trash' => __( 'No Slides found in Trash' ),
		'parent' => __( 'Parent Slide' )
		),
'public' => true,
'supports' => array('thumbnail','title'),
'rewrite' => true,
'query_var' => true,
'exclude_from_search' => true,
'show_ui' => true,
'capability_type' => 'post'
		)
	);
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );  



add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 ); 



function remove_thumbnail_dimensions( $html ) {     



$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );    



return $html; } 




?>