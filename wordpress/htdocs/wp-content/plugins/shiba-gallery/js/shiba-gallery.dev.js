(function($){
shibaGallery = {
	
	updateImg : function(m) {		
		// reset the width to make sure it updates
		var mw = m.style.width;
		m.style.width = 'auto';
		var tmp = m.offsetWidth; // Need this statement to make sure image updates
		m.style.width = mw;
	},

	expandImg : function(m, stage, dw, dh) {
		if ( dw >= dh ) { 
			m.style.width = stage.offsetWidth + 'px'; //'100%';
			m.style.height = 'auto';
		} else {
			m.style.width = 'auto';
			m.style.height = '100%';				
		} 
	},

	adjustWidth : function(m, stage, crop) {
//		console.log("m " + m.offsetWidth);
		m.style.padding = 0;
		m.style.marginLeft = '0px';
		m.style.width = 'auto';
		m.style.height = 'auto';

		var dw = m.offsetWidth - stage.offsetWidth;
		var dh = m.offsetHeight - stage.offsetHeight;
			
		if ((dw <= 0) && (dh <= 0)) { // If image does not fill parent space
			this.expandImg(m, stage, dw, dh);
		}
		if (!crop) {
			if ((dw <= 0) && (dh <= 0)) { // no constraint needed
			} else 	{
				this.expandImg(m, stage, dw, dh);	
			}
		} 

		var w=parseInt(stage.offsetWidth)-parseInt(m.offsetWidth),
			pw=Math.floor(w/2),
			h=parseInt(stage.offsetHeight)-parseInt(m.offsetHeight),
			ph=Math.floor(h/2);

		if(w>0){
			m.style.paddingLeft=pw+'px';
			m.style.paddingRight=(w-pw)+'px';
		} else {
			if (crop) {
				m.style.marginLeft=pw+'px';
			}
		}
		if(h>0){
			m.style.paddingTop=ph+'px';
			m.style.paddingBottom=(h-ph)+'px';
		} else {
		}				
	}
};


})(jQuery);
