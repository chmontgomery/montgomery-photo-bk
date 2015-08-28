<?php
/*
Template Name: Portfolio
*/
 get_header();
?>
	<!-- start container -->
	<div id="container">
		<div class="columns sixteen">
        		<h2 class="page-title"><?php _e('Portfolio'); ?></h2>
		</div>
            	<?php query_posts(array('post_type'=>'portfolios', 'posts_per_page' => 9, 'order' => 'DESC', 'paged' => get_query_var('paged') ) ); ?>
        	<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
		<div class="portfolio">
            		<!-- start common -->
            		<div class="one-third column Common1">
       	    			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumb-m', $thumb_attrs); ?></a>
            		</div>
            	<!-- end common -->
            	<?php endwhile; ?>
		</div>
		<?php endif; ?>
		<div class="clear"></div>
            	<!-- pagination -->
            	<div class="columns sixteen">
            	<!-- pagination -->
            	<div class="pagination">
			<div class="left"><?php previous_posts_link('Newer Items') ?></div>
			<div class="right"><?php next_posts_link('Older Items','') ?></div>
			<div class="clear"></div>
		</div>
            	<!-- pagination -->
		</div>
		<?php wp_reset_query(); ?>
        </div>
        <!-- end container -->
<?php get_footer(); ?>