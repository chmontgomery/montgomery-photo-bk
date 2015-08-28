<?php
/*
Plugin Name: Shiba Gallery
Plugin URI: http://shibashake.com/wordpress-theme/super-wordpress-gallery-plugin
Description: Allows you to display your WordPress galleries using NoobSlide, SlimBox, TINY SlideShow, or the WordPress native gallery. Display multiple galleries and mix and match any way you want using the gallery shortcode.
Version: 4.3.4
Author: ShibaShake
Author URI: http://shibashake.com
*/


/*  Copyright 2009  Shiba Gallery  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	MooTools and NoobSlide are distributed under the MIT License.
	
*/


// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


define( 'SHIBA_GALLERY_VERSION', '4.3.4'); 
define( 'SHIBA_GALLERY_RELEASE_DATE', date_i18n( 'F j, Y', 1379398670 ) );
define( 'SHIBA_GALLERY_DIR', WP_PLUGIN_DIR . '/shiba-gallery' );
define( 'SHIBA_GALLERY_URL', WP_PLUGIN_URL . '/shiba-gallery' );

define( 'SHIBA_GALLERY_OPTIONS', 'shiba_gallery_options' );

// This is the default width that we assign, when javascript or cookies are not available,
// and we cannot determine the device screen width.
if (!defined('SHIBA_MAX_SCREEN_W'))
	define('SHIBA_MAX_SCREEN_W', 960);

// Do responsive processing for images and galleries that are below this size.
if (!defined('SHIBA_MAX_IMAGE_W'))
	define('SHIBA_MAX_IMAGE_W', 600);

// Load galleries

if (!class_exists("Shiba_Gallery")) :

class Shiba_Gallery {
	public static $default_options = 
		array( 	
			  	'gallery_type' => 'noobslide_thumb',
				'size' => 'large',
				'tsize' => 'auto',
				'client_responsive' => 'aspect',
				'frame' => 'frame7',
				'caption' => 'title',
				'link' => 'file',
				'cpos' => 'bottom',
				'post_gallery_type' => 'lightbox'
//				crop, autoplay 
// 				default_image, responsive (server responsive)
				);
	public static $thumb_types = array('native', 'native2', 'lytebox', 'slimbox', 'lightbox', 'navlist');
	public static $lb_types = array('slimbox', 'lytebox', 'lightbox'); // lightbox types
		
	var $found = array();
	var $empty_image; 
	
	var $general, $helper, $upload, $responsive, $options_page;
	var $noobslide, $tiny1, $tiny2, $popeye, $native2, $galleria;
	var $popeye_option, $tiny1_option, $tiny2_option, $galleria_option, $native2_option;
	
	var $options;
	var $lightNum = 1; // Keeps track of slimbox and lytebox image groups/galleries
	var $nsNum = 1; 	// Noobslide
	var $gNum = 0; 		// Other galleries
	
	var $jsStr = '', $styleStr = '';
	var $screen_w = 0; // Screen width value used by galleries during rendering

	static $galleries = array('native2' => array('class' => 'Shiba_Gallery_Native2',
												 'file' => 'shiba-native2.php'),
							  'popeye' 	=> array('class' => 'Shiba_Gallery_Popeye',
												 'file' => 'shiba-popeye.php'),
							  'tiny1' 	=> array('class' => 'Shiba_Gallery_Tiny1',
												 'file' => 'shiba-tiny1.php'),
							  'tiny2' 	=> array('class' => 'Shiba_Gallery_Tiny2',
												 'file' => 'shiba-tiny2.php'),
							  'galleria' => array('class' => 'Shiba_Gallery_Galleria',
												 'file' => 'shiba-galleria.php'),
							  'noobslide' => array('class' => 'Shiba_Gallery_NoobSlide',
												 'file' => 'shiba-noobslide.php')
							  );
			
	function __construct() {	

		if (!class_exists("Shiba_Gallery_General")) 
			require(SHIBA_GALLERY_DIR."/shiba-gallery-general.php");
		$this->general = new Shiba_Gallery_General();	
		if (!class_exists("Shiba_Gallery_Helper")) 
			require(SHIBA_GALLERY_DIR."/shiba-gallery-helper.php");
		$this->helper = new Shiba_Gallery_Helper();	


		// assign default gallery options
		$this->options = get_option(SHIBA_GALLERY_OPTIONS);
		if (!is_array($this->options)) {
			update_option( SHIBA_GALLERY_OPTIONS, Shiba_Gallery::$default_options );
			$this->options = Shiba_Gallery::$default_options;
		}
		$this->options = array_merge(Shiba_Gallery::$default_options, $this->options);


		if (is_admin()) {
			if (!class_exists("Shiba_Gallery_Options"))
				require(SHIBA_GALLERY_DIR . '/shiba-gallery-options.php');
			$this->options_page = new Shiba_Gallery_Options();	
		}	

		add_action('init', array($this,'init') );

		$this->empty_image = SHIBA_GALLERY_URL.'/images/empty.jpg';
	}


