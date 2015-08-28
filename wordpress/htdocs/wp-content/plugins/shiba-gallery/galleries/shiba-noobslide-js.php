<?php
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Shiba_Gallery_NoobSlide_JS")) :

class Shiba_Gallery_NoobSlide_JS {
	var $caption_opacity = 0.7;
//	var $transition = 'Fx.Transitions.Quint.easeOut';
	var $walk;
	
	function __construct() {
	}

	function _generate_object_item($image, $link) {
		global $shiba_gallery;
		
		$title = esc_js($image->post_title);
		$author = $image->post_author;
		$date = $image->post_date;
		$imglink = $shiba_gallery->helper->get_attachment_url($image, $link);
		return "title:'$title', author:'$author', date:'$date', link:'$imglink'";
	}

	function generate_object_items($images, $link) {
		$outStr = '';
		foreach ($images as $image) {
			$outStr .= "{";
			$outStr .= $this->_generate_object_item($image, $link);
			$outStr .= "},\n";
		}
		// remove last ','
		$outStr = substr($outStr, 0, strlen($outStr)-2);
		return $outStr;
	}

	function create_items($all_img) {
		$outStr = "[";
		for ($i = 0; $i < count($all_img)-1; $i++) {
			$outStr .= $i . ",";
		}
		$outStr .= $i . "]";
		return $outStr;
	}


