<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Helper")) :

class Shiba_Gallery_Helper {
	
	var $thumb_ms = array(54,54);
	var $thumb_ls = array(108,108);
	var $thumb_ss = array(32,32);

	var $panel_ms = array(480,240);
	var $panel_ls = array(580,300);
	var $panel_ss = array(240,150);


	var $pimg_ms = array(150,150);
	var $pimg_ls = array(250,250);
	var $pimg_ss = array(54,54);

	
	function get_panel_text($image, $size) {
		$length =  absint(($size[0]*0.5/14) * ($size[1]/14));		
		$description = $this->get_attachment_description($image, $length);
		return $description;
	}
	
		
	function get_panel_size($size, &$pimg_size) {

		if (empty($size)) $size = 'medium';
		if (is_array($size)) {
			if ($size[0] <= 0) $size = 'medium';
			elseif ($size[1] <= 0) $size[1] = absint(0.7 * $size[0]); 
		}
		if (is_string($size)) {
			switch ($size) {
			case 'thumbnail':
				$size = $this->panel_ss;
				$pimg_size = $this->pimg_ss;
				break;
			case 'large':
				$size = $this->panel_ls;
				$pimg_size = $this->pimg_ls;
				break;
			case 'medium':
			default:	
				$size = $this->panel_ms;
				$pimg_size = $this->pimg_ms;
				break;
			}
		} else {
			// Panel image size
			$pimg_size = array(absint($size[0]*0.5), absint($size[1]*0.5));

		}
		return $size;		
	}
	
	function get_gallery_size($images, $size, $old_size, &$all_img, $crop = FALSE) {
		$old_size = $this->convert_size($old_size);
		if (is_array($size)) {
			$tmp_size = array();
	
			$tmp_size[0] = ($size[0] < 0) ? 900 : $size[0];
			$tmp_size[1] = ($size[1] < 0) ? 900 : $size[1];
		} else $tmp_size = $size;
		
		// Check for cropping
		if ($crop)
			if ($tmp_size[0] > $tmp_size[1]) $tmp_size[1] = $tmp_size[0];
			else $tmp_size[0] = $tmp_size[1];
		
		if (!$all_img) $all_img = array();
		$maxW = 0; $maxH = 0;
		foreach ( $images as $image ) {		
			$img = $this->get_attachment_image_src($image->ID, $tmp_size);
			$w = intval($img[1]); $h = intval($img[2]);
			if ($w > $maxW) $maxW = $w;
			if ($h > $maxH) $maxH = $h;
			$all_img[] = $img;
		}
	
		if ($crop)
			return $old_size; // $size;
		
		if (is_array($size)) {
			$size_arr = array();
			$size_arr[0] = ($size[0] < 0) ? $maxW : $size[0];
			$size_arr[1] = ($size[1] < 0) ? $maxH : $size[1];
		} else 
			$size_arr = array($maxW, $maxH);
			
		// Adjust old_size based on aspect ratio of size_arr
		$w = $old_size[0];
		$h = $size_arr[1]/$size_arr[0] * $old_size[0];
		return array($w, $h); // $size_arr;	
	}
	
	function get_image_style($img, $crop, $maxW, $maxH) {
		global $shiba_gallery;
		// Deal with the case where both w < maxW and h < maxH - 
		// then expand image on one side
		$dw = $img[1] - $maxW;
		$dh = $img[2] - $maxH;
		if  (($dw < 0) && ($dh < 0)) {
			$ratio = $img[1]/$img[2];
			if ($dw >= $dh) { 
				$img[1] = $maxW; 
				$img[2] = $img[1]/$ratio;
			} else {
				$img[2] = $maxH;
				$img[1] = $ratio * $img[2];
			}
		}

		// padding for main image
		$left_pad = ($maxW>$img[1]) ? intval(ceil(($maxW-$img[1]) *0.5)) : 0;
		$top_pad = ($maxH>$img[2]) ? intval(ceil(($maxH-$img[2]) *0.5)) : 0;
		$w = $img[1] + ($left_pad*2);
		$h = $img[2] + ($top_pad*2);
		$left_pad = $left_pad/$maxW * 100;
		$top_pad = $top_pad/$maxW * 100; // NOTE: padding has to be with respect to maxW!!!
		if ($crop) {
			$w = $w/$maxW * 100;
			$h = $h/$maxH * 100;
			
			$style = "width:{$w}%;height:{$h}%;padding:{$top_pad}% {$left_pad}%;";
		} else {
			$style = "width:100%;height:100%;padding:{$top_pad}% {$left_pad}%;";
		}
		// Center image based on width
		if ($crop) {
			if ($img[1] > $maxW) {
				$mleft = ($img[1] - $maxW)/2;
				$mleft = $mleft/$maxW * 100;
				$style .= "margin-left:-{$mleft}%;";
			}
		}
		return $style;
	}
	