	function init() {
		if (is_admin()) return;

		add_action( 'wp_enqueue_scripts', array($this, 'add_styles_and_scripts'), 20 );
		add_action('wp_print_footer_scripts', array($this,'shiba_add_scripts'), 1);
		add_action('wp_footer', array($this,'shiba_gallery_footer'), 51);
		add_action('wp_head', array($this,'shiba_gallery_header'), 51);
	
		add_filter('post_gallery', array($this,'parse_gallery_shortcode'), 10, 2);			
		add_filter('img_caption_shortcode', array($this, 'gallery_caption_shortcode'), 10, 3);
		
		if ($this->is_option('responsive')) {
			if (!class_exists("Shiba_Gallery_Responsive")) 
				require(SHIBA_GALLERY_DIR."/shiba-gallery-responsive.php");
			$this->responsive = new Shiba_Gallery_Responsive();	
		} 
		
	}

	function add_styles_and_scripts() {
		wp_enqueue_style('shiba-gallery-css', SHIBA_GALLERY_URL.'/css/shiba-gallery.css', array(), SHIBA_GALLERY_VERSION);	
		wp_enqueue_style('shiba-gallery-frames-css', SHIBA_GALLERY_URL.'/css/shiba-gallery-frames.css', array(), SHIBA_GALLERY_VERSION);	
		wp_enqueue_script('jquery');
		wp_enqueue_script('shiba-gallery-js', SHIBA_GALLERY_URL.'/js/shiba-gallery.min.js', array(), SHIBA_GALLERY_VERSION, true);
	}
	
	function shiba_gallery_header() {
		global $shiba_gallery;
		
		?><style>
		.wp-caption p.wp-caption-text { margin: 10px 0 10px 0 !important; }
		<?php
		if ($this->is_option('image_frame')) { ?>
			.post .wp-caption { padding:0; background:transparent; border:none; }
		<?php }
		
		$current_theme = wp_get_theme();
		switch ($current_theme['Name']) {
		case 'Twenty Ten': ?>
			#content .gallery img { border:none; }
			#content .shiba-caption h3,#content .shiba-caption p, #content .shiba-caption h4, #content .shiba-gallery-caption p, #content .wp-caption img { margin: 0; color:inherit;}
			#content .noobpanel h3 { clear:none; margin:0; }
			.ts_information h3 { font-weight: bold; }
		<?php break;
		case 'Thematic':?>
			#content .noobpanel h3 { clear:none; margin:0; }
		<?php break;		
		}
		?>  
		</style>
		<script type="text/javascript">
		//<![CDATA[
		var shibaResizeFunctions = new Array();		   
		//http://stackoverflow.com/questions/2025789/preserving-a-reference-to-this-in-javascript-prototype-functions		  
		if (!Function.prototype.bind) { // check if native implementation available
		  Function.prototype.bind = function(){ 
			var fn = this, args = Array.prototype.slice.call(arguments),
				object = args.shift(); 
			return function(){ 
			  return fn.apply(object, 
				args.concat(Array.prototype.slice.call(arguments))); 
			}; 
		  };
		}		
		//]]>
		</script>
		<?php

		// Get screen width
		global $shiba_screen_w;
		$this->screen_w = (isset($shiba_screen_w)) ? $shiba_screen_w : SHIBA_MAX_SCREEN_W;
	}

