<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


if (!class_exists("Shiba_Gallery_Responsive")) :

class Shiba_Gallery_Responsive {
	
	function __construct() { 
		wp_enqueue_script('shiba-cookies', SHIBA_GALLERY_URL.'/js/cookies.js', array(), '1.1', FALSE);
		// This action *must* be after 10 so that the cookies.js file gets enqueued first
		add_action( 'wp_head', array($this, 'get_screen_res'), 11);
	}

	function get_screen_res() { 
		global $shiba_screen_w;
		
		if (isset($shiba_screen_w)) return;
		?>
        <script type="text/javascript">
		//<![CDATA[
		var w = screen.width; // screen.width | window.innerWidth | jQuery(window).width()
		var c = new cookie('shiba_screen_w', w, 365); 
		if (c.read() != w) { 
			c.set();
			if (document.cookie.indexOf("shiba_screen_w")!=-1) {
				location.reload();
			}
		}
		//]]>
		</script>
		<?php
		if (!isset($_COOKIE['shiba_screen_w'])) $shiba_screen_w = SHIBA_MAX_SCREEN_W;
		else $shiba_screen_w = absint($_COOKIE['shiba_screen_w']); 
	}

	function responsive_gallery($size, &$args) {
		global $shiba_gallery;
		
		$screen_w = $shiba_gallery->screen_w;
		if (!$screen_w) return $size;
//		if ($size == 'thumbnail') return $size;
		
		if ($screen_w >= SHIBA_MAX_IMAGE_W) return $size;

		extract($args);
		// Estimate padding - 6% each side
		$padding = 0.12 * $screen_w; // 0.085 * $screen_w;
		$frame_w = $shiba_gallery->helper->get_frame_width($frame);
		$new_w = absint($screen_w - ($padding + $frame_w));
		$csize = $shiba_gallery->helper->convert_size($size);

		if ($frame == 'frame4') // shadow frame
			if ($type != 'native')
				$new_w -= 20;
		
		$args['link'] = 'file';

		switch($type) {
		case 'noobslide_6':
			$new_w -= 60; $args['tsize'] = 'small';
			break;
		case 'tiny2':
			if ($args['tsize'] != 'none')
				$args['tsize'] = 'small';
			break;
		case 'lytebox':	
		case 'lightbox':	
		case 'slimbox': // reduce the size
		 	$m = $screen_w / SHIBA_MAX_IMAGE_W;
			$csize[0] = absint($m*$csize[0]);
			$csize[1] = absint($m*$csize[1]);
			return $csize;
			break;
		case 'native':
			if ($caption != 'none')
				if ($columns > 2) $args['columns'] = $columns = 2;
			$new_w += $frame_w;
			$new_w = $new_w/$columns;
									
			// reduce by frame width + a little in-between space
			$new_w = absint($new_w - $frame_w - 8);
			if ($frame == 'frame4')
				$new_w -= 10;
			break;
		case 'galleria':
			$args['active'] = false;
			$args['autoplay'] = false;
			break;
		case 'nativex':
		case 'noobslide_nativex':
			$args['caption'] = 'none';
		case 'noobslide_4':
		case 'noobslide_8':
			$pimg_size = array();
			$csize = $shiba_gallery->helper->get_panel_size($size, $pimg_size);
			break;
		}
		if (is_array($csize) && ($csize[0] < $new_w)) return $size;
		
		// Handle aspect ratio, don't want things to be too skewed
		if (is_array($size)) {
			// User specified aspect ratio - preserve it
			if ($size[1] <= 0) $new_h = $size[1];
			else
				$new_h = absint($size[1]/$size[0] * $new_w);
		} else {
			// Standard WP size - set aspect ratio to one
			$new_h = $new_w;
		}
		return array($new_w, $new_h); 
	}
	
	function responsive_image($args) {
		global $shiba_gallery;
		
		$screen_w = $shiba_gallery->screen_w;
		if (!$screen_w) return $args;
		if ($screen_w >= SHIBA_MAX_IMAGE_W) return $args;

		extract($args);
		// Estimate padding
		$padding = 0.085 * $screen_w;
		$frame_w = $shiba_gallery->helper->get_frame_width($frame) * 2;
		$new_w = absint($screen_w - ($padding + $frame_w));

		if ($width < $new_w) return $args;
		$width = $new_w;
		if ($id) {
			if (get_post_type($id) == 'attachment') {
				// Get old image
				$old_img = $shiba_gallery->general->substring($content,'<img','>');
				if (!$old_img) break;
				// Replace just the source url
				$old_img = $shiba_gallery->general->substring($old_img,'src="','"');
				$img = wp_get_attachment_image_src($id, array($width, 900) );
				
/*				
				$old_img = '<img' . $old_img . '>';
				// Change image depending on width
				$img = wp_get_attachment_image( $id, array($width, 900) );
*/				if ($img)
					$content = str_replace($old_img, $img[0], $content);
			}
		}
		$args = compact(/*'id', 'frame', 'width',*/ 'content');
		return $args;
	}
}// end class
endif;
?>