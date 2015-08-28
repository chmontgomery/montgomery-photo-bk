<!-- start footer -->
        <div class="columns sixteen">
        <div id="footer">
            <p><?php echo get_option('photography_ftext'); ?></p>
        </div>
        </div>
        <!-- end footer -->
    </div>
</div>
<!-- end wrap -->
<?php wp_footer(); ?>
<?php if ( get_option('photography_google_analytics') <> "" ) { echo stripslashes(get_option('photography_google_analytics')); } ?>
</body>
</html>