	function shiba_add_scripts() {
		global $wp_scripts;
		if(is_admin()) return;
		
		if (isset($this->found['noobslide'])) :
			wp_enqueue_script('mootools', SHIBA_GALLERY_URL.'/js/noobslide/mootools-core-1.4.2-full-compat-yc.js', array(), '1.4.2', true);
			wp_enqueue_script('noobslide', SHIBA_GALLERY_URL.'/js/noobslide/_class.noobSlide.min.js', array('mootools'), SHIBA_GALLERY_VERSION, true);
			wp_enqueue_script('noobslide_walk', SHIBA_GALLERY_URL.'/js/noobslide/noobslide-walk.min.js', array('noobslide'), SHIBA_GALLERY_VERSION, true);
			// Have to manually add to in_footer
			// Check if mootools is done, if not, then add to footer
			if (!in_array('mootools', $wp_scripts->done) && !in_array('mootools', $wp_scripts->in_footer)) {
				$wp_scripts->in_footer[] = 'mootools';
//				$wp_scripts->done[] = 'mootools'; // Can't mark done or else it won't get added
			}	
			$wp_scripts->in_footer[] = 'noobslide';
			$wp_scripts->in_footer[] = 'noobslide_walk';
		endif;	


		if (isset($this->found['slimbox'])) :
			wp_enqueue_script('slimbox', SHIBA_GALLERY_URL.'/js/slimbox/slimbox2.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'slimbox';
		endif;

		if (isset($this->found['lightbox'])) :
			wp_enqueue_script('lightbox', SHIBA_GALLERY_URL.'/js/lightbox/lightbox-2.6.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'lightbox';
		endif;

		if (isset($this->found['lytebox'])) :
			// Lytebox does not work properly when minified
			wp_enqueue_script('lytebox', SHIBA_GALLERY_URL.'/js/lytebox/lytebox.dev.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'lytebox';
		endif;

		if (isset($this->found['popeye'])) :
			wp_enqueue_script('popeye', SHIBA_GALLERY_URL.'/js/popeye/jquery.popeye-2.1.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'popeye';
		endif;
		
		if (isset($this->found['tiny1'])) :
			wp_enqueue_script('tiny1', SHIBA_GALLERY_URL.'/js/tiny/tinyslider1.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'tiny1';
    	endif;

		if (isset($this->found['tiny2'])) :
			wp_enqueue_script('tiny2', SHIBA_GALLERY_URL.'/js/tiny/tinyslider2.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'tiny2';
    	endif;

		if (isset($this->found['galleria'])) :
			wp_enqueue_script('galleria', SHIBA_GALLERY_URL.'/js/galleria/galleria-1.2.7.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'galleria';
		endif;

		if (isset($this->found['native2'])) :
			wp_enqueue_script('native2', SHIBA_GALLERY_URL.'/js/shiba-native2.min.js', array(), SHIBA_GALLERY_VERSION, true);
			$wp_scripts->in_footer[] = 'native2';
		endif;

	}

	function load_gallery($type) {
		if (!isset(self::$galleries[$type])) return;
		$ginfo = self::$galleries[$type];
		if (is_array($ginfo) && !is_object($this->$type)) {
			if (!class_exists($ginfo['class'])) 
				require(SHIBA_GALLERY_DIR.'/galleries/'.$ginfo['file']);		
			$this->$type = new $ginfo['class'];	
		}
	}
	
	
	function shiba_gallery_footer() {
		global $shiba_gallery; // On some installations $shiba_gallery is not equal to $this. Don't know why
		
		if(is_admin()) return;

		?>
		<script type="text/javascript">
		//<![CDATA[

		// Render javascript as necessary
		// Deal with IE7 peculiarities
		if (navigator.userAgent.indexOf("MSIE 7") != -1) {
			jQuery('.shiba-caption').css('padding-bottom', '0px');
			jQuery('.noobslide_info_overlay').css({'zoom':'1','left':'0px'});
		}
		
		// Add resize event 
		var rtimer;
		jQuery(window).resize(function(e) {
			if (rtimer) clearTimeout(rtimer);
			rtimer = setTimeout(function(){	
//				console.log("number of functions " + shibaResizeFunctions.length);						 
				// Execute all resize events stored	
				for (var i=0; i < shibaResizeFunctions.length; i++) {
//					console.log(shibaResizeFunctions[i]);
					shibaResizeFunctions[i]();
				}
			}, 200);
		});
		<?php
		$shiba_gallery->styleStr = str_replace("\t", '', $shiba_gallery->styleStr);
		$shiba_gallery->styleStr = str_replace("\n", '', $shiba_gallery->styleStr);
		$shiba_gallery->styleStr = str_replace("\r", '', $shiba_gallery->styleStr);
		if ($shiba_gallery->styleStr) {
			echo 'jQuery("<style type=\'text/css\'>' . $shiba_gallery->styleStr . '</style>").appendTo("head");'."\n";
		}
		if ($shiba_gallery->jsStr) echo $shiba_gallery->jsStr; 
		
		
		if (isset($this->found['slimbox'])) : ?>
			// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
			if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
				jQuery(function($) {
					$("a[rel^='lightbox']").slimbox({/* Put custom options here */}, null, function(el) {
						return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
					});
				});
			}
		<?php endif;	

		if (isset($this->found['lytebox'])) : ?>
			if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
				function initLytebox() { myLytebox = new LyteBox(); }
				jQuery(function($) {
					initLytebox();
				});
			}
		<?php endif;	
		
		if (isset($this->found['galleria'])) : 
			$galleria_theme = SHIBA_GALLERY_URL."/js/galleria/themes/classic/galleria.classic.min.js";
			$galleria_theme = apply_filters('galleria_theme', $galleria_theme);
			echo "Galleria.loadTheme('{$galleria_theme}');\n";
			
			foreach ( $this->galleria_option as $id => $option ) {
				// Write out the options array
				if (!empty($option)) {
					echo $this->general->write_array("galleriaOptions{$id}", $option);
//					echo "Galleria.configure(galleriaOptions{$id});";
				}	
				echo "Galleria.run('#galleria-{$id}', galleriaOptions{$id});\n";
			}
											 
		endif;
				
		if (isset($this->found['popeye'])) : ?>	
			jQuery(document).ready(function () {
				<?php 
					foreach ( $this->popeye_option as $id => $option ) {
						// Write out the options array
						echo $this->general->write_array("options{$id}", $option);
						echo "jQuery('#ppy'+'{$id}').popeye(options{$id});\n";
					}
				?>	
			});   
		<?php endif;

		if (isset($this->found['noobslide'])) :
		 endif;
		
		if (isset($this->found['tiny2'])) : ?>
//			jQuery(document).ready(function () {
			var tiny2 = new Array();
			<?php 
				foreach ( $this->tiny2_option as $id => $option ) { 
					echo "tiny2[$id] = new TINY.slider.slide('tiny2[$id]',{\n";	
					echo "id:'slider-{$id}',\n";
			  		echo "auto:{$option['auto']},\n";
			 		echo "resume:false,\n";
			  		echo "vertical:false,\n";
			  		echo "navid:'pagination-{$id}',\n";
			  		echo "activeclass:'current',\n";
			  		echo "position:0,\n";
			  		echo "rewind:false,\n";
			  		echo "elastic:false,\n";
					echo "responsive:{$option['responsive']},\n";	
					echo "aspect:{$option['aspect']},\n";	
					echo "crop:{$option['crop']},\n";	
					if (wp_is_mobile()) echo "slideInterval:.5,\n";
					if ($option['active'])
			  			echo "imgLink:'imglink-{$id}',\n";
			  		echo "left:'slideleft-{$id}',\n";
			  		echo "right:'slideright-{$id}'\n";
					echo "});\n";
				} // end foreach
			?>
//			});
		<?php endif;
		
		if (isset($this->found['tiny1'])) : ?>
			jQuery('.tinyslideshow').css('display','none');
			jQuery('.ts_wrapper').css('display','block');
			var slideshow = new Array();
			<?php 
				foreach ( $this->tiny1_option as $id => $option ) { 
					echo "slideshow[$id] = new TINY1.slideshow('slideshow[$id]');\n";
					// Need to set the height of the thumbnail area
					if (isset($option['thumbHeight']) && $option['thumbHeight']) {
						echo "jQuery('#ts_slideleft-{$id}').height({$option['thumbHeight']});\n";
						echo "jQuery('#ts_slideright-{$id}').height({$option['thumbHeight']});\n";
						echo "jQuery('#ts_slidearea-{$id}').height({$option['thumbHeight']});\n";
						echo "jQuery('#ts_slider-{$id}').height({$option['thumbHeight']});\n";
					}
				}
			?>	
			jQuery(document).ready(function () {
			<?php 
				foreach ( $this->tiny1_option as $id => $option ) { 
					echo "slideshow[$id].auto={$option['autoplay']};\n";
					if ($this->screen_w <= 480) {
						echo "slideshow[$id].infoSpeed=1;\n";
						echo "slideshow[$id].speed=3;\n";
						echo "slideshow[$id].imgSpeed=3;\n";
					}
//					else echo "slideshow[$id].speed=5;\n";
					echo "slideshow[$id].link='linkhover';\n";
					if ($option['caption']) echo "slideshow[$id].info='ts_information-{$id}';\n";
					else echo "slideshow[$id].info=false;\n";
					if (isset($option['thumbHeight']) && $option['thumbHeight']) {
						echo "slideshow[$id].thumbs='ts_slider-{$id}';\n";
						echo "slideshow[$id].left='ts_slideleft-{$id}';\n";
						echo "slideshow[$id].right='ts_slideright-{$id}';\n";
					} 
					echo "slideshow[$id].scrollSpeed=4;\n";
					echo "slideshow[$id].spacing=5;\n";
					echo "slideshow[$id].active='#fff';\n";
					echo "slideshow[$id].responsive ={$option['responsive']};\n";	
					echo "slideshow[$id].crop ={$option['crop']};\n";
					echo "slideshow[$id].aspect ={$option['aspect']};\n";					
					$imglink = ($option['active']) ? "ts_imglink-{$id}" : '';
					echo "slideshow[$id].init('tinyslideshow-{$id}','ts_image-{$id}','ts_imgprev-{$id}','ts_imgnext-{$id}','{$imglink}');\n";
				}
			?>	
			});
		<?php endif;

		if (isset($this->found['native2'])) : ?>	
			var native2 = new Array();
			jQuery(document).ready(function () {
				<?php 
					foreach ( $this->native2_option as $id => $option ) {
						// Write out the options array
						echo $this->general->write_array("options{$id}", $option);
						echo "native2[$id] = new shibaNative();\n";
						echo "native2[$id].init(options{$id})\n";
					}
				?>	
			});   
		<?php endif;


		
		?>   			 
		//]]>
		</script>
		<?php			
	}


	function menu_order_cmp($a, $b) {
		global $shiba_menu_order;

   		$pos1 = isset($shiba_menu_order[$a->ID]) ? $shiba_menu_order[$a->ID] : 0;
   		$pos2 = isset($shiba_menu_order[$b->ID]) ? $shiba_menu_order[$b->ID] : 0;

   		if ($pos1==$pos2)
       		return 0;
  		 else
      		return ($pos1 < $pos2 ? -1 : 1);
	}
	

	function process_attributes($post, $attr, $dtype) {
		
		$attr = apply_filters('shiba_gallery_attributes', $attr);
		
		if (isset($attr['type'])) {
			if (in_array($attr['type'], Shiba_Gallery::$thumb_types))
				if (!isset($attr['size']))
					$attr['size']='thumbnail';
			if (in_array($attr['type'], Shiba_Gallery::$thumb_types))
				if (!isset($attr['active']))
					$attr['active']= TRUE;
		} else {
			if (in_array($dtype, Shiba_Gallery::$thumb_types))
				if (!isset($attr['size']))
					$attr['size']='thumbnail';
		}
			
		// We're trusting author input, so let's at least make sure it looks like a valid 
		// orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}

		if (isset($attr['frame'])) 
			$attr['frame'] = $this->helper->translate_frame_name($attr['frame']);
			
		$default_values = apply_filters( 'shiba_gallery_defaults', 
			array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,

			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,

			'type' 		 => $dtype,
			'size' 		 => $this->options['size'], // 'medium',
			'tsize'		 => $this->options['tsize'], // 'auto',
			
			// whether to turn on CSS responsiveness
			'responsive' => $this->options['client_responsive'], 
			'frame'		 => $this->options['frame'],
			'caption'	 => $this->options['caption'],
			'cpos'	 	 => $this->options['cpos'], // Caption position
			'link' 		 => $this->options['link'],
			
			'crop'		 => $this->is_option('crop'), 
			'autoplay'	 => $this->is_option('autoplay'), // FALSE,
			'active'	 => $this->is_option('active'), // FALSE, // whether image is a hyperlink

			'include'    => '',
			'exclude'    => '',
			'post_type'  => '',
			'category'	 => '',
			'tag'		 => '',
			'tag_and'		 => '',
			'recent'	 => FALSE,
			'related'	 => FALSE,
			'page'		 => 1,
			'numberposts' => -1
	
		) );
//		extract(shortcode_atts($default_values, $attr));
		$attr = shortcode_atts($default_values, $attr);
		
		// Sanitize attribute values
		$attr['id'] = absint($attr['id']);
		$attr['order'] = esc_attr($attr['order']);
		$attr['orderby'] = esc_attr($attr['orderby']);
		
		// Tag attributes - only for native gallery
		$attr['itemtag'] = tag_escape($attr['itemtag']);
		$attr['captiontag'] = tag_escape($attr['captiontag']);
		$attr['icontag'] = tag_escape($attr['icontag']);
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $attr['itemtag'] ] ) )
			$attr['itemtag'] = 'dl';
		if ( ! isset( $valid_tags[ $attr['captiontag'] ] ) )
			$attr['captiontag'] = 'dd';
		if ( ! isset( $valid_tags[ $attr['icontag'] ] ) )
			$attr['icontag'] = 'dt';
		$attr['columns'] = intval($attr['columns']);
//		$attr['itemwidth'] = $attr['columns'] > 0 ? floor(100/$attr['columns']) : 100;
		$attr['float'] = is_rtl() ? 'right' : 'left';
		
		// Shiba Gallery attributes
		$attr['type'] = esc_attr($attr['type']);
		$attr['size'] = esc_attr($attr['size']);
		$attr['tsize'] = esc_attr($attr['tsize']);
		$attr['responsive'] = esc_attr($attr['responsive']);
		if (!in_array($attr['responsive'], array('none', 'aspect', 'width')))
			$attr['responsive'] = 'aspect';									 
		$attr['link'] = esc_attr($attr['link']);
		$attr['caption'] = esc_attr($attr['caption']);
		$attr['cpos'] = esc_attr($attr['cpos']);

		// Content attributes
		$attr['post_type'] = esc_attr($attr['post_type']);
		$attr['category'] = esc_attr($attr['category']);
		$attr['tag'] = esc_attr($attr['tag']);
		$attr['tag_and'] = esc_attr($attr['tag_and']);
		$attr['page'] = absint($attr['page']);
		$attr['numberposts'] = intval($attr['numberposts']);

		// Boolean attributes
		$attr['crop'] = (bool)$attr['crop'];
		$attr['autoplay'] = (bool)$attr['autoplay'];
		$attr['active'] = (bool)$attr['active'];
		$attr['recent'] = (bool)$attr['recent'];
		$attr['related'] = (bool)$attr['related'];

		$type = $attr['type'];
		$attr['loadtype'] = $type;
		// Reassign deprecated gallery types
		switch ($type) {
		case 'lightbox':
		case 'slimbox':
		case 'lytebox':
			$attr['link'] = $type;
			$attr['loadtype'] = 'native2';
			break;
		case 'navlist':
			$attr['loadtype'] = 'native2';
			break;
		case 'native':
			$attr['loadtype'] = 'native2';
			break;
		case 'smoothgallery':
		case 'pslides':
		case 'tiny':
			$attr['type'] = $attr['loadtype'] = 'tiny2';
			break;
		case 'slideviewer':	
			$attr['type'] = $attr['loadtype'] = 'noobslide_slideviewer';
			break;
		case 'nativex':	
			$attr['type'] = $attr['loadtype'] = 'noobslide_nativex';
			break;
		default:
			break;
		}
	
		if ( 'RAND' == $attr['order'] )
			$attr['orderby'] = 'none';
		
		return $attr;	
	}
	
	/**
	 * Shiba Gallery Shortcode function.
	 *
	 * Borrows from the native gallery_shortcode function in wp-includes/media.php.
	 *
	 * @param array $attr Attributes attributed to the shortcode.
	 * @return string HTML content to display gallery.
	 */
	function parse_shiba_gallery($attr, $dtype) {
		static $use_default = FALSE;
		$output = '';
		$post = get_post();		

		$attr = $this->process_attributes($post, $attr, $dtype);
		extract($attr);

		$pos = strpos($type, '_');
		$noobnum = 1;
		if ( $pos !== FALSE) { // get noobslide number
			$old_type = $type;
			$loadtype = $type = substr($type, 0, $pos);
			$noobnum = substr($old_type, $pos+1);
		}

		// if size is custom - array(width, height) - then convert the string into an array
		if (strpos($size, '(') !== FALSE) {
			// convert size to array
			$size = explode(',', $this->general->substring($size, '(', ')') ); 
		}	
		if (is_array($size)) if (count($size) != 2) $size = 'medium';
		if (!is_array($size)) $crop = FALSE;
		
		$old_size = $size;
		if (is_object($this->responsive)) {
			$args = compact('tsize', 'link', 'caption', 'type', 'frame', 'autoplay', 'columns');
			$size = $this->responsive->responsive_gallery($size, $args);
			// Check if client responsive
			if (($responsive != 'width') && ($responsive != 'aspect'))
				$old_size = $size;
			extract($args);
		}
		// This has to be here because the number of columns may change after responsive.
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		
		if (strpos($tsize, '(') !== FALSE) {
			// convert size to array
			$tsize = explode(',', $this->general->substring($tsize, '(', ')') ); 
		}	
		if (is_array($tsize)) if (count($tsize) != 2) $tsize = 'auto';
		
		// 'post_mime_type' => 'image',
		// sort menu order later
		$args = array( 	'post_status' => 'publish',
						'post_type' => $post_type, 
						'order' => $order, 
						'orderby' => $orderby,
						'numberposts' => $numberposts );

		if ($id && ($orderby == 'shiba_menu_order')) { unset($args['orderby']); unset($args['order']); }

		// add paging
		if (($numberposts > 0) && ($page > 1)) {
			$offset = ($page -1) * $numberposts;
			$args['offset'] = $offset;
		}
						
		if ($post_type == 'attachment') $args['post_status'] = NULL;
		elseif ($post_type == 'any') $args['post_status'] = 'any';
		
		if ( !empty($include) ) {
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$args['include'] = $include;
			if (!$post_type) { $args['post_type'] = 'any';  $args['post_status'] = 'any'; }
			$_attachments = get_posts( $args );
			
			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} else {
			if ( !empty($exclude) ) {
				$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
				$args['exclude'] = $exclude;
			}	
			if ($category) :
				$category = preg_replace( '/[^0-9,]+/', '', $category );		
				$args['category'] = $category;
				if (!$post_type) $args['post_type'] = 'post'; 
				$attachments = get_posts( $args );			 
	
			elseif ($tag) :
				$args['tag'] = $tag;
				if (!$post_type) { $args['post_type'] = 'any'; $args['post_status'] = 'any'; } 
				$attachments = get_posts( $args );			 
			
			elseif ($tag_and) :
				// convert it into an array
				$args['tag_slug__and'] = explode(',',$tag_and);
				if (!$post_type) { $args['post_type'] = 'any'; $args['post_status'] = 'any'; } 
				$attachments = get_posts( $args );			 
			
			elseif ($related) : 
				// works with the YARPP plugin to retrieve related posts and put it in a gallery
				global $yarpp, $wp_filter;

				// MUST save state of global $wp_filter because we are currently in 
				// a $wp_filter loop
				$state = key($wp_filter['the_content']);

				if (is_object($yarpp) && is_object($yarpp->cache))  
					$attachments = $this->get_yarpp_related_current($post, $numberposts, $args);
				elseif (function_exists('yarpp_cache_enforce'))  
					$attachments = $this->get_yarpp_related_old($post, $numberposts, $args);					
				else return "<div>No related results - YARPP plugin not installed.</div>\n";
				
				if (!empty($attachments)) {
					$output = "<div class='alignspace'></div>\n";
					$output .= "<h2>Related Articles</h2>\n";
				}

				// Restore wp_filter array back to its previous state
				reset($wp_filter['the_content']);			
				while(key($wp_filter['the_content']) != $state)
					next($wp_filter['the_content']);
										
			elseif ($recent) :
				if (!$post_type) $args['post_type'] = 'post';
				else $args['post_type'] = $post_type;
				$args['post_status'] = 'publish';
				$args['order'] = 'DESC';
				$args['orderby'] = 'post_date';
				$attachments = get_posts( $args );			 
			
			else :
				$args['post_parent'] = $id;
				if (!$post_type)  { $args['post_type'] = 'any'; $args['post_status'] = 'any'; } 			
				$attachments = get_children( $args );			 
			endif;	
		}
		
		if ( empty($attachments) )
			return "<div class='no-images-found'></div>";


		// Sort menu_order here
		if ($id && ($orderby == 'shiba_menu_order')) { 
			global $shiba_menu_order;
			$shiba_menu_order = get_post_meta($id, '_menu_order', TRUE);
			if (is_array($shiba_menu_order)) {
				usort($attachments, array($this, 'menu_order_cmp')); 
				if ($order == 'DESC')
					$attachments = array_reverse($attachments);
				unset($shiba_menu_order);
			}	
		}	
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $attachment ) {
				$feed_link = wp_get_attachment_link($attachment->ID, "thumbnail", true);
				if (strpos($feed_link, "Missing Attachment") === FALSE)
					$output .= $feed_link . "\n";
			}	
			return $output;
		}
	
	
		$args = compact('type', 'id', 'size', 'old_size', 'tsize', 'link', 'itemtag', 'captiontag', 'caption', 'cpos', 'active', 'responsive', 'icontag', 'columns', 'itemwidth', 'float','frame', 'autoplay', 'crop');  
	
