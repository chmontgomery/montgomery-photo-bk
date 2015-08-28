(function($){
shibaNative = function() {
};

shibaNative.prototype = {
	
	init : function(opt) {
		this.o = opt;
		var self = this;
//		console.log(opt);
		this.p = $('#' + this.o.id + ' .shiba-outer'); // shiba outer is the one with padding
		this.m = $('#' + this.o.id + ' .shiba-stage');

		// tie image to text links
		if (this.o.active) {
			$(this.p).click(function(e) {
//				console.log($(this).parents('.' + self.o.class).find('.shiba-link a'));
				$(this).parents('.' + self.o.class).find('.shiba-link a')[0].click();	
			});

		}

		if (!this.o.responsive) { 
			$(this.m).each(function() {
				this.style.height = self.o.height + 'px';
			});
			return; 
		}

		this.img = $('#' + this.o.id + ' .shiba-outer img');
		this.opw = parseInt($(this.p[0]).css('width')); // original parent width
		
		shibaResizeFunctions.push(this.resize.bind(this));
		// Wait until all images have loaded before triggering resize
		this.initResize();
	},
	
	initResize : function() {
		var img = this.img;
		
		// Need to check that all images are loaded
		for (var i = 0; i < img.length; i++) {
			if (img[i].naturalWidth <= 1) { // Image not loaded
				window.setTimeout(this.initResize.bind(this), 1000);
				return;
			} 
		}
		this.origWidth = 0; this.resize();
	},
				
	resize : function() {
		var p = this.p[0];
		
		// Only resize when parent container width has changed
		if (p.offsetWidth == this.origWidth)
			return;
		this.origWidth = p.offsetWidth;
		
		if (this.o['aspect']) {						 
			this.m.each(this.aspect.bind(this));
		} else {
			this.m.each(this.width.bind(this));
		}
	},
	
	aspect : function(i) {
		var p = $(this.p[i]);
		var el = $(this.m[i]);
		var img = this.img[i];
		
		// resize thumbnail container width
		p.height(this.o['aspect'] * p.width());
		el.height(this.o['aspect'] * el.width());
		return;
	},

	width : function(i) {
		var p = this.p[i];
		var el = this.m[i];
		var m = this.img[i];

		el.style.height = this.o.height + 'px';
		
		shibaGallery.adjustWidth(m, el, this.o.crop);
	}
};


})(jQuery);