	function write_js($num, $item_list, $size, $options) {
		$options = apply_filters('shiba_noobslide_options', $options);
		$jsStr = "var nS{$num};\n";
		$jsStr .= "window.addEvent('domready',function(){\n";
		
		if (isset($options['before']))
			$jsStr .= $options['before'] . "\n";
		$jsStr .= "
			nS{$num} = new noobSlide({
				box: document.id('noobslide{$num}'),
				items: {$item_list},
				size: {$size},
				responsive: {$options['responsive']}";
		foreach ($options as $key => $value) {
			switch($key) {
			case 'before':
			case 'after':
				break;
			case 'buttons':
				$jsStr .= $this->add_buttons($num, $value);
				break;
			case 'fxOptions':
				$jsStr .= $this->add_fxOptions($num, $value);
				break;
			case 'walk':
				$jsStr  .= ",\n";
				$jsStr .= "onWalk: ";
				if ($value)
					$jsStr .= $value;
				break;
			default:	
				$jsStr .= ",\n $key: $value";
				break;
			}
		}
		$jsStr .= "\n});"; // Close noobSlide

		if (isset($options['after']))
			$jsStr .= $options['after'] . "\n";

		$jsStr .= "\n});\n"; // Close domready	
		return $jsStr;
	}
	
	function add_buttons($num, $buttons) {
		$jsStr  = ",\n";
		$jsStr .= "addButtons: {\n";
		foreach ($buttons as $button) {
			$button_id = "{$button}{$num}";
			$jsStr .= "\n{$button}: document.id('{$button_id}'),";
		}
		// Remove last comma
		$jsStr = substr($jsStr, 0, -1); //rtrim($jsStr, ",");
		$jsStr .= "\n}\n";
		return $jsStr;		
	}
	
	function add_fxOptions($num, $options) {
		static $default = 
			array( 	'duration' => 1000,
					'transition' => 'Fx.Transitions.Quint.easeOut',
					'wait' => 'false'
					);
			
		if ($options == FALSE) return '';	
		elseif (is_string($options)) {
			return ",\n fxOptions: $options";
		} elseif (is_array($options))
			$options = array_merge($default, $options);
		else
			$options = $default;
			
		$jsStr  = ",\n";
		$jsStr .= "fxOptions: {";
		foreach ($options as $key => $value) {
			$jsStr .= "\n$key: $value,";
		}
		// Remove last comma
		$jsStr = substr($jsStr, 0, -1); //rtrim($jsStr, ",");
		$jsStr .= "\n}";
		return $jsStr;		
	}
	
	function prepare_options($size, $tsize, $args, $noobnum) {
		global $shiba_gallery;
		
		$num = $shiba_gallery->nsNum;
		$id = 'noobslide'.$num;
		$options = array(	'autoPlay' => ($args['autoplay']) ? 'true' : 'false',
							'crop' => ($args['crop']) ? 'true' : 'false',
							'aspect' => 0,
						);
		switch ($args['responsive']) {
		case 'aspect':
			if ($size[0])
				$options['aspect'] = $size[1]/$size[0];
		case 'width':
			if (in_array($noobnum, array('4', '8', 'nativex', 'nativex2')))
				$options['panel'] = 'true';
			$responsive = TRUE;
			break;
		case 'none':
		default:
			$responsive = FALSE;
			break;
		}
		$options['responsive'] = ($responsive)?'true':'false';
		
		switch ($noobnum) {	
		case '1':
			break;
			
		case '3':
			//SAMPLE 3 (play, stop, playback)
			$options['interval'] = 1000; 
			$options['startItem'] = 0;
			$options['buttons'] = array('playback', 'stop', 'play');
			break;
			
		case '4':
			//SAMPLE 4 (walk to item)
			$options['handles'] = "$$('#handles{$num} span')"; 
			$options['walk'] = 'shibaNoobWalk.slideviewer';
			break;
			
		case '5':
			//SAMPLE 5 (mode: vertical, using 'onWalk' )
			$options['mode'] = "'vertical'";
			$options['buttons'] = array('previous', 'play', 'stop', 'next');
			if ($args['caption'] != 'none')
				$options['walk'] = 'shibaNoobWalk.walk_info_overlay'; 
			break;
			
		case '6':
			//SAMPLE 6 (on 'mouseenter' walk)
			$options['mode'] = "'vertical'";
			$options['buttons'] = array('previous', 'play', 'stop', 'playback', 'next');
			$options['button_event'] = "'click'";
			$options['handle_event'] = "'click'";
			$options['fxOptions'] = TRUE;
			$options['after'] = "nS{$num}.next();";
			if ($args['caption'] != 'none')
				$options['walk'] = 'shibaNoobWalk.walk_info_overlay'; 
			if ($tsize[0]) 
				$options['handles'] = "$$('#handles{$num}_1 div').extend($$('#handles{$num}_2 div'))";
			break;
			
		case '7':
			$outerW = 54 + 6;
			$before = "	
				var startItem = 0;
				var fxOptions{$num} = {property:'left',duration:1000, transition:Fx.Transitions.Quint.easeOut, wait:false};";
			$options['fxOptions'] = "fxOptions{$num}";
			$options['startItem'] = 'startItem';

			if ($tsize[0]) {
				$before .= "
				var thumbs_mask{$num} = document.id('thumbs_mask{$num}').setStyle('left',(startItem*{$outerW}-570)+'px').set('opacity',0.3);
				var thumbsFx = new Fx.Tween(thumbs_mask{$num},fxOptions{$num});";
				
				$options['handles'] = "$$('#thumbs_handles{$num} span')";
				$options['walk'] = "function(currentItem){ thumbsFx.start(this.currentIndex*{$outerW}-570);}";
				$options['after'] = "nS{$num}.walk(0);";
			}
			$options['before'] = "{$before}";
			break;
			
		case '8':
			$before = "var handles{$num}_more = $$('#handles{$num}_more span');";
			//more 'previous' and 'next' buttons
			$after = "			
				nS{$num}.addActionButtons('previous',$$('#noobslide{$num} .prev'));
				nS{$num}.addActionButtons('next',$$('#noobslide{$num} .next'));
				//more handle buttons
				nS{$num}.addHandleButtons(handles{$num}_more);
				//walk to item 3 witouth fx
				nS{$num}.walk(0,false,true);";	
	
//			$options['handles'] = "$$('#handles{$num} span')";
			$options['buttons'] = array('previous', 'play', 'stop', 'playback', 'next');
			$options['walk'] = 'shibaNoobWalk.panel_buttons'; 
			$options['before'] = "{$before}";
			$options['after'] = "{$after}";
			break;
			
		case 'slideviewer':
			//SAMPLE 4-modified 
			$options['handles'] = "$$('#handles{$num} span')";
			$options['walk'] = 'shibaNoobWalk.slideviewer'; 
			break;
			
		case 'galleria':
		case 'thumb':
			// modified SAMPLE 6 (on 'mouseenter' walk)
			$options['handle_event'] = "'click'";
			$options['fxOptions'] = TRUE;
			$options['walk'] = 'shibaNoobWalk.walk_info_overlay'; 
			if ($tsize[0]) 
				$options['handles'] = "$$('#handles{$num} div')";
			if ($noobnum == 'galleria') {
				$options['buttons'] = array('previous', 'play', 'stop', 'next');
				$options['button_event'] = "'click'";
			}
			break;
			
		case 'nativex':
		case 'nativex2':
			//SAMPLE 8 modified with thumbnails
			//more 'previous' and 'next' buttons
			$after = "			
				nS{$num}.addActionButtons('previous',$$('#noobslide{$num} .prev'));
				nS{$num}.addActionButtons('next',$$('#noobslide{$num} .next')); ";
			$options['handles'] = "$$('#handles{$shiba_gallery->nsNum} div')";
			$options['buttons'] = array('play', 'stop', 'playback');
			$options['after'] = "{$after}";
			if ($noobnum == 'nativex2') {
				$options['handles'] = "$$('#handles{$num} span')";
				$options['walk'] = 'shibaNoobWalk.slideviewer'; 
			}
			break;
			
		case '2':
		default:
			//SAMPLE 2 (transition: Bounce.easeOut)
			$options['interval'] = 3000; 
			$options['fxOptions'] = TRUE;
			$options['buttons'] = array('previous', 'play', 'stop', 'next');
			break;
		} // end switch
		$options = apply_filters('shiba_noobslide_options', $options);
		return $options;
	}
	
	
	function noobslide_js($size, $tsize, $args, $images, $all_img, $noobnum) {
		global $shiba_gallery;
		
		$num = $shiba_gallery->nsNum;
		$id = 'noobslide'.$num;
		$item_list = "$$('#{$id} .noob-item')"; 
//		$item_list = $this->create_items($all_img);
		$options = $this->prepare_options($size, $tsize, $args, $noobnum);
		
		$jsStr = "";
		switch ($noobnum) {	
		case '4':
			$jsStr .= $this->write_js($num, "$$('#{$id} .noobpanel')", $size[0], $options);
			break;
		
		case '5':
//			$noobObjItems = $this->generate_object_items($images, $args['link']);
			if ($args['caption'] != 'none')
				$jsStr .= "var info{$num} = document.id('info{$num}').set('opacity',{$this->caption_opacity});\n";
	
			$jsStr .= $this->write_js($num, $item_list, $size[1], $options); // "[$noobObjItems]"
			break;
		
		case '6':
//			$noobObjItems = $this->generate_object_items($images, $args['link']);
			if ($args['caption'] != 'none')
				$jsStr .= "var info{$num} = document.id('{$id}').getNext().set('opacity',{$this->caption_opacity});\n";
	
			$jsStr .= $this->write_js($num, $item_list, $size[1], $options); // "[$noobObjItems]"	
			break;
		
		case '8':
			$jsStr .= $this->write_js($num, "$$('#{$id} .noobpanel')", $size[0], $options); 
			break;
		
		case 'slideviewer':
			if ($args['caption'] != 'none') {
				$jsStr .= "var info{$num} = document.id('$id').getNext().set('opacity',{$this->caption_opacity});\n";
			}
			$jsStr .= $this->write_js($num, $item_list, $size[0], $options);
			break;
			
		case 'galleria':
		case 'thumb':
			if ($args['caption'] != 'none') {
				$jsStr .= "var info{$num} = document.id('{$id}').getNext().set('opacity',{$this->caption_opacity});\n";
			}
			$jsStr .= $this->write_js($num, $item_list, $size[0], $options);		
			break;		
	
		case 'nativex':
		case 'nativex2':
			$w = $size[0]; $h = $size[1];
			$jsStr .= $this->write_js($num, "$$('#{$id} .noobpanel')", $w, $options); 
			break;

		default:
			$jsStr .= $this->write_js($num, $item_list, $size[0], $options);
			break;

		} // end switch

		return $jsStr;
	} // end noobslide_js

} // end class
endif;
?>