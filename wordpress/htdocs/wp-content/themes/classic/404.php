<?php get_header(); ?>

        <!-- start container -->

        <div id="container">

        	<!-- start leftcol -->

            <div class="eleven columns" id="leftcol">

            	<!-- start post -->

                <div class="post">

                    <h2 class="page-title"><?php _e('Error 404'); ?></h2>

                    <!-- entry -->

                    <div class="entry">

                       	<p><?php _e('The page you are looking for does not exist. Please check the URL for typing errors, or'); ?> <a href="<?php echo home_url(); ?>" title="Go Home"><?php _e('head back home'); ?></a> <?php _e('and start over'); ?></p>

                    </div>

                    <!-- entry -->

                </div>

                <!-- end post -->

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