	function get_padding($size, $img) {
		$left_pad = ($size[0]>$img[1]) ? intval(ceil(($size[0]-$img[1]) *0.5)) : 0;
		$top_pad = ($size[1]>$img[2]) ? intval(ceil(($size[1]-$img[2]) *0.5)) : 0;
		$right_pad = ($size[0]>$img[1]) ? $size[0] - $left_pad - $img[1] : 0;
		$bottom_pad = ($size[1]>$img[2]) ? $size[1] - $top_pad - $img[2] : 0;
		return "{$top_pad}px {$right_pad}px {$bottom_pad}px {$left_pad}px";
	}
	
	
	function get_thumb_size($tsize, $maxW, $maxH) {
		if ($tsize == 'none') return array(0,0);
		if (empty($tsize)) $tsize = 'auto';
		if (is_array($tsize))
			if (($tsize[0] <= 0) || ($tsize[1] <= 0)) $tsize = 'auto';
			
		if (is_string($tsize)) {
			switch ($tsize) {
			case 'small':
				$tsize = $this->thumb_ss;
				break;
			case 'medium':
				$tsize = $this->thumb_ms;
				break;
			case 'large':
				$tsize = $this->thumb_ls;
				break;
			case 'thumb':
				$tsize = 'thumbnail';
			case 'thumbnail':
				$tsize = $this->convert_size($tsize);
				break;		
			case 'auto':
			default:
				if ($maxW > 700) $tsize = $this->thumb_ls;
				elseif ($maxW < 170) $tsize = $this->thumb_ss;
				else $tsize = $this->thumb_ms;
				break;			
			}
		}
		return $tsize;		
	}


	function get_thumb_padding($img, $tsize) {
		global $shiba_gallery;
		$left_pad = intval(ceil(($tsize[0]-$img[1]) *0.5));
		$top_pad = intval(ceil(($tsize[1]-$img[2]) *0.5));
		$bottom_pad = $tsize[1]-$img[2]-$top_pad;
		$right_pad = $tsize[0]-$img[1]-$left_pad;
		return "{$top_pad}px {$right_pad}px {$bottom_pad}px {$left_pad}px";
	}

	function get_attachment_by_title($title) {
		global $wpdb;
		$attachment = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='attachment'", $title ));
		return $attachment;
	}

	function get_attachment_image_src($id, $size) {
		global $shiba_gallery;
		$image = get_post($id);
		if ($image->post_type == 'attachment')
			return wp_get_attachment_image_src($id, $size);
		else {
			if (function_exists('get_post_thumbnail_id'))
				$attachment_id = get_post_thumbnail_id($id);
			if ($attachment_id)	
				return wp_get_attachment_image_src($attachment_id, $size);
			else { // return a default attachment
				if (!$shiba_gallery->is_option('default_image')) {
					$size = $this->convert_size($size);
					if (isset($size[0]) && isset($size[1]))
						return array($shiba_gallery->empty_image, $size[0], $size[1]);
				}
				return wp_get_attachment_image_src($shiba_gallery->options['default_image'], $size);
			}
		}		
	}


