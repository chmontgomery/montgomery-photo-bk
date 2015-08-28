<div id="search_main" class="widget">
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
		<div>
			<input type="text" class="field" name="s" id="s"  value="<?php _e('to search, type and hit enter',photographythemes); ?>" onfocus="if (this.value == '<?php _e('to search, type and hit enter',photographythemes); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('to search, type and hit enter',photographythemes); ?>';}" />
			<input type="submit" class="submit" name="submit" value="<?php _e('Search...',photographythemes); ?>" />
		</div>
	</form>
</div>

