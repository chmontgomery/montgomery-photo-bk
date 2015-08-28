<?php
/*
* gallery taxonomy archive
*/
get_header();
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
?>
<!-- start container -->
        <div id="container">
		<div class="columns sixteen">
        		<h2 class="page-title"><?php echo apply_filters( 'the_title', $term->name ); ?></h2>
		</div>
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
        </div>
        <!-- end container -->
<?php get_footer(); ?>