		$this->found[$loadtype] = TRUE;
		$this->load_gallery($loadtype);
		switch ($type) {
		case 'tiny1':	
			$output .= $this->tiny1->render($attachments, $args);
			break;	
		case 'tiny2':	
			$output .= $this->tiny2->render($attachments, $args);
			break;	
		case 'lightbox':
		case 'lytebox':
		case 'slimbox':
		case 'navlist':
		case 'native':		
		case 'native2':
			$output .= $this->native2->render($attachments, $args);
			break;
		case 'popeye':
//			$this->found['popeye'] = TRUE;	
			$output .= $this->popeye->render($attachments, $args);
			break;	
		case 'noobslide':
			$output .= $this->noobslide->render($attachments, $args, $noobnum);
			break;	
		case 'galleria':
//			$this->found['galleria'] = TRUE;	
			$output .= $this->galleria->render($attachments, $args);
			break;
		default:
			// gallery type not found rerender using default gallery
			if ($use_default) { // no such default gallery use noobslide_thumb
				$this->options['default_gallery'] = $attr['type'] = 'noobslide_thumb';
			} else $attr['type'] = $this->options['default_gallery'];
			$use_default = TRUE;
			
			$output .= $this->parse_shiba_gallery($attr, $this->options['default_gallery']);
			break;	
		}

