<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_NoobSlide")) :

class Shiba_Gallery_NoobSlide {
	var $js;
	
	function __construct() {
		if (!class_exists("Shiba_Gallery_NoobSlide_JS")) 
			require(SHIBA_GALLERY_DIR."/galleries/shiba-noobslide-js.php");
		$this->js = new Shiba_Gallery_NoobSlide_JS();	
	}
	
	function generate_containers($size, $tsize, $args, $doclick = TRUE, $noobnum = '1') {
		global $shiba_gallery;
		
		$num = $shiba_gallery->nsNum;		
		$id = "noobslide{$num}";
		// Make all noobslide image area clickable and advance
		if ($doclick) {
//			if ($args['active']) $click_event = "onClick='nS{$num}.active();'"; 
//			else  
			$click_event = "onClick='nS{$num}.next(true);'";
		} else $click_event = "";
		
		$outStr = '';
		$frame_w = $shiba_gallery->helper->get_frame_width($args['frame']);
		
		$main_width = $gallery_width = $size[0] + $frame_w;
		$stage_style = "width:{$size[0]}px; height:{$size[1]}px;";
		$outer_style = "";
		
		switch ($noobnum) {
		case '6':
			if ($tsize[0]) {
				$twidth = ($tsize[0] + 6); // 6 px for margins
				$main_width += $twidth * 2; 
				$outer_style = "margin:0 {$twidth}px;";	
			}
			break;
		}
		$main_style = "width:{$main_width}px;";
		
		switch ($args['responsive']) {
		case 'width':
		case 'aspect':
			$stage_style .= "max-width:100%;";
			$main_style .= "max-width:100%;";
			break;
		case 'none':
		default:
			break;
		}
		
		$outStr .= "<div class='noobmain {$args['frame']} shiba-gallery' style='{$main_style}'>\n";
		$outStr .= "<div class='shiba-outer' style='{$outer_style}'>\n"; 
		$outStr .= "<div class='noobmask shiba-stage' style='{$stage_style}' >\n";
		$outStr .= "<div id='$id' class='noobslide' {$click_event}>\n";
		return $outStr;
	}

	function generate_thumbnails($id, $class, $style, $tsize, $images, $start=0, $len=-1, $link = FALSE) {
		global $shiba_gallery;			
		$num = $shiba_gallery->nsNum;
		
		$num_images = count($images);
		if ($len <= 0)
			$len = $num_images;
			
		$thumb_height = $tsize[1];	
		if ($link && ($link != 'none') && ($tsize[0] >= 100)) 
			$thumb_height += 55; // for text
			
		$thumbDiv = "<div id='{$id}' class='noobslide_thumbs {$class}' style='{$style}'>\n"; 
		$i = 0;  $j = 0;
		foreach ($images as $image) {
			if ($i < $start) { $i++; continue; } 
			$img = $shiba_gallery->helper->get_attachment_image_src($image->ID, $tsize);
			$padding = $shiba_gallery->helper->get_thumb_padding($img, $tsize);
			$thumbDiv .= "<div style='width:{$tsize[0]}px; height:{$thumb_height}px'>\n";
			
			$style = "padding:{$padding};width:{$tsize[0]}px;height:{$tsize[1]}px;";
			$thumbDiv .= "<img src='{$img[0]}' style='{$style}'/>";
			
			if ($link && ($link != 'none') && ($tsize[0] >= 100) ) {
				$title = $shiba_gallery->helper->get_attachment_title($image);
				$thumbDiv .= "<a href='#' onClick='nS{$num}.active($j);return false;'>$title</a>\n";
//				$imglink = $shiba_gallery->helper->get_attachment_link($image, 'file');	
//				$thumbDiv .= "{$imglink}\n";
			}
			$thumbDiv .= "</div>\n";
			$i++; $j++; $len--;
			if ($len <= 0) break;
		}
		$thumbDiv .= "</div>\n";
		
		return $thumbDiv;
	}
	
	function generate_buttons($num, $buttons) {
		$outStr = "<div class='noobslide_buttons shiba-nav'>\n";
		foreach ($buttons as $button) {
			switch ($button) {
			case 'playback':
				$text = "&lt; Playback";
				break;
			case 'stop':
				$text = "Stop";
				break;
			case 'play':
				$text = "Play &gt;";
				break;
			case 'previous':
				$text = "&lt;&lt; Previous";
				break;
			case 'next':
				$text = "Next &gt;&gt;";			
				break;
			}
			$outStr .= "<span id='{$button}{$num}'>$text</span>\n";
		} // end foreach
		$outStr .= "</div>\n";
		return $outStr;
	}

