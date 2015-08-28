<?php
/*
Template Name: Home
*/
get_header();
?>
<script type="text/javascript">
		jQuery(window).load(function() { jQuery('#slider').nivoSlider({ effect: '<?php echo get_option('photography_slideshow_effect'); ?>', slices: 5, boxCols: 5, boxRows: 5, animSpeed: 700, directionNav:false, controlNav: true, pauseTime: <?php if (get_option('photography_slideshow_pausetime') != "") { echo get_option('photography_slideshow_pausetime'); } else { echo 7000; } ?> }); });
		</script>

<!-- start showcase -->
        <div class="columns sixteen" id="showcase">
        	<div class="slider-wrapper theme-default">
            	<div id="slider" class="nivoSlider">
                 <?php query_posts(array('post_type'=>'slide','posts_per_page'=>15)); ?>
                 <?php if(have_posts()):while(have_posts()):the_post(); ?>
   	    			<?php the_post_thumbnail(); ?>
                 <?php endwhile; endif; wp_reset_query(); ?>
                </div>
            </div>
        </div>
        <!-- end showcase -->
        <div class="clear"></div>
        <!-- start container -->
        <div id="container">
		<div class="columns sixteen">
			<div class="heading">
        			<h2>From the Blog</h2>
			</div>
		</div>
            	<!-- start common1 -->
            	<?php query_posts(array('post_type'=>'post','posts_per_page'=>3)); ?>
            	<?php if(have_posts()):while(have_posts()):the_post(); ?>
            	<div class="column one-third Common1">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumb-m', $thumb_attrs); ?></a>
                	<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                	<p class="meta"><?php _e('Published '); ?><?php the_time('F j, Y'); ?></p>
            	</div>
            	<!-- end common1 -->
            	<?php endwhile; endif; wp_reset_query(); ?>
          	<div class="clear"></div>
        </div>
        <!-- end container -->
<?php get_footer(); ?>