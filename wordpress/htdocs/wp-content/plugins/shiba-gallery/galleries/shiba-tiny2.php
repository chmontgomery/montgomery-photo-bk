<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Tiny2")) :

class Shiba_Gallery_Tiny2 {
	var $gallery_width;
	
	function open_tinyss($size, $args) {
		global $shiba_gallery;

		$frame = $args['frame'];
		$responsive = $args['responsive'];
		$num = $shiba_gallery->gNum;
		$id = "tiny-{$num}";
		$frame_w = $shiba_gallery->helper->get_frame_width($frame);
		
		$gallery_style = "";
		$outer_style = "";
		
		switch ($responsive) {
		case 'width':
		case 'aspect':
			// These styles make the slider buttons be contained within the gallery
/*			$shiba_gallery->styleStr .= "#{$id} .slider { float:none; }";
			$shiba_gallery->styleStr .= "#{$id} .sliderbutton  {position:absolute; width:25%; height:95%; cursor:pointer; z-index:150}";
			$shiba_gallery->styleStr .= "#{$id} .slideleft {left:10px; background:url('".SHIBA_GALLERY_URL."/images/popeye/prev3.png') left center no-repeat; }";
			$shiba_gallery->styleStr .= "#{$id} .slideright {right:10px; background:url('".SHIBA_GALLERY_URL."/images/popeye/next3.png') right center no-repeat; }";
			$shiba_gallery->styleStr .=	 "#{$id} .shiba-caption { padding: 2px 0; }";
*/
			$gallery_style .= "max-width:100%;";
			$responsive = TRUE;
			break;
		case 'none':
		default:
			$responsive = FALSE;
			break;
		}
		$gallery_width = $size[0] + $frame_w;
		$gallery_height = $size[1] + $frame_w;
		if ($shiba_gallery->screen_w >= SHIBA_MAX_IMAGE_W) { 
			// Increase width for slider buttons
			$gallery_width += 50; 
		}

		
		$this->gallery_width = $gallery_width;
		$gallery_style .= "width:{$gallery_width}px;height:{$gallery_height}px;";
		
		$outStr = "<div id='tiny-wrapper-{$num}' class='tiny2-wrapper shiba-gallery {$frame}' style='{$gallery_style}'>\n";  
		$outStr .= "<div id='{$id}' class='tiny2 shiba-outer' {$outer_style}>\n"; 
		$outStr .= "<div class='sliderbutton slideleft' id='slideleft-{$num}' onclick='tiny2[{$num}].move(-1)'></div>\n";
		$outStr .= "<div class='sliderbutton slideright' id='slideright-{$num}' onclick='tiny2[{$num}].move(1)'></div>";
		
		// add active image link
		if ($args['active'] && ($args['link'] != 'none')) {
			$outStr .= "<a href='#' onclick='console.log(\"click\");' id='imglink-{$num}' class='imglink'></a>"; 
		}	

		return $outStr;
	}
	

	function close_tinyss($size, $tsize, $args, $images) {
		global $shiba_gallery;
		$class = 'pagination';
		$num = $shiba_gallery->gNum;
		
		$outStr = "";
		switch ($args['responsive']) {
		case 'width':
		case 'aspect':
			break;
		case 'none':
		default:
			break;
		}
		if ($tsize[0]) { // prepare to add thumbs
			$class .= ' thumb';			
			$outStr .= "</div>\n"; // close tiny2 container
		}
		
		$outStr .= "<ul id='pagination-{$num}' class='$class' >"; 
		$i = 0;
		foreach ( $images as $image ) {		
			if ($tsize[0]) {
				$thumb = $shiba_gallery->helper->get_attachment_image_src($image->ID, $tsize);
				// padding for thumb
				$padding = $shiba_gallery->helper->get_padding($tsize, $thumb);
				$outStr .= "<li style='width:{$tsize[0]}px;height:{$tsize[1]}px' onclick='tiny2[{$num}].pos($i)'>";
				$outStr .= "<img width='{$tsize[0]}' height='{$tsize[1]}'  src='{$thumb[0]}' style='padding:{$padding};' />\n";
				$outStr .= "</li>\n";
			} else {	
				$outStr .= "<li onclick='tiny2[{$num}].pos($i)'>";
				$outStr .= "</li>\n";
			}
			$i++;
		}
		$outStr .= "</ul>\n"; 
		if ($tsize[0]) {
			$outStr .= "</div>\n"; // close ts_wrapper
			$outStr .= "<div style='clear:both;'></div>\n";
		} else	
			$outStr .= "</div></div>\n"; // close ts_wrapper

		$shiba_gallery->gNum++;
		return $outStr;		
	}	


		
	function render($images, $args) {
		global $shiba_gallery;	
		extract($args);
	
		$size_arr = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
		$maxW = $size_arr[0]; $maxH = $size_arr[1];
		$tsize = $shiba_gallery->helper->get_thumb_size($tsize, $maxW, $maxH);
		$num = $shiba_gallery->gNum;
		
		$options = array( 	'auto' => ($autoplay)?'4':'false',
							'aspect' => 0,		   
							'crop' => ($crop)?'true':'false',
							'active' => ($active && ($link != 'none')),
						);

		if ($responsive) {
			$stage_style = "width:{$maxW}px;height:{$maxH}px;";	
			$li_style = "width:{$maxW}px;height:{$maxH}px;text-align:left;";
		} else {
			$stage_style = $li_style = "width:{$maxW}px;height:{$maxH}px;";
		}

		switch ($responsive) {
		case 'aspect':
			if ($maxW)
				$options['aspect'] = $maxH/$maxW;
		case 'width':
			$responsive = TRUE;
			break;
		case 'none':
		default:
			$responsive = FALSE;
			break;
		}
		$options['responsive'] = ($responsive)?'true':'false';
		
		$shiba_gallery->tiny2_option[$num] = apply_filters('shiba_tiny2_options', $options);
		$imgStr = $this->open_tinyss($size_arr, $args);

		$j = 0; 
		$imgStr .= "<div id='slider-{$num}' class='slider shiba-stage' style='{$stage_style}'>\n";
		$imgStr .= "<ul>\n"; 
		foreach ( $images as $image ) {		
			$description = $shiba_gallery->helper->get_attachment_description($image);
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
			
			$img = $all_img[$j]; $j++;
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";

			$style = $shiba_gallery->helper->get_image_style($img, $crop, $maxW, $maxH);

			$imgStr .= "<li style='{$li_style}'>\n"; 
			$imgStr .= "<span style='display:block;position:relative;'>\n";
			$imgStr .= "<img src='{$img[0]}' $alt style='{$style}' />\n";
			
			$caption_style = '';
			if ($caption == 'none')
				$caption_style = "style='display:none;'";
				
			$imgStr .= "<div class='shiba-caption c{$cpos}' {$caption_style}>\n";
			$imgStr .= "<h4>$imglink</h4>\n";
			if (($caption == 'description') || ($caption == 'permanent')) 
				$imgStr .= "<p>{$description}</p>\n";
			$imgStr .= "</div>\n";

			$imgStr .= "</span>\n";			
			$imgStr .= "</li>\n";
		}
		$imgStr .= "</ul>\n";
		$imgStr .= "</div>\n";
		$imgStr .= $this->close_tinyss($size_arr, $tsize, $args, $images);
		return $imgStr;
	}
} // end class
endif;
?>