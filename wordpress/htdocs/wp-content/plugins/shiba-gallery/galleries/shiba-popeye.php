<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Popeye")) :

class Shiba_Gallery_Popeye {

	function open_popeye($size, $args) {
		global $shiba_gallery;
		
		$outStr = "<div id='ppy{$shiba_gallery->gNum}' class='{$args['frame']} shiba-gallery popeye' >\n";
		$outStr .= "<div class='ppy'>\n";
		$outStr .= "<ul class='ppy-imglist'>\n";
		return $outStr;
	}
	

	function close_popeye($size, $args) {
		global $shiba_gallery;
		
		$stage_style = "width:{$size[0]}px;height:{$size[1]}px;"; 
		if ($args['responsive']) {
			$stage_style .= "max-width:100%;";
		}
		$outStr = "</ul>\n";

			$outStr .= "<div class='ppy-outer shiba-outer'>\n";
			$outStr .= "<div class='ppy-stage shiba-stage' style='{$stage_style}'>\n";
 			$outStr .= '
                   <div class="ppy-nav"> 
                        <div class="nav-wrap"> 
                            <a class="ppy-prev" title="Previous image">Previous image</a>';
			$outStr .= '	<a class="ppy-play" title="Play Slideshow">Play Slideshow</a>
                			<a class="ppy-pause" title="Stop Slideshow">Stop Slideshow</a>';
			if ($shiba_gallery->screen_w >= SHIBA_MAX_IMAGE_W) {	
				$outStr .= '<a class="ppy-switch-enlarge" title="Enlarge">Enlarge</a> 
							<a class="ppy-switch-compact" title="Close">Close</a>';
			}
			$outStr .= '	<a class="ppy-next" title="Next image">Next image</a> 
                        </div> 
                    </div> 
                    <div class="ppy-counter"> 
                        <strong class="ppy-current"></strong> / <strong class="ppy-total"></strong> 
                    </div> ';


			if ($args['caption'] == 'permanent') $outStr .= "<div class='ppy-caption shiba-caption c{$args['cpos']}' style='height:70px;'>\n";   
		   	else $outStr .= "<div class='ppy-caption shiba-caption c{$args['cpos']}'>\n";
		   	$outStr .= "<span class='ppy-text'></span>\n"; 
           	$outStr .= "</div> <!-- Close caption -->\n";
			$outStr .=  "</div><!--End ppy-stage-->\n";


			$outStr .= "</div><!--End ppy-outer -->\n"; 
			$outStr .= "</div></div>\n";
		$shiba_gallery->gNum++;
		return $outStr;		
	}	


		
	function render($images, $args) {
		global $shiba_gallery;	
		extract($args);
	
		$id= "ppy{$shiba_gallery->gNum}";
		$size_arr = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
		$maxW = $size_arr[0]; $maxH = $size_arr[1];
		
		$frame_w = $shiba_gallery->helper->get_frame_width($args['frame']);
		$gallery_width = $maxW + $frame_w; 
		$gallery_height = $maxH + $frame_w;
		
		// Add popeye options
		$options = array(	'active' => $active,
						 	'responsive' => $responsive,
							'aspect' => 0,	
							'width' => $gallery_width,
							'height' => $gallery_height,
						 );

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

		switch ($caption) {
		case 'title':
		case 'description':	
			$options['caption'] = 'hover';
			break;
		case 'none':
			$options['caption'] = FALSE;
			break;
		case 'permanent':
			$options['caption'] = 'permanent';
			break;
		}
		$options['autoslide'] = $autoplay ? TRUE : FALSE;
		
		if ($args['frame'] == 'frame2') $options['navigation'] = 'permanent';	
		if ($shiba_gallery->screen_w <= 400) $options['navigation'] = 'permanent';
		if ($shiba_gallery->screen_w >= SHIBA_MAX_IMAGE_W) $options['enlarge'] = TRUE;
		
		$shiba_gallery->popeye_option[$shiba_gallery->gNum] = apply_filters('shiba_popeye_options', $options);
				
		$imgStr = $this->open_popeye($size_arr, $args);
		$j = 0;		
		foreach ( $images as $image ) {		
			$img_caption = $shiba_gallery->helper->get_caption($image, $caption, $link, '<p>');
			if (strpos($img_caption, '<p>'))
				$img_caption .= '</p>';

			// Get url of full image
			$full = $shiba_gallery->helper->get_attachment_image_src($image->ID, "full");
			$full_empty = SHIBA_GALLERY_URL . '/images/full_empty1.jpg';
			$img = $all_img[$j]; $j++;

			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";

			// padding for main image
			$left_pad = intval(ceil(($maxW-$img[1]) *0.5));
			$top_pad = intval(ceil(($maxH-$img[2]) *0.5));
 	
			$imgStr .= "<li>\n";
			if (is_array($full)) $imgStr .= "<a href='{$full[0]}'>\n";
			else $imgStr .= "<a href='{$full_empty}'>\n";
			$imgStr .= "<img src='{$img[0]}' {$alt} width='{$img[1]}' height='{$img[2]}'/>\n";
			$imgStr .= "</a>\n";
			$imgStr .= "<span class='ppy-extcaption'>\n";
			$imgStr .= $img_caption;
			$imgStr .= "</span>\n";
			$imgStr .= "</li>\n";
		}
		$imgStr .= $this->close_popeye($size_arr, $args);
		return $imgStr;
	}
} // end class
endif;
?>