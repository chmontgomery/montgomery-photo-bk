<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_Tiny1")) :

class Shiba_Gallery_Tiny1 {

	function open_tinyss($size, $args) {
		global $shiba_gallery;
	
		$outStr = "<ul id='tinyslideshow-{$shiba_gallery->gNum}' class='tiny1' >\n";
		return $outStr;
	}
	

	function close_tinyss($size, $tsize, $args) {
		global $shiba_gallery;
		$id = $shiba_gallery->gNum;

		$responsive = $args['responsive'];
		$gallery_width = $size[0];
		$gallery_width = $size[0] + $shiba_gallery->helper->get_frame_width($args['frame']);
		$gallery_style = "width:{$gallery_width}px;";
		
		switch ($responsive) {
		case 'width':
		case 'aspect':
			$gallery_style .= 'max-width:100%;';
			$img_width = '100%';
			$img_height = $size[1].'px';
			break;
		case 'none':
		default:
			$frame_w = $shiba_gallery->helper->get_frame_width($args['frame']);
			$img_width = $size[0].'px';
			$img_height = $size[1].'px';
			break;
		}
		
		$outStr = "
		</ul>
		<div id='ts_wrapper-{$id}' class='ts_wrapper shiba-gallery {$args['frame']}' style='{$gallery_style};'>"; 
		$outStr .= "<div class='shiba-outer'>";
		$outStr .= "
				<div id='ts_fullsize-{$id}' class='ts_fullsize  shiba-stage' >\n"; 
		$outStr .= "<div id='ts_imgprev-{$id}' class='ts_imgnav ts_imgprev' title='Previous Image'></div>\n"; 
		if ($args['active'])
			$outStr .= "<div id='ts_imglink-{$id}' class='ts_imglink'></div>\n";
		$outStr .= "<div id='ts_imgnext-{$id}' class='ts_imgnav ts_imgnext'  title='Next Image'></div>\n"; 
		
		$outStr .= "<div id='ts_image-{$id}' class='ts_image' style='width:{$img_width};height:{$img_height};'></div>\n"; 
		if ($args['caption'] != 'none') {
			$outStr .= "<div id='ts_information-{$id}' class='shiba-caption c{$args['cpos']}' >
						<h3></h3>
						<p></p>
						</div>\n";
		}
		$outStr .= "</div>\n"; // close shiba-outer
		$outStr .= "</div>\n"; // close ts_fullsize
		if ($tsize[0]) 
			$outStr .= "
			<div id='ts_thumbnails-{$id}' class='ts_thumbnails' >
				<div id='ts_slideleft-{$id}' class='ts_slideleft' title='Slide Left'></div>
				<div id='ts_slidearea-{$id}' class='ts_slidearea'> 
					<div id='ts_slider-{$id}' class='ts_slider'></div>
				</div>
				<div id='ts_slideright-{$id}' class='ts_slideright' title='Slide Right'></div>
			</div>\n";
		$outStr .= "</div>\n"; // close ts_wrapper
		$outStr .= "<div style='clear:left;'></div>";
		$shiba_gallery->gNum++;
		return $outStr;		
	}	


		
	function render($images, $args) {
		global $shiba_gallery;	
		extract($args);
	
		$size_arr = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
		$maxW = $size_arr[0]; $maxH = $size_arr[1];
		$tsize = $shiba_gallery->helper->get_thumb_size($tsize, $maxW, $maxH);

		$options = array(	'caption' => ($caption == 'none')?FALSE:TRUE,
							'active' => ($active)?TRUE:FALSE,
							'autoplay' => ($autoplay)?'true':'false',
							'crop' => ($crop)?'true':'false',
							'aspect' => 0,
							'thumbHeight' => $tsize[1] );
		
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
		
		$shiba_gallery->tiny1_option[$shiba_gallery->gNum] = apply_filters('shiba_tiny1_options', $options);
		$imgStr = $this->open_tinyss($size_arr, $args);

		$j = 0; 		
		foreach ( $images as $image ) {		
//			$title = $shiba_gallery->helper->get_attachment_title($image); 
			$description = $shiba_gallery->helper->get_attachment_description($image);
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
			$url = $shiba_gallery->helper->get_attachment_url($image, $link);
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";
			
			if ($tsize[0]) {
				$thumb = $shiba_gallery->helper->get_attachment_image_src($image->ID, $tsize);
				// padding for thumb
				$padding = $shiba_gallery->helper->get_padding($tsize, $thumb);
			}
			$img = $all_img[$j]; $j++;
			
			$imgStr .= "<li>\n";
			$imgStr .= "<h3>{$imglink}</h3>\n";
			
			$style = $shiba_gallery->helper->get_image_style($img, $crop, $maxW, $maxH);
			$tstyle = "";
			if ($tsize[0])
				$tstyle = "width='{$tsize[0]}' height='{$tsize[1]}'  src='{$thumb[0]}' style='padding:{$padding};'";
			$imgStr .= "<span style='{$style}'>{$img[0]}</span>\n";				
			
			if (($caption == 'description') || ($caption == 'permanent')) $imgStr .= "<p>{$description}</p>\n";
			else $imgStr .= "<p></p>\n";
			if ($tsize[0]) {
				
				$imgStr .= "<a href='{$url}'><img {$alt} {$tstyle}/></a>\n";
			}
			$imgStr .= "</li>\n";
		}
		$imgStr .= $this->close_tinyss($size_arr, $tsize, $args);
		return $imgStr;
	}
} // end class
endif;
?>