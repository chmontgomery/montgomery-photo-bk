<?php get_header(); ?>
<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
<!-- start imgarea -->
        <div class="columns sixteen" id="imgarea">
        	<?php if ( has_post_thumbnail()) { ?>
   	    		<div class="thumb fullwidth"><?php the_post_thumbnail(); ?></div>
			<?php include (TEMPLATEPATH . '/includes/portfolio-prev-next.php' ); ?>
		<?php } ?>
        </div>
        <!-- end imgarea -->
        <!-- start container -->
        <div id="container">
        	<div class="columns sixteen">
        	<!-- portfolio item -->
            	<div class="post">
                	<h2 class="post-title"><?php the_title(); ?></h2>
                	<span class="meta"><?php _e('Published in '); ?><?php the_terms( $post->ID, 'gallery', '', ', ', ' ' ); ?></span>
        		<!-- entry -->
                	<div class="entry">
                    		<?php the_content(); ?>
                	</div>
                	<!-- entry -->
            	</div>
            	<!-- post -->
            </div>
        <div class="clear"></div>
        </div>
        <!-- end container -->
<?php endwhile; endif; ?>
<?php get_footer(); ?>