		if ($this->is_option('post_gallery'))
			$this->found[$this->options['post_gallery_type']] = TRUE;

		// Update slimbox/lytebox options
		switch ($link) {
		case 'lightbox':
			$this->found['lightbox'] = TRUE;
			$this->lightNum++;
			break;
		case 'slimbox':
			$this->found['slimbox'] = TRUE;
			$this->lightNum++;
			break;
		case 'lytebox':
			$this->found['lytebox'] = TRUE;
			$this->lightNum++;
			break;
		}
		return $output;
	}

	function yarpp_distinct($distinct, $query) {
		return 'DISTINCT';
	}

	function yarpp_where($where, $query) {
		global $wpdb;
		if (strpos($where, 'yarpp') === FALSE) return $where;
		if (isset($query->query_vars['post__not_in']) && !empty($query->query_vars['post__not_in'])) {
			$post__not_in = implode(',',  array_map( 'absint', $query->query_vars['post__not_in'] ));
			$where .= " AND {$wpdb->posts}.ID NOT IN ($post__not_in)";		
		}
//		$where = str_replace("AND {$wpdb->posts}.post_type = 'page'", "AND {$wpdb->posts}.post_type IN ('post', 'page')", $where);
		$where = str_replace("AND {$wpdb->posts}.post_type = 'page'", '', $where);
		$where = str_replace("AND {$wpdb->posts}.post_type = 'post'", '', $where);
		return $where;
	}


	function get_yarpp_related_current($post, $numberposts, $args) {
		global $yarpp;

		$cache_status = $yarpp->cache->enforce($post->ID);
		$yarpp->cache->begin_yarpp_time($post->ID);

		add_filter('posts_distinct', array($this, 'yarpp_distinct'), 10, 2);
		add_filter('posts_where', array($this, 'yarpp_where'), 50, 2);
		$related_args = array( 	'p' => $post->ID,
								'order' => 'DESC',
								'orderby' => 'score',
								'post_type' => 'any', //array('page', 'post'),
								'suppress_filters' => FALSE,
								'posts_per_page' => $numberposts,
								'showposts' => $numberposts );
		if (isset($args['exclude']) && $args['exclude']) $related_args['post__not_in'] = wp_parse_id_list($args['exclude']);
								
		$related_query = new WP_Query();
		$related_query->query($related_args);

		$attachments = $related_query->posts;
		remove_filter('posts_distinct', array($this, 'yarpp_distinct'), 10, 2);
		remove_filter('posts_where', array($this, 'yarpp_where'), 10, 2);

		$yarpp->cache->end_yarpp_time(); // YARPP time is over... :(
		return $attachments;
	}
	
	function get_yarpp_related_old($post, $numberposts, $args) {
		global $yarpp_time, $yarpp_cache, $wp_filter;
		
		if (is_object($yarpp_cache)) {				
			$yarpp_cache->begin_yarpp_time($post->ID); // get ready for YARPP TIME!
			yarpp_cache_enforce($post->ID);
		} else {
			$yarpp_time = TRUE; // get ready for YARPP TIME!		
			yarpp_cache_enforce(array('post'),$post->ID);
		}	
		add_filter('posts_distinct', array($this, 'yarpp_distinct'), 10, 2);
		$related_args = array( 	'p' => $post->ID,
								'order' => 'DESC',
								'orderby' => 'score',
								'post_type' => array('page', 'post'),
								'suppress_filters' => FALSE,
								'posts_per_page' => $numberposts,
								'showposts' => $numberposts );
		if ($args['exclude']) $related_args['post__not_in'] = wp_parse_id_list($args['exclude']);

		$related_query = new WP_Query();
		$related_query->query($related_args);

		$attachments = $related_query->posts;
		remove_filter('posts_distinct', array($this, 'yarpp_distinct'), 10, 2);

		if (is_object($yarpp_cache))				
			$yarpp_cache->end_yarpp_time(); // YARPP time is over. :(
		else $yarpp_time = FALSE;

		return $attachments;
	}
	
	
	function parse_gallery_shortcode($output, $attr) {
		// get options
		$output .= $this->parse_shiba_gallery($attr, $this->options['default_gallery']);
		return $output;
	} 
	
	
	// Add proper link for post_gallery
	function add_post_gallery_link($id, $content) {
//		if (!$this->is_option('post_gallery')) return;
		
		// Remove any previous links from content
		$content = preg_replace("/\<a(.*)\>(.*)\<\/a\>/iU", "$2", $content);
//		preg_replace("/\<a([^>]*)\>([^<]*)\<\/a\>/i", "$2", $content);
		// Add in our own link
		$image = get_post($id);
		if (!$image) return $content;
		
		$num = $this->lightNum;
		$this->lightNum = 0;
		$content = $this->helper->get_attachment_link($image, $this->options['post_gallery_type'], 'title', $content);
		$this->lightNum = $num;
		return $content;
	}
	
	/**
	 * From wp-includes/media.php
	 */
	function gallery_caption_shortcode($output, $attr, $content) {
		$caption_type = '';
		$imgID = 0;
		
		// Check if image frames are set and if content contains img
		if (strpos($content, '<img') !== FALSE) {
			$caption_type = 'image';
			$content = str_replace(array('<br />'),'',$content);
		}
		
		// Check if content contains gallery
		if (strpos($content, '[gallery') !== FALSE) $caption_type = 'gallery';
		if (!$caption_type) return $output;
		
		extract(shortcode_atts(array(
			'id'	=> '',
			'align'	=> 'alignnone',
			'width'	=> '',
			'frame' => $this->options['frame'],
			'caption' => ''
		), $attr));
		$align = esc_attr($align);
		$frame = $this->helper->translate_frame_name($frame);

		if (strpos($id, 'attachment_') !== FALSE) {
			$id = str_replace('attachment_', '', $id);
			$imgID = absint($id);
			
			if (is_object($this->responsive)) {
				$args = compact('id', 'frame', 'width', 'content');
				$args = $this->responsive->responsive_image($args);
				extract($args);
			}
		}
		
		$owidth = $width + $this->helper->get_frame_width($frame); 
		if ( 10 > $width ) $owidth = $width = 'auto';
		else {
			$width = $width. 'px';
			$owidth = $owidth . 'px';
		}

		if ($this->is_option('post_gallery') && ($caption_type == 'image'))
			$content = $this->add_post_gallery_link($imgID, $content);
			
		if (!$this->is_option('image_frame') && ($caption_type == 'image'))
			$caption_type = 'wordpress';	
	

		if ( $id ) $id = "id='{$id}'";
		$captionStr = '';
		switch ($caption_type) {	
		case 'image':
			$captionStr .= "<div {$id} class='wp-caption {$frame} {$align}' style='width:{$owidth}' >";
			$captionStr .= "<div class='shiba-outer shiba-gallery' >";
			$captionStr .= "<div class='shiba-stage' style='width:{$width}'>";
			$captionStr .= $content;
			$captionStr .= "<div class='wp-caption-text shiba-caption'>{$caption}</div>";
			$captionStr .= "</div> <!-- End shiba-stage -->";
			$captionStr .= "</div></div>";
			break;
		case 'gallery':	 
			$captionStr .= 	"<div {$id} class='shiba-gallery-caption {$align}' style='width:{$owidth}; font-size:100%;' >";
			$captionStr .= do_shortcode( $content );
			$captionStr .= "<p class='shiba-gallery-caption-text'>{$caption}</p>";
			$captionStr .= "</div>";
			break;
		case 'wordpress':
		default:
			$captionStr .= "<div {$id} class='wp-caption {$align}' style='width:{$owidth}' >";
			$captionStr .= $content;
			$captionStr .= "<p class='wp-caption-text'>{$caption}</p>\n";
			$captionStr .= "</div>";
			break;
		}
		return $captionStr;	
	}

	function is_option($option) {
		return (isset($this->options[$option]) && $this->options[$option]);		
	}

} // end class
endif;


global $shiba_gallery;
if (class_exists("Shiba_Gallery") && !$shiba_gallery) {
    $shiba_gallery = new Shiba_Gallery();	
}	
?>