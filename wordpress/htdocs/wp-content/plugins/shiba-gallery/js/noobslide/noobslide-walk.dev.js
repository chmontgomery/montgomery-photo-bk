shibaNoobWalk = {
	setInfo : function(main, currentItem){
//		console.log(main);
		var info = main.info;
		var c = main.currentIndex;
		
		if (info && (typeof info != 'undefined')) {
			info.empty();

			var l = main.links[c];
			var cap = main.cap[c];
			var p = main.descriptions[c];
			if (!cap) {
				cap = main.cap[main.currentIndex] = currentItem.getElement('.noob-data .title');
				if (cap != null) {
					cap.grab(l);
					// Add in description
					if (p) cap.grab(p);
				}
			}
			if (cap != null) {
				info.grab(cap); 
			} else if (l != null) { 
				info.grab(l.parentNode);
				if (p) info.grab(p);
				
			} else {
				new Element('h4').set('html',currentItem.getElement('img').getProperty('alt')).inject(info);
			} 

		}
	},
	
	walk_info_overlay : function(currentItem, currentHandle){
//		console.log(this);
		shibaNoobWalk.setInfo(this, currentItem);
		
//		new Element('p').set('html','<b>Author</b>: '+currentItem.author+' &nbsp; &nbsp; <b>Date</b>: '+currentItem.date).inject(info{$id});
		
		if (currentHandle != null && this.handles != null) {
			this.handles.set('opacity',0.3);
			currentHandle.set('opacity',1);
		}
	},

	slideviewer : function(currentItem,currentHandle){
		var info = this.info;
		var addInfo = this.main.getElement('.noobslide_info a');
		var l = this.links[this.currentIndex];
		if (addInfo != null) {
			addInfo.empty();
			addInfo.set('html',l.innerHTML);
		}
		shibaNoobWalk.setInfo(this, currentItem);

		if (currentHandle != null && this.handles != null) {	
			this.handles.removeClass('active');
			currentHandle.addClass('active');
		}
	},

	panel_buttons : function(currentItem,currentHandle){
		var hgroup = this.main.getElement('.noobslide_numcontrol');
		if (!this.pbHandles) {
			this.pbHandles = hgroup.getElements('span');
		}
		//style to properly highlight number thumbnails
		this.pbHandles.removeClass('active');
		$$(currentHandle, this.pbHandles[this.currentIndex]).addClass('active');

		var plink = this.links[this.previousIndex].innerHTML;
		var nlink = this.links[this.nextIndex].innerHTML;
		
		//text for 'previous' and 'next' default buttons
		this.main.getElement('.panel-buttons .pb-previous').set('html','&lt;&lt; '+ plink);
		this.main.getElement('.panel-buttons .pb-next').set('html',nlink +' &gt;&gt;');
	},

};

