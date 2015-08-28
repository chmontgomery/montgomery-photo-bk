<?php get_header();  ?>
 <!-- start container -->
        <div id="container">
        	<!-- start leftcol -->
            <div class="eleven columns" id="leftcol">
			<h3 class="result-heading"><?php _e('Search results for'); ?> &ldquo;<?php printf(__('%s'), $s) ?>&rdquo;</h3>
                <!-- post -->
                <?php if(have_posts()):while(have_posts()):the_post(); ?>
          	<div class="post excerpt" id="post-<?php the_ID(); ?>">
                    	<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              		<div class="meta"><?php _e('Published '); the_time('F j, Y'); ?></div>
                </div>
                <!-- post -->
                <?php endwhile; endif; ?>
                
                <!-- pagination -->
            	<div class="pagination results">
			<div class="left"><?php previous_posts_link('Newer Posts/Pages') ?></div>
			<div class="right"><?php next_posts_link('Older Posts/Pages','') ?></div>
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