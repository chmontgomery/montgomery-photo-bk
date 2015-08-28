<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Options")) :

class Shiba_Gallery_Options {
	var $pagehook, $page_id, $settings_field, $options;
	
	function __construct() {
		$this->page_id = 'shiba_gallery';
		// This is the get_options slug used in the database to store our plugin option values.
		$this->settings_field = 'shiba_gallery_options';
		
		add_action('admin_init', array($this,'admin_init'), 20 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function admin_init() {
		global $shiba_gallery;
		
		register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options') );
		add_option( $this->settings_field, Shiba_Gallery::$default_options );
		$this->options = $shiba_gallery->options; // get_option( $this->settings_field );
	}
	
	function admin_menu() {
		if ( ! current_user_can('switch_themes') )
			return;
			
		// Add a new submenu
		$this->pagehook = $page = add_media_page(
			__('Shiba Gallery', 'shiba_gallery'), __('Shiba Gallery', 'shiba_gallery'), 
			'administrator', $this->page_id, array($this,'render') );
		

		// Executed on-load. Add all metaboxes.
		add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );

		add_action("admin_print_scripts-$page", array($this, 'js_includes'));
//		add_action("admin_head-$page", array($this, 'header_style') );
//		add_action("admin_head-$page", array($this, 'js'), 50);
	}

	function js_includes() {
		wp_enqueue_media();
		wp_enqueue_script('shiba-gallery-admin', SHIBA_GALLERY_URL.'/js/shiba-gallery-admin.min.js', array( ), CHILD_THEME_VERSION, true);
		// Needed to allow metabox layout and close functionality.
		wp_enqueue_script( 'postbox' );
	}

	function header_style() { ?>
		<style>
		</style>
	<?php }


	/*
		Sanitize our plugin settings array as needed.
	*/	
	function sanitize_theme_options($options) {
//		$options['example_text'] = stripcslashes($options['example_text']);
		return $options;
	}


	/*
		Settings access functions.
		
	*/
	protected function get_field_name( $name ) {

		return sprintf( '%s[%s]', $this->settings_field, $name );

	}

	protected function get_field_id( $id ) {

		return sprintf( '%s[%s]', $this->settings_field, $id );

	}

	protected function get_field_value( $key ) {

		return $this->options[$key];

	}
	

	function render() {
		global $shiba_gallery;

//		$messages[1] = __('Shiba gallery settings updated.', 'shiba_gallery');
//		$messages[2] = __('Shiba gallery settings failed to update.', 'shiba_gallery');
//		$messages[3] = __('Shiba gallery default image updated.', 'shiba_gallery');
//		$messages[4] = __('Shiba gallery default image removed.', 'shiba_gallery');
		
		if ( isset($_GET['message']) && (int) $_GET['message'] ) {
			$message = $messages[$_GET['message']];
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
		}
		
				
		$title = __('Shiba gallery Options', 'gallery_options');
		?>
        <div class="wrap">   
        <?php screen_icon(); ?>
        <h2><?php echo esc_html( $title ); ?></h2>
    
        <?php
            if ( !empty($message) ) : 
            ?>
            <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
            <?php 
            endif; 
            $options = $this->options;
            if (!is_array($options)) $options = array();
        ?>
    
        <form name="shiba-gallery_options" id="shiba-gallery_options" method="post" action="options.php" class="">
            <p>
            <input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
            </p>
            
            <div class="metabox-holder">
                <div class="postbox-container" style="width: 99%;">
                <?php 
                    // Render metaboxes
                    settings_fields($this->settings_field); 
                    do_meta_boxes( $this->pagehook, 'main', null );
                    if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) )
                        do_meta_boxes( $this->pagehook, 'column2', null );
                ?>
                </div>
            </div>

            <p>
            <input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
            </p>
            

