<?php get_header(); ?>
        <?php if ( has_post_thumbnail()) { ?>
	<!-- start imgarea -->
        <div class="columns sixteen" id="imgarea">
		<div class="thumb fullwidth"><?php the_post_thumbnail(); ?></div>
        </div>
        <!-- end imgarea -->
	<?php } ?>
        <!-- start container -->

        <div id="container">

        	<!-- start leftcol -->

            <div class="eleven columns" id="leftcol">

		<?php if(have_posts()):while(have_posts()):the_post(); ?>

            	<!-- start post -->

                <div class="post">

                    <h2 class="page-title"><?php the_title(); ?></h2>

                    <!-- entry -->

                    <div class="entry">

                        <?php the_content(); ?> 

                    </div>

                    <!-- entry -->

                </div>

                <!-- end post -->

                <?php endwhile; endif; ?>

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