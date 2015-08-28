<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Galleria")) :

class Shiba_Gallery_Galleria {

	function render($images, $args) {
		global $shiba_gallery;	

		extract($args);

		$num = $shiba_gallery->gNum;
		$size = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
		$tsize = $shiba_gallery->helper->get_thumb_size($tsize, $size[0], $size[1]);

		// http://galleria.io/docs/
		$options = array( 	'queue' => false,
//							'fullscreenDoubleTap' => false,
							'swipe' => false,
							);
		$options['height'] = absint($size[1]);
		if (!$tsize[0]) $options['thumbnails'] = false;
		else $options['height'] += ($tsize[1] + 10);

		$frame_w = $shiba_gallery->helper->get_frame_width($frame);

		switch ($responsive) {
		case 'width':
		case 'aspect':
			if (($responsive == 'aspect') && $size[0]) {
				$options['height'] = $options['height']/$size[0];
			}
			$responsive == TRUE;
			break;
		case 'none':
		case 'default':
			$responsive = FALSE;
			$options['width'] = absint($size[0]); 
			break;
		}
		$gallery_width = $size[0] + $frame_w;
		
		// Other options - lightbox, showCounter, showImagenax, responsive
		$options['responsive'] = $responsive;
		if  ($link == 'lightbox') {
			$options['lightbox'] = true;
			$link = 'none';			
		} 
		
		if ($caption == 'none')
			$options['showInfo'] = false;
		if ($crop)
			$options['imageCrop'] = true;
			
		$options['autoplay'] = $autoplay ? true : false;
		if (!$active) $options['clicknext'] = true;
		
		$shiba_gallery->galleria_option[$num] = apply_filters('shiba_galleria_options',$options);
		
		$info_style = '';
		$link_style = '';
		
		switch ($frame) {
		case 'frame1':
			$hf = absint(floor($frame_w * 0.5))-2;
			break;
		case 'frame2':
			$hf = absint(floor($frame_w * 0.5))+4;
			break;
		default:	
			$hf = 0;
			break;
		}
		$w = "100%";
		
		$bottom = $tsize[1] + 20;
		switch ($cpos) {
		case 'top':
			$info_style = "width:{$w}; top:{$hf}px; left:0px; padding: 0 {$hf}px;"; 
			$link_style = "top:15px; left:15px; ";
			break;
		case 'bottom':
			$info_style = "width:{$w}; bottom:{$bottom}px; left:0px; padding: 0 {$hf}px;";			
			$link_style = "bottom:8px; right:15px; ";
			break;
		case 'right':
			$bottom += 5;
			$info_style = "width: 50%; bottom:{$bottom}px; right:{$hf}px;"; 
			$link_style = "bottom:8px; right:15px; ";
			break;
		default:	
		case 'left':
			$top = $hf + 10;
			$info_style = "width: 50%; top:{$top}px; left:{$hf}px;"; 
			$link_style = "top:0px; left:15px; ";
			break;
		}
		$r = $hf + 5;
		$shiba_gallery->styleStr .= ".galleria-info-link,.galleria-info-close {right:{$r}px;}";
		$gallery_style = "width:{$gallery_width}px;";
		
		// Add thumbnail styles to header string
		$shiba_gallery->styleStr .=  
        "#galleria-{$num} .galleria-thumbnails-container { height: " . ($tsize[1] + 10) . "px; }";
		$shiba_gallery->styleStr .= 
		"#galleria-{$num} .galleria-stage { bottom: " . ($tsize[1] + 20) . "px; }";
		$shiba_gallery->styleStr .= 
	   	"#galleria-{$num} .galleria-thumbnails .galleria-image { height: {$tsize[1]}px; width: {$tsize[0]}px;}";
		$shiba_gallery->styleStr .= 
	   	"#galleria-{$num} .galleria-info { $info_style } #galleria-{$num} .galleria-info-link { $link_style }";
		
		$j = 0; 
		$imgStr = "";		
		if ($responsive) {
			$shiba_gallery->styleStr .= "#galleria-{$num} .galleria-container { width:auto !important; }";
			$gallery_style .= "max-width:100%;";
		}
		$imgStr .= "<div id='galleria-{$num}' class='shiba-gallery $frame' style='{$gallery_style}'>";
		foreach ( $images as $image ) {	
			$simple_title = $shiba_gallery->helper->get_attachment_title($image);
	
			if (isset($options['lightbox'])) {
				$title = "<a href='#' onclick='return false;' class='shiba-link'>{$simple_title}</a>";
				$linkStr = '';
			} elseif (in_array($link, array('slimbox', 'lytebox'))) {
				$title = $shiba_gallery->helper->get_attachment_link($image, $link, 'none');
				$linkStr = $shiba_gallery->helper->get_attachment_url($image, $link);
				
			} else {
				$title = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
				$linkStr = $shiba_gallery->helper->get_attachment_url($image, $link);
			}
			
			$img = $all_img[$j];
			$full = $shiba_gallery->helper->get_attachment_image_src($image->ID, "full");
			if (!is_array($full) || ($full[1] <= 1) || ($full[2] <= 1)) {
				if ($frame == 'frame6')
					$full[0] = $thumb[0] = SHIBA_GALLERY_URL . '/images/full_empty1.jpg';
				else
					$full[0] = $thumb[0] = SHIBA_GALLERY_URL . '/images/full_empty2.jpg';
			} else
				$thumb = $shiba_gallery->helper->get_attachment_image_src($image->ID, array($tsize[0], $tsize[1]));
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";
			
			$imgStr .= "<a href='{$img[0]}'>\n";

//			$title = str_replace("'", "\"", $title);
			switch ($caption) {
			case 'none':
				$imgStr .= "<img src='{$thumb[0]}' $alt data-big='{$full[0]}' data-link='{$linkStr}' class='shiba-stage' />";
				$imgStr .= "</a>\n";
				break;
			case 'title':
				$imgStr .= "<img src='{$thumb[0]}' $alt data-big='{$full[0]}' data-link='{$linkStr}' data-title='{$simple_title}' class='shiba-stage' />";  
				$imgStr .= "</a>\n";
				$imgStr .= "{$title}";
				break;
			case 'description':	
			default:
				$description = $shiba_gallery->helper->get_attachment_description($image);
				$imgStr .= "<img src='{$thumb[0]}' $alt data-big='{$full[0]}' data-description='{$description}' data-link='{$linkStr}' data-title='{$simple_title}' class='shiba-stage'/>";   
				$imgStr .= "</a>\n";
				$imgStr .= "{$title}";
				break;
			}
//			if (in_array($link, array('slimbox', 'lytebox')) || isset($options['lightbox']))
//				$imgStr .= "\n{$title}\n";
			
			$j++;
		}
		$imgStr .= '<div style="clear:both;"></div>' ."\n";
		$imgStr .= "</div>\n";
		$shiba_gallery->gNum++;
		return $imgStr;
	}
} // end class
endif;
?>