	function convert_size($size) {
    	global $_wp_additional_image_sizes;
		$result = array();
		
   		if ( is_array($size) ) { return $size; }
		
		switch($size) {
		case 'thumb':
			$size = 'thumbnail';
		case 'thumbnail':
		case 'medium':
		case 'large':
			$result[0] = intval(get_option($size.'_size_w'));
			$result[1] = intval(get_option($size.'_size_h'));
			break;			
		default:
			if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
				$result[0] = intval( $_wp_additional_image_sizes[$size]['width'] );
				$result[1] = intval( $_wp_additional_image_sizes[$size]['height'] );
			} else return FALSE;
			break;
		}	
		return $result;	
	}
	

	function shorten_description($result, $max_len) {
		$result = str_replace("\n", " ", $result);	
		$result = str_replace("\r", " ", $result);	
		if (strlen($result) > $max_len) {
			$end = strpos($result, ' ', $max_len);
			if ($end !== FALSE)
				return substr($result, 0, $end) . " [...]";
			else
				return substr($result, 0, $max_len) . " [...]";
		} else
			return $result;		
	}

	function get_caption($image, $caption_type, $link_type, $separator = '-', $max_len=200) {
		
		$img_link = $this->get_attachment_link($image, $link_type);
		switch ($caption_type) {
		case 'description':					
		case 'permanent':		
			$description = $this->get_attachment_description($image, $max_len);
			
			if ($description) $description = "<strong>{$img_link}</strong> {$separator} {$description}";
			else $description = "<strong>{$img_link}</strong>";
			return $description;
		case 'title':
			return "<strong>{$img_link}</strong>";
		case 'none':
		default:
			return '';
		}
	}
	
	function get_attachment_title($image) {
		$title = str_replace("'", '&apos;', $image->post_title);
		return apply_filters('shiba_attachment_title', $title, $image);
	}	
	
	function get_attachment_description($image, $max_len=200) {
		global $shiba_gallery;
		
		if ($shiba_gallery->screen_w <= 400)
			if ($max_len > 180) $max_len = 180;
			
		$max_len = apply_filters('shiba_caption_length', $max_len);

		if ($image->post_excerpt)
			$result = $image->post_excerpt;
		else
			$result = $image->post_content;
		$result = apply_filters('shiba_attachment_description', $result, $image);

		$result = strip_shortcodes( $result );
		$result = str_replace(']]>', ']]&gt;', $result);
		$result = strip_tags($result);
		
		$result = trim($result);
		$result = str_replace("\n", " ", $result);	
		$result = str_replace("\r", " ", $result);	
		$result = str_replace("'", "", $result);	
		$result = str_replace('"', "", $result);	
		
		if ($max_len <= 0) return $result;
		if (strlen($result) > $max_len) {
			$end = strpos($result, ' ', $max_len);
			if ($end !== FALSE)
				return substr($result, 0, $end) . " [...]";
			else
				return substr($result, 0, $max_len) . " [...]";
		} else
			return $result;		
	}

	function get_lightbox_caption($image, $caption) {
		$img_caption = $this->get_caption($image, $caption, 'file');
		if ($img_caption) {
			$img_caption = str_replace("'", "\"", $img_caption);
			$img_caption = "title='{$img_caption}'";
		}
		return $img_caption;
	}
	
	// caption is only used for slimbox and lytebox type
	function get_attachment_link($image, $type='file', $caption='title', $text='') {
		global $shiba_gallery;
		if (!$text) $title = $this->get_attachment_title($image);
		else $title = $text;	

		if ($caption == 'none') $caption = 'title';
		$size = apply_filters('lightbox_image_size', 'full');
		
		if (in_array($type, Shiba_Gallery::$lb_types)) {
			$full = $this->get_attachment_image_src($image->ID, $size);
			if (is_array($full)) $src = $full[0];
			else $src = SHIBA_GALLERY_URL . '/images/full_empty1.jpg';
		}
		
		if ($image->post_type == 'attachment') {// get source file
			switch ($type) {
			case 'slimbox':
			case 'lytebox':
			case 'lightbox':
				$num = $shiba_gallery->lightNum;
				$img_caption = $this->get_lightbox_caption($image, $caption);
				if ($type == 'slimbox')
					$link = "<a href='{$src}' class='shiba-link' rel='lightbox-shiba{$num}' {$img_caption}>{$title}</a>";
				elseif ($type == 'lightbox')	
					$link = "<a href='{$src}' class='shiba-link' data-lightbox='shiba{$num}' {$img_caption}>{$title}</a>";
				else
					$link = "<a href='{$src}' class='shiba-link' rel='lytebox[shiba{$num}]' {$img_caption}>{$title}</a>";
				break;
			case 'file':
				$link = "<a href='".wp_get_attachment_url($image->ID)."' class='shiba-link' target='_top'>{$title}</a>";
				break;
			case 'none':
				$link = $title;
				break;
			case 'attachment':
			default:
				$link = "<a href='".get_permalink($image->ID)."' class='shiba-link' target='_top'>{$title}</a>";	
			}	
		} else {
			switch ($type) {
			case 'slimbox':
			case 'lytebox':
			case 'lightbox':
				$num = $shiba_gallery->lightNum;
				$img_caption = $this->get_lightbox_caption($image, $caption);
				
				if ($type == 'slimbox')
					$link = "<a href='{$src}' class='shiba-link' rel='lightbox-shiba{$num}' {$img_caption}>{$title}</a>";
				elseif ($type == 'lightbox')
					$link = "<a href='{$src}' class='shiba-link' data-lightbox='shiba{$num}' {$img_caption}>{$title}</a>";
				else	
					$link = "<a href='{$src}' class='shiba-link' rel='lytebox[shiba{$num}]' {$img_caption}>{$title}</a>";
				break;
				
			case 'none':
				$link = $title;
				break;
			case 'file':
			case 'attachment':
			default:			
				$link = "<a href='".get_permalink($image->ID)."' class='shiba-link' target='_top'>{$title}</a>";
			}
		}
		
		return apply_filters( 'shiba_get_attachment_link', $link, $image->ID );
	}


	function get_attachment_url($image, $type = 'file') {
		if ($image->post_type == 'attachment') {// get source file
			switch ($type) {
			case 'file':
				$url = wp_get_attachment_url($image->ID);
				break;
			case 'none':
				$url = '';
				break;
			case 'attachment':
			default:
				$url = get_permalink($image->ID);
			}	
		} else {
			switch ($type) {
			case 'none':
				$url = '';
				break;
			case 'file':
			case 'attachment':
			default:			
				$url = get_permalink($image->ID);
			}	
		}	
		return apply_filters( 'shiba_get_attachment_url', $url, $image->ID );
	
	}
	
	function get_frame_width($frame) {
		switch ($frame) {
		case 'frame1':
			return 24;
		case 'frame2':
			return 18;
		case 'frame3':
			return 0;
		case 'frame4':
			return 0;	
		case 'frame5':
			return 26;	
		case 'frame6':
			return 26;	
		case 'frame7':
			return 26;	
		default:
			return 0;	
		}			
	}
	
	function get_frame_inner_width($frame) {
		switch ($frame) {
		case 'frame1':
			return 4;
		case 'frame2':
			return 4;
		case 'frame3':
			return 0;
		case 'frame4':
			return 0;
		default:
			return 0;	
		}			
	}
	
	function translate_frame_name($fname) {
		global $shiba_gallery;
		
		if (in_array($fname, array('frame0', 'frame1', 'frame2', 'frame3', 'frame4', 'frame5', 'frame6', 'frame7')))
			return $fname;					   
		switch ($fname) {
		case 'none':
			return 'frame0';	
		case 'green':
			return 'frame1';
		case 'blue':
			return 'frame2';
		case 'gray':
			return 'frame3';
		case 'shadow':
			return 'frame4';	
		case 'black':
			return 'frame5';	
		case 'white':
			return 'frame6';	
		case 'border':
			return 'frame7';	
		default:
			return $shiba_gallery->is_option('frame');
		}
	}
	
	function render_frame_options($name, $selected, $add_default=FALSE) {
		global $shiba_gallery;
		 ?>
        <select name='<?php echo $name;?>' id='<?php echo $name;?>'>
            <!-- Display themes as options -->
            <?php 
				if ($add_default)
                 	echo $shiba_gallery->general->write_option("Default", "", $selected);
               	echo $shiba_gallery->general->write_option("None", "frame0", $selected);
                echo $shiba_gallery->general->write_option("Shadow", "frame4", $selected);
                echo $shiba_gallery->general->write_option("White", "frame6", $selected);
                echo $shiba_gallery->general->write_option("Black", "frame5", $selected);
                echo $shiba_gallery->general->write_option("Double Border", "frame7", $selected);
                echo $shiba_gallery->general->write_option("Popeye Green", "frame1", $selected);
                echo $shiba_gallery->general->write_option("Popeye Blue", "frame2", $selected);
                echo $shiba_gallery->general->write_option("Popeye Gray", "frame3", $selected);
           ?>
        </select>	
	<?php }
} // end Shiba_Helper class
endif;

?>