	function generate_numcontrol($id, $w, $images) {
		$outStr = "<div class='noobslide_numcontrol' id='{$id}'>\n"; 
		$i = 1;	
		foreach ( $images as $image ) {	
			$outStr .= "<span class='noobslide_numthumb'>{$i}</span>\n";
			$i++;
		}
		$outStr .= "</div>\n";
		$outStr .= "<div style='clear:left;'></div>\n";
		return $outStr;
	}
	
	
	function generate_caption($num, $caption, $w, $cpos) {
		// noobslide_info_overlay 
		$outStr = '';
		if ($caption != 'none')	
			$outStr = "<div id='info{$num}' class='shiba-caption c{$cpos}' ></div>\n";
		return $outStr;	
	}
	
	
	function open_noobslide($size, $tsize, $args, $images, $all_img, $noobnum) {
		global $shiba_gallery;
		
		$num = $shiba_gallery->nsNum;
		$id = 'noobslide'.$num;
		$outStr = "";
		
		$jsStr = $this->js->noobslide_js($size, $tsize, $args, $images, $all_img, $noobnum);

		switch ($noobnum) {
		
		case '6':
			$thumbDiv = '';
			if ($tsize[0]) {
				// draw first half of thumbnails
				$num_images = count($images);
				$len = intval(ceil($num_images * 0.5));
				$vthumb_w = $tsize[0] + 6;
				$style = "width:{$vthumb_w}px;left:0px;";
				$thumbDiv .= $this->generate_thumbnails("handles{$num}_1", 'noobslide_vthumbs', $style, $tsize, $images, 0, $len);

				// draw second half of thumbnails
				$style = "width:{$vthumb_w}px;right:0px;";
				$thumbDiv .= $this->generate_thumbnails("handles{$num}_2", 'noobslide_vthumbs', $style, $tsize, $images, $len);	
			}
			
			$outStr .= 	$this->generate_containers($size, $tsize, $args, TRUE, '6' );

			if ($thumbDiv)
				$outStr = str_replace("<div class='shiba-outer'",$thumbDiv."\n<div class='shiba-outer'",$outStr);
			break;

		case '4':
		case '8':
		case 'nativex':
		case 'nativex2':
			$outStr .= $this->generate_containers($size, $tsize, $args, FALSE);
			break;
			
		default:
			$outStr .= 	$this->generate_containers($size, $tsize, $args);
			break;
		}

		$jsStr = apply_filters('shiba_js_noobslide', $jsStr, $size, $tsize, $args, $images, $all_img, $noobnum);
		$outStr = apply_filters('shiba_open_noobslide', $outStr, $size, $tsize, $args, $images, $all_img, $noobnum);
		$shiba_gallery->jsStr .= $jsStr;
		return $outStr;
	} // end open noobslide


	
	function close_noobslide($size, $tsize, $args, $images, $all_img, $noobnum) {
		global $shiba_gallery;
		
		$outStr = '';
		$num = $shiba_gallery->nsNum;
		switch ($noobnum) {
		case '1':
			$outStr .= "</div></div></div>\n";
			break;
		case '3':
			$outStr .= "</div></div></div>\n";	
			$outStr .= $this->generate_buttons($num, array('playback', 'stop', 'play'));
			break;
		case '4':
			$outStr .= "</div></div></div>\n";	
			$outStr .= "
				<h6>Show: <span id='noobslide_info{$num}' class='noobslide_info'><a href='#' onClick='nS{$num}.active(-1);return false;'></a></span></h6>";
			$outStr .= $this->generate_numcontrol("handles{$num}", $size[0], $images);	
			break;
		case '5':
			$outStr .= "</div>\n"; // close noobslide
			$outStr .= $this->generate_caption($num, $args['caption'], $size[0], $args['cpos']);
			$outStr .= "</div></div>\n"; // close noobmask
			$outStr .= $this->generate_buttons($num, array('previous', 'play', 'stop', 'next'));
			break;
			
		case '6':
			$outStr .= "</div>\n"; // close noobslide	
			$outStr .= $this->generate_caption($num, $args['caption'], $size[0], $args['cpos']);
			$outStr .= "</div></div>\n"; // close noobmask

			$outStr .= $this->generate_buttons($num, array('previous', 'playback', 'stop', 'play', 'next'));
			break;
			
			
		case '7':
			// use original here because the mask graphic is built for 54x41
			$elementW = 54; 
			$elementH = 41;
			$outerW =  $elementW + 6;
			$outStr .= "</div></div></div>\n";
			$outStr .= "<div style='clear:both;height:20px;'></div>\n";	
			
			if ($tsize[0]) {
				$outStr .= "<div class='noobslide_thumb_overlay' style='height:{$elementH}px;'>\n";
				$outStr .= "<div class='noobslide_thumbs' style='height:{$elementH}px;'>\n";
				foreach ($images as $image) {
					$img = $shiba_gallery->helper->get_attachment_image_src($image->ID, array($elementW, $elementH));
					$pad_left = intval(ceil(($outerW-$img[1])*0.5));
					$style = "padding:0px {$pad_left}px;width:{$outerW}px;height:{$elementH}px;";
					$outStr .= "<div style='width:{$outerW}px;height:{$elementH}px;'><img src='{$img[0]}' style='{$style}'/></div>\n";
				}
				$outStr .= "</div>\n";
		
				$outStr .= "<div id='thumbs_mask{$num}' class='noobslide_thumbs_mask' style=\"width:1200px;height:{$elementH}px;background:url('".SHIBA_GALLERY_URL."/images/noobslide/thumbs_mask.gif"."') no-repeat center top;\"></div>\n";
		
				$outStr .= "<p id='thumbs_handles{$shiba_gallery->nsNum}' class='noobslide_thumbs_handles' style='height:{$elementH}px;'>\n";
				foreach ( $all_img as $img ) {		
					$outStr .= "<span style='width:{$outerW}px;height:{$elementH}px;'></span>\n";
				}
				$outStr .= "</p></div>\n"; // End thumbs7
			}
			break;
			
		case '8':
			$w = $size[0];	
			$outStr .= "</div></div></div>\n";
			
			$outStr .= "
			<div class='noobslide_buttons shiba-nav panel-buttons'>
			<span id='previous{$num}' class='pb-previous'>&lt;&lt; Previous</span> | <span id='next{$num}' class='pb-next'>Next &gt;&gt;</span>
			</div>";

			$outStr .= $this->generate_buttons($num, array('playback', 'stop', 'play'));
			$outStr .= $this->generate_numcontrol("handles{$num}_more", $size[0], $images);						
			break;
			
			
		case 'slideviewer':
			$outStr .= "</div>\n"; // close noobslide	
			$outStr .= $this->generate_caption($num, $args['caption'], $size[0], $args['cpos']);
			$outStr .= "</div></div>\n"; // close noobmask
			
			$outStr .= $this->generate_numcontrol("handles{$num}", $size[0], $images); 
			break;
			
		case 'galleria':
		case 'thumb':
			$outStr .= "</div>\n"; // close noobslide	
			$outStr .= $this->generate_caption($num, $args['caption'], $size[0], $args['cpos']);
			$outStr .= "</div></div>\n"; // close noobmask
			$outStr .= "<div style='clear:both;'></div>\n";
	
			if ($noobnum == 'galleria') {
				$outStr .= $this->generate_buttons($num, array('previous', 'stop', 'play', 'next'));
			} else $outStr .= "<div style='height:10px;'></div>\n";	
			
			if ($tsize[0]) {
				$outStr .= $this->generate_thumbnails("handles{$num}", '', '', $tsize, $images);
				$outStr .= "<div style='clear:left;'></div>\n";
			}
			break;		
		case 'nativex':
			$outStr .= "</div></div></div>\n";
			$outStr .= $this->generate_buttons($num, array('playback', 'stop', 'play'));
	
			if ($tsize[0]) {
				if ($args['caption'] && $args['caption'] != 'none') 
					$outStr .= $this->generate_thumbnails("handles{$num}", '', '', $tsize, $images, 0, -1, $args['link']);
				else	
					$outStr .= $this->generate_thumbnails("handles{$num}", '', '', $tsize, $images, 0, -1);
				$outStr .= "<div style='clear:left;'></div>\n";
			}
			break;
		case 'nativex2':
			$outStr .= "</div></div></div>\n";
			$outStr .= $this->generate_buttons($num, array('playback', 'stop', 'play'));
			$outStr .= $this->generate_numcontrol("handles{$num}", $size[0], $images); 
			break;
		case '2':
		default: // defaults to sample 2
			$outStr .= "</div></div></div>\n";	
			$outStr .= $this->generate_buttons($num, array('previous', 'play', 'stop', 'next'));
			break;		
		}
		
		$outStr = apply_filters('shiba_close_noobslide', $outStr, $size, $tsize, $args, $images, $all_img, $noobnum);

		$outStr .= "</div><!-- Close noobmain -->\n"; // close noobmain		
		
		$shiba_gallery->nsNum++;
		return $outStr;		
	}	// end close noobslide
	
	
	function render_caption($image, $size, $link, $caption, $noobnum) {
		global $shiba_gallery;
		
		$description = $shiba_gallery->helper->get_attachment_description($image);

		$renderStr = "<div class='noob-data'>\n"; 
		switch($noobnum){
		case '4':
			$description = $shiba_gallery->helper->get_panel_text($image, $size);
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
			break;
		case '5':
			$title = $shiba_gallery->helper->get_attachment_title($image);
			$author = $image->post_author;
			$date = $image->post_date;
			$renderStr .= "<h4 class='title'>$title</h4>\n";
			$renderStr .= "<span class='author'>$author</span>\n";
			$renderStr .= "<span class='date'>$date</span>\n";
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption, 'link');
			$imglink = str_replace('shiba-link', 'shiba-link nooblink', $imglink);
			break;
		default:
			$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
			break;
		}
		$renderStr .= "<h4>$imglink</h4>\n";
		if (($caption == 'description') || ($caption == 'permanent')) 
			$renderStr .= "<p>{$description}</p>\n";
		$renderStr .= "</div>\n";
		return $renderStr;
	}
	
	function render($images, $args, $noobnum) {
		global $shiba_gallery;
		extract($args);

		$num = $shiba_gallery->nsNum;
		$click_event = "";
		$all_img = array(); $pimg_size = array(); $old_pimg_size = array();
		switch ($noobnum) {
		case '4':
		case '8':
		case 'nativex':
		case 'nativex2':
			
			$new_psize = $shiba_gallery->helper->get_panel_size($size, $pimg_size);
			$psize = $shiba_gallery->helper->get_panel_size($old_size, $old_pimg_size);
			$size_arr = $shiba_gallery->helper->get_gallery_size($images, $pimg_size, $old_pimg_size,$all_img, FALSE);
			if ($active) $click_event = "onClick='nS{$num}.active(-1);'"; 
//			else  $click_event = "onClick='nS{$num}.next(true);'";
			
			if (($noobnum == 'nativex') && ($tsize == 'auto') && ($new_psize[0] > 450)) 
				$tsize = 'thumbnail';
			$tsize = $shiba_gallery->helper->get_thumb_size($tsize, $new_psize[0], $new_psize[1]);
			$imgStr = $this->open_noobslide($psize, $tsize, $args, $images, $all_img, $noobnum);
			break;
		default:
			$size_arr = $shiba_gallery->helper->get_gallery_size($images, $size, $old_size, $all_img, $crop);
			$maxW = $size_arr[0]; $maxH = $size_arr[1];
			$tsize = $shiba_gallery->helper->get_thumb_size($tsize, $maxW, $maxH);
			$imgStr = $this->open_noobslide($size_arr, $tsize, $args, $images, $all_img, $noobnum);
		}	
		$j = 0; 		
		foreach ( $images as $image ) {		
			$img = $all_img[$j]; $j++;
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			if ($alt) $alt = "alt='{$alt}'";
			
			switch ($noobnum) {				
			case '4':
			case '8':
			case 'nativex':
			case 'nativex2':
				$imglink = $shiba_gallery->helper->get_attachment_link($image, $link);
				$w = $psize[0]; $h = $psize[1];
	
				$renderStr = "<div class='noobpanel' style='width:{$w}px;height:{$h}px;'>\n";
				if ($noobnum != '4') {
				  $renderStr .= "<div class='noobslide_buttons shiba-nav'>\n";
				  $renderStr .= "<span class='prev'>&lt;&lt; Previous</span>\n";
				  $renderStr .= "<span class='next'>Next &gt;&gt;</span>\n";
				  $renderStr .= "</div>\n";
				  $renderStr .= "<div style='clear:both;'></div>\n";
				}
		
				$renderStr .= "<img src='{$img[0]}' style='width:{$img[1]}px;height:{$img[2]}px;max-width:100%;' $alt $click_event/>\n"; // 

				$description = $shiba_gallery->helper->get_panel_text($image, $psize);
				$imglink = $shiba_gallery->helper->get_attachment_link($image, $link, $caption);
				$renderStr .= "<h3>$imglink</h3>\n";
				$renderStr .= "<p>$description</p>\n";

				$renderStr .= "</div>\n";
				break;
				
			default:
				$style = $shiba_gallery->helper->get_image_style($img, $crop, $maxW, $maxH);

				$renderStr = "<span class='noob-item' style='width:{$maxW}px;height:{$maxH}px;overflow:hidden;'>\n";
				$renderStr .= "<img src='$img[0]' style='$style' $alt />\n";

				$renderStr .= $this->render_caption($image, $size_arr, $link, $caption, $noobnum);
				$renderStr .= "</span>\n";
				break;
			} // end switch
			
			$imgStr .= apply_filters('shiba_render_noobslide', $renderStr, $size_arr, $args, $image, $img, $noobnum);

		} // end foreach

		switch ($noobnum) {
		case '4':
		case '8':
		case 'nativex':
		case 'nativex2':
			$imgStr .= $this->close_noobslide($psize, $tsize, $args, $images, $all_img, $noobnum);
			break;
		default:
			$imgStr .= $this->close_noobslide($size_arr, $tsize, $args, $images, $all_img, $noobnum);
		}	
		return $imgStr;
	} // end render noobslide
	
} // end class
endif;
?>