<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head profile="http://gmpg.org/xfn/11">

<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css' />

<link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,700italic,700,400italic' rel='stylesheet' type='text/css' />

<title>

<?php if ( is_home() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php bloginfo('description'); ?><?php } ?>

<?php if ( is_search() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Search Results',photographythemes); ?><?php } ?>

<?php if ( is_author() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Author Archives',photographythemes); ?><?php } ?>

<?php if ( is_single() ) { ?><?php wp_title(''); ?>&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>

<?php if ( is_page() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php wp_title(''); ?><?php } ?>

<?php if ( is_category() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Archive',photographythemes); ?>&nbsp;|&nbsp;<?php single_cat_title(); ?><?php } ?>

<?php if ( is_month() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Archive',photographythemes); ?>&nbsp;|&nbsp;<?php the_time('F'); ?><?php } ?>

<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Tag Archive',photographythemes); ?>&nbsp;|&nbsp;<?php  single_tag_title("", true); } } ?>

<?php if ( is_tax() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );  $term_title = $term->name; echo "$term_title"; ?> - Gallery<?php } ?>

</title>

<link href='http://fonts.googleapis.com/css?family=Bevan' rel='stylesheet' type='text/css' />

<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('photography_feedburner_url') <> "" ) { echo get_option('photography_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_single() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php echo get_option( 'photography_exscripts' ); ?>

<?php wp_head(); ?>

<!--[if lt IE 9]>

	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>

	<link rel='stylesheet' href='<?php bloginfo('template_directory'); ?>/css/ie.css' type='text/css' media='all' />

<![endif]-->

</head>

<body>

<!-- start wrap -->

<div id="wrap">

    <div class="container">

        <!-- start header -->

        <div class="columns sixteen" id="header">

            <!-- logo -->

            <div id="logo">

                <div><span><a href="<?php echo home_url(); ?>" title="<?php bloginfo('title'); ?>"><?php if ( get_option( 'photography_logo' ) <> "" ) { ?><img src="<?php echo get_option( 'photography_logo' ); ?>" alt="logo" /><?php } else { ?><?php echo bloginfo('title'); ?><?php } ?></a></span></div>

            </div>

            <!-- logo -->

            <!-- nav -->

            <div id="navigation">

		<div class="style-select">

                <?php

			wp_nav_menu( array(

			'theme_location'	=> 'main-nav-menu',

			'menu_id'		=> 'header-menu-links',

			'container'		=> false, // don't wrap in div

				) );

		?>

		</div>

            </div>

            <!-- nav -->

        </div>

        <!-- end header -->