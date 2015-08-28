<?php get_header();  ?>
 <!-- start container -->
        <div id="container">
        	<!-- start leftcol -->
            <div class="eleven columns" id="leftcol">
                <!-- post -->
                <?php if(have_posts()):while(have_posts()):the_post(); ?>
          	<div class="post listing" id="post-<?php the_ID(); ?>">
                    	<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              		<div class="meta"><?php _e('Published '); the_time('F j, Y'); _e(' in '); the_category(', '); _e(' - '); comments_popup_link('No Comments', '1 Comment', '% Comments'); ?></div>
			<?php if ( has_post_thumbnail()) { ?>
            		<div class="thumb">
				<?php the_post_thumbnail(); ?>
			</div>
			<?php } ?>
                </div>
                <!-- post -->
                <?php endwhile; endif; ?>
                
                <!-- pagination -->
            	<div class="pagination">
			<div class="left"><?php previous_posts_link('Newer Posts') ?></div>
			<div class="right"><?php next_posts_link('Older Posts','') ?></div>
		</div>
            	<!-- pagination -->
            </div>
            <!-- end leftcol -->
            <!-- start rightcol -->
            <div class="five columns" id="rightcol">
                <?php get_sidebar(); ?>
            </div>
            <!-- end rightcol -->
          	<div class="clear"></div>
        </div>
        <!-- end container -->
	<?php get_footer(); ?>