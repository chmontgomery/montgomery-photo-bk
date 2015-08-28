<?php 
header("Content-type: text/css");
if(file_exists('../../../../wp-load.php')) :
	include '../../../../wp-load.php';
else:
	include '../../../../wp-load.php';
endif; 

ob_flush();
?>
body {
	background:#<?php echo get_option('photography_body_background_color'); ?>;  /**body color */
	color:#<?php echo get_option('photography_body_text_color'); ?>; 
}
.heading h2{
	background:#<?php echo get_option('photography_body_background_color'); ?>;
}
a { color:#<?php echo get_option('photography_link_color'); ?>; } /* link */
#wrap {
	border-top:solid 5px #<?php echo get_option('photography_wrap_color'); ?>; /*topborder*/
}
#logo, #logo a {
	color:#<?php echo get_option('photography_logo_color'); ?>;
}
#navigation a {
	color:#<?php echo get_option('photography_nav_link_color'); ?>;
}

h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a{
	color:#<?php echo get_option('photography_heading_color'); ?>;
}
a:hover, #navigation a:hover, #navigation a.current-menu-item, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover {
	color:#<?php echo get_option('photography_link_hover_color'); ?>;
}
#navigation {
	border-top-color:#<?php echo get_option('photography_global_border_color'); ?>;
	border-bottom-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.slider-wrapper{
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.Common1 .img, .Common2 img {
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.heading {
	border-bottom-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.post.listing img.thumb {
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.widget .input, .widget input[type="text"] {
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#comments li {
	border-bottom-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#comments ol ul {
	border-top-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#comments li li {
	border-top-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#respond .input{
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#respond .textarea{
	border-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
#footer {
	border-top-color:#<?php echo get_option('photography_global_border_color'); ?>;
}
.widget .button, .widget input[type="submit"], #respond .button, .button {
	background:#<?php echo get_option('photography_global_button_color'); ?>;
	color:#<?php echo get_option('photography_global_button_text_color'); ?>;
}
.widget .button:hover, .widget input:hover[type="submit"], #respond .button:hover, .button:hover {
	background-color:#<?php echo get_option('photography_global_button_hover_color'); ?>;
}
.meta{
	color:#<?php echo get_option('photography_global_meta_color'); ?>;
}