        </form>
        <!-- Needed to allow metabox layout and close functionality. -->
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
    </div>
	<?php }

	function metaboxes() {

		add_meta_box( 'shiba-gallery-version', __('Set Default Image', 'shiba_gallery' ), array( $this, 'info_box' ), $this->pagehook, 'main', 'high' );
		
		add_meta_box( 'shiba-gallery-default-settings', __('Gallery Default Settings', 'shiba_gallery' ), array( $this, 'gallery_default_settings' ), $this->pagehook, 'main' );

		add_meta_box( 'shiba-gallery-other-settings', __('Other Gallery Settings', 'shiba_gallery' ), array( $this, 'other_gallery_settings' ), $this->pagehook, 'main' );

	}


	/*
		Render metabox controls
	*/
	function checkbox($id, $label, $small) { ?>
		<p>
        	<input type="checkbox" name="<?php echo $this->get_field_name($id); ?>" <?php if (isset($this->options[$id]) && $this->options[$id]) echo 'checked';?>/>  
			<?php _e( $label, 'shiba_gallery' ); ?>
<!--            
			<label for="<?php echo $this->get_field_id($id); ?>">
				<?php _e( $label, 'shiba_gallery' ); ?>
            </label>
-->            
        </p>
        
        <?php 
		if ($small) { ?>
        	<small><?php echo $small;?></small>
        <?php }
	}

	function option_metabox($id, $label, $values, $default) {
		global $shiba_gallery;		 
		 
		if (isset($this->options[$id]))
			$selected = $this->options[$id];
		else
			$selected = $default;	
		 
		?>
        <p>
        <label for="<?php echo $this->get_field_id($id); ?>">
            <?php echo $label;?>
        </label>
		<select name='<?php echo $this->get_field_name($id);?>' id='<?php echo $this->get_field_id($id);?>'>
			<?php 
//			echo $shiba_gallery->general->write_option('None', 0, $selected);
			foreach ($values as $value => $title) {
				echo $shiba_gallery->general->write_option($title, $value, $selected);
			}
		   ?>
		</select>
        </p>
		<?php
	}

	function info_box() { ?>
    
		<p><strong><?php _e( 'Version:', 'shiba_gallery' ); ?></strong> <?php echo SHIBA_GALLERY_VERSION; ?> <?php echo '&middot;'; ?> <strong><?php _e( 'Released:', 'shiba_gallery' ); ?></strong> <?php echo SHIBA_GALLERY_RELEASE_DATE; ?></p>

        <p>Please pick which image you want to use as a default thumbnail when a gallery post or page does not have an assigned thumbnail/featured image.</p>
        
        <p>
        <?php 
			$url = '';
			if (isset($this->options['default_image']) && $this->options['default_image']) { 
            	$img = wp_get_attachment_image_src($this->options['default_image'], array(200, 200));
				$url = $img[0];
			}
			?>
			<div id="shiba-gallery-default-image">
				<img src="<?php echo $url;?>" style="max-width:200px;max-height:200px;"/>
			</div>    
        </p>


        <?php
            $modal_update_href = esc_url( add_query_arg( array(
                'page' => 'shiba_gallery',
                '_wpnonce' => wp_create_nonce('shiba_gallery_options'),
            ), admin_url('upload.php') ) );
        ?>
        
        <p>
        <!-- Or we can include insert-media class, which is what happens in edit post screen -->
        <a id="choose-default-image" href="#"><?php _e( 'Set default image', 'shiba_gallery' ); ?></a> |
        <a id="unset-default-image" href="#"><?php _e( 'Unset Default Image', 'shiba_gallery' ); ?></a>
        </p>

        <input name="<?php echo $this->get_field_name( 'default_image' ); ?>" id="<?php echo $this->get_field_id( 'default_image' ); ?>" type="hidden" value="<?php echo esc_attr( $this->get_field_value( 'default_image' ) ); ?>"/>
            

		<?php
	}


	function default_text() { ?>
        <label for="<?php echo $this->get_field_id( 'default_gallery' ); ?>"><?php _e( 'Gallery Type', 'shiba_gallery' ); ?></label>
        <input type="text" size="35" id="<?php echo $this->get_field_id( 'default_gallery' ); ?>" name="<?php echo $this->get_field_name( 'default_gallery' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'default_gallery' ) ); ?>">
        
        <p>
        <small>Please enter which gallery type to want to use as the default. Current gallery types include - tiny, slimbox, popeye, native, and a variety of noobslide galleries.<br/>
        Noobslide gallery types include - 1 through 8, galleria, slideviewer, thumb, nativex. Specified as noobslide_1, noobslide_galleria, etc.</small>
        </p>


        <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Gallery Size', 'shiba_gallery' ); ?></label>
        <input type="text" size="10" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'size' ) ); ?>">
        
        <p>
        <small>Valid sizes - thumbnail, medium, large or the width and height of the gallery, e.g. (300, 300).</small>
        </p>


        <label for="<?php echo $this->get_field_id( 'tsize' ); ?>"><?php _e( 'Thumbnail Size', 'shiba_gallery' ); ?></label>
        <input type="text" size="10" id="<?php echo $this->get_field_id( 'tsize' ); ?>" name="<?php echo $this->get_field_name( 'tsize' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'tsize' ) ); ?>">
        
        <p>
        <small>None (for no thumbnails) or the width and height of the thumbnails, e.g. (30, 30).</small>
        </p>
		
	<?php }
	
	
	function default_frame() { 
		global $shiba_gallery;
		
		$selected = (isset($this->options['frame'])) ? $this->options['frame'] : 'frame4';
		?>
        <p>
            <label for="<?php echo $this->get_field_id('frame'); ?>">
                <?php _e('Frame', 'shiba_gallery');?>
            </label>
            <?php $shiba_gallery->helper->render_frame_options($this->get_field_name('frame'), $selected); 
?>
        </p>
    	<?php $this->checkbox('image_frame', 'Use default frame on images.', ''); ?>
    
    <?php }
	
	
	function gallery_default_settings() {
		// Type, Size, Tsize
		$this->default_text();

		// Default client responsive
		$this->option_metabox(
			'client_responsive', __( 'Client Responsive', 'shiba_gallery' ), 
			array("none" => "None",
				  "width" => "Width",
				  "aspect" => "Aspect"),
			'aspect');

		$this->default_frame();
		
		// Default caption
		$this->option_metabox(
			'caption', __( 'Caption', 'shiba_gallery' ), 
			array("none" => "None",
				  "title" => "Title",
				  "description" => "Description"),
			'title');
		// Default link
		$this->option_metabox(
			'link', __( 'Link', 'shiba_gallery' ), 
			array("none" => "None",
				  "file" => "File",
				  "attachment" => "Attachment",
				  "lightbox" => "LightBox",
				  "slimbox" => "SlimBox",
				  "lytebox" => "LyteBox"),
			'file');
		// Caption position
		$this->option_metabox(
			'cpos', __( 'Caption Position', 'shiba_gallery' ), 
			array("bottom" => "Bottom",
				  "top" => "Top",
				  "left" => "Left",
				  "right" => "Right"),
			'file');
 		
		// Autoplay, Crop
		$this->checkbox('crop', 'Crop.', '');
		$this->checkbox('autoplay', 'Autoplay.', 'If checked, galleries will autoplay by default.');
		$this->checkbox('active', 'Active.', 'Images are active. When clicked on, users will be redirected to image link.');
	}
	
	function other_gallery_settings() {
		$this->checkbox('responsive', 'Enable server responsive galleries and images.', 'Allows galleries and images to be resized based in device screen width. This is done with the use of javascript & cookies.');
/*		$this->checkbox('html5', 'Using html5 markup.', 'Allows galleries to be rendered properly using html5 markup.'); */
		$this->checkbox('post_gallery', 'Make post gallery.', 'Include all captioned images in the post as a slimbox gallery.');
		
				// Default link
		$this->option_metabox(
			'post_gallery_type', __( 'Post Gallery Type', 'shiba_gallery' ), 
			array("lightbox" => "LightBox",
				  "slimbox" => "SlimBox",
				  "lytebox" => "LyteBox"),
			'lightbox');

	}
	
	
} // end Shiba_Media_Library_Options class
endif; 

?>