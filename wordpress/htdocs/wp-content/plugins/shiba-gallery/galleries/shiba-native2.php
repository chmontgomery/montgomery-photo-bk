<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Native2")) :

class Shiba_Gallery_Native2 {

	function add_native_styles($galleryID, $args) {
		global $shiba_gallery;
		extract($args);

		$native_style = "";
		if ( apply_filters( 'use_default_gallery_style', true ) )
			$native_style = "
				#{$galleryID} .shiba-outer {
					margin: auto;
				}
				#{$galleryID} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;			
				}
				#{$galleryID} .shiba-gallery-caption {
					margin: 0px 5px 0px 5px;
				}
			";
		$shiba_gallery->styleStr .= apply_filters( 'gallery_style', $native_style);		
	}
	
	function render_native($image, $img, $args) {
		global $shiba_gallery;
		static $i = 0; 
		extract($args);
		
		$image_meta  = wp_get_attachment_metadata( $image->ID );
		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		$imgStr = "<{$itemtag} class='{$class}' style='{$gallery_style}'>\n"; 
		$imgStr .= "<div class='native-wrapper'>\n"; 
		$imgStr .= "<{$icontag} class='gallery-icon {$orientation}' >\n"; 
		
		$imgStr .= "<div class='shiba-outer'>\n"; 	
		$style = $shiba_gallery->helper->get_image_style($img, $crop, $maxW, $maxH);
		$imgStr .= "<div class='shiba-stage' >\n";  
		$imgStr .= "<img src='{$img[0]}' style='{$style}' $alt title='{$title}' />\n";  
		$imgStr .= "</div>\n";
		$imgStr .= "</div>\n"; // End shiba-outer
		
		$imgStr .= "</{$icontag}>";
		
		if ( $captiontag ) { 
			// Add caption
			$caption_style = '';
			if ($caption == 'none')
				$caption_style = "style='display:none;'";
				
			$imgStr .= "<{$captiontag} class='native-text' {$caption_style}>";  // wp-caption-text gallery-caption
			$imgStr .= "<h6 class='shiba-link'>\n";
			$imgStr .= $imglink; // wp_texturize
			$imgStr .= "</h6>\n";
			if (($caption == 'description') || ($caption == 'permanent')) 
				$imgStr .= "<p>{$description}</p>";
			$imgStr .= "</{$captiontag}>\n";
		}
		$imgStr .= "</div>\n";	// Close native-wrapper	
		$imgStr .= "</{$itemtag}>\n";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$imgStr .= "<br style='clear: both' />\n";
		return $imgStr;	
	}
	
	
	function render_native2($image, $img, $args) {
		global $shiba_gallery;
		extract($args);
		
		switch($type) {
		case 'navlist':
			$imgStr = "<div class='navlist-item'>\n"; 
			$imgStr .= "<div class='native-wrapper' style='{$gallery_style}'>\n";
			break;
		case 'native2':
		default:
			$imgStr = "<div class='native2-thumb' style='$gallery_style' >\n"; 
			$imgStr .= "<div class='native-wrapper'>\n";
			break;
		}
		
		$imgStr .= "<div class='shiba-outer'>";
		$style = $shiba_gallery->helper->get_image_style($img, $crop, $maxW, $maxH);
		$imgStr .= "<div class='shiba-stage'>\n"; 
		$imgStr .= "<img src='{$img[0]}' style='{$style}' $alt title='{$title}' />\n";  
		$imgStr .= "</div>\n";
		$imgStr .= "</div>\n"; // End shiba-outer
		$imgStr .= "</div>\n";	// End native-wrapper
		
		// Add caption
		$caption_style = '';
		if ($caption == 'none')
			$caption_style = "style='display:none;'";
			
		$imgStr .= "<div class='native-text' {$caption_style}>";  
		$imgStr .= "<h5 class='shiba-link'>{$imglink}</h5>";
		if (($caption == 'description') || ($caption == 'permanent')) 
			$imgStr .= "<p>{$description}</p>";
		$imgStr .= "</div>\n";	// End native-text

		$imgStr .= "</div>\n"; // End native2-thumb	
		return $imgStr;	
	}
	
	
	function render($images, $args) {
		global $shiba_gallery;	
	
		extract($args);
		
		$num = $shiba_gallery->gNum;
		$galleryID = "native-{$num}";
		$size = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
		$maxW = $size[0]; $maxH = $size[1];

		$options = array(	'id' => $galleryID,
						 	'crop' => $crop,
						 	'active' => $active,
							'aspect' => 0,
//							'padding' => 0,
							'height' => $maxH,
						);		

		$frame_w = $shiba_gallery->helper->get_frame_width($frame);

		$gallery_width = $size[0] + $frame_w;
		$gallery_height = $size[1] + $frame_w;
		$options['width'] = $gallery_width;

		$gallery_style = "width:{$gallery_width}px;"; // height:{$gallery_height}px;
		if (($type == 'native') && ($responsive != 'none'))
			$gallery_style = "";
		
		switch ($responsive) {
		case 'aspect':
			$options['aspect'] = $size[1]/$size[0];			
		case 'width':
			$gallery_style .= "max-width:100%;";
			$responsive = TRUE;
			break;
		case 'none':
		default:
			$responsive = FALSE;
			break;
		}
		$options['responsive'] = $responsive;
		
		switch ($type) {
		case 'native':
			$class = "gallery-item";
			$this->add_native_styles($galleryID, $args);
			$imgStr = "<div id='{$galleryID}' class='gallery galleryid-{$id} gallery-columns-{$columns} shiba-gallery  {$frame}'>"; 
			break;
		case 'navlist':
			$class = "navlist-item";
			$imgStr = "<div id ='{$galleryID}' class='shiba-gallery {$frame}'>\n";
			break;
		case 'native2':
		default:
			$class = "native2-thumb";
			$imgStr = "<div id ='{$galleryID}' class='shiba-gallery {$frame}'>\n";
			break;
		}
		$options['class'] = $class;
		$shiba_gallery->native2_option[$num] = apply_filters('shiba_native2_options', $options);

		$j = 0;
		foreach ( $images as $image ) {		
			$title = $shiba_gallery->helper->get_attachment_title($image);
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
			$description = $shiba_gallery->helper->get_attachment_description($image, 200);
			
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";
			
			// Set the link to the attachment URL
			$img = $all_img[$j];
			if (($img[1] <= 1) && ($img[2] <= 1)) {
				$img[1] = 100; $img[2] = 100;
			}
			
			$args2 = compact('type', 'class', 'gallery_style', 'title', 'caption', 'imglink', 'crop', 'maxW', 'maxH', 'alt', 'description', 'icontag', 'itemtag', 'captiontag', 'columns');
			switch ($type) {
			case 'native':
				$imgStr .= $this->render_native($image, $img, $args2);
				break;
			case 'lytebox':
			case 'slimbox':
			case 'lightbox':
			case 'navlist':
			case 'native2':	
			default:
				$imgStr .= $this->render_native2($image, $img, $args2);
				break;
			}
				
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