/*
Author:
	luistar15, <leo020588 [at] gmail.com>
License:
	MIT License
 
Class
	noobSlide (rev.19-06-08)

Arguments:
	Parameters - see Parameters below

Parameters:
	box: dom element | required
	items: dom collection | required
	size: int | item size (px) | default: 240
	mode: string | 'horizontal', 'vertical' | default: 'horizontal'
	addButtons:{
		previous: single dom element OR dom collection| default: null
		next:  single dom element OR dom collection | default: null
		play:  single dom element OR dom collection | default: null
		playback:  single dom element OR dom collection | default: null
		stop:  single dom element OR dom collection | default: null
	}
	button_event: string | event type | default: 'click'
	handles: dom collection | default: null
	handle_event: string | event type| default: 'click'
	fxOptions: object | Fx.Tween options | default: {duration:500,wait:false}
	interval: int | for periodical | default: 5000
	autoPlay: boolean | default: false
	onWalk: event | pass arguments: currentItem, currentHandle | default: null
	startItem: int | default: 0

Properties:
	box: dom element
	items: dom collection
	size: int
	mode: string
	buttons: object
	button_event: string
	handles: dom collection
	handle_event: string
	previousIndex: int
	nextIndex: int
	fx: Fx.Tween instance
	interval: int
	autoPlay: boolean
	onWalk: function
	
Methods:
	previous(manual): walk to previous item
		manual: bolean | default:false
	next(manual): walk to next item
		manual: bolean | default:false
	play (interval,direction,wait): auto walk items
		interval: int | required
		direction: string | "previous" or "next" | required
		wait: boolean | required
	stop(): stop auto walk
	walk(item,manual,noFx): walk to item
		item: int | required
		manual: bolean | default:false
		noFx: boolean | default:false
	addHandleButtons(handles):
		handles: dom collection | required
	addActionButtons(action,buttons):
		action: string | "previous", "next", "play", "playback", "stop" | required
		buttons: dom collection | required

Requires:
	mootools 1.2 core
*/
var noobSlide = new Class({

	initialize: function(params){
		this.items = params.items;
		// Shiba Gallery
		var self = this;
		this.imgs = params.box.getElements('img');
		this.links = params.box.getElements('a');
		this.descriptions = params.box.getElements('p');
		this.stage = params.box.parentNode;
		this.main = this.stage.parentNode.parentNode;
		this.info = this.stage.getElement('.shiba-caption');
		this.cap = new Array();		
		
		this.crop = params.crop || false;
		this.responsive = params.responsive || false;
		this.aspect = params.aspect || 0;
		this.panel = params.panel || false;
		
		this.mode = params.mode || 'horizontal';
		this.modes = {horizontal:['left','width'], vertical:['top','height']};
		this.size = params.size || 240;
		this.box = params.box.setStyle(this.modes[this.mode][1],(this.size*this.items.length)+'px');
		this.button_event = params.button_event || 'click';
		this.handle_event = params.handle_event || 'click';
		this.onWalk = params.onWalk || null;
		this.currentIndex = null;
		this.previousIndex = null;
		this.nextIndex = null;
		this.interval = params.interval || 5000;
		this.autoPlay = params.autoPlay || false;
		this._play = null;
		this.handles = params.handles || null;
		if(this.handles){
			this.addHandleButtons(this.handles);
		}
		this.buttons = {
			previous: [],
			next: [],
			play: [],
			playback: [],
			stop: []
		};
		if(params.addButtons){
			for(var action in params.addButtons){
				this.addActionButtons(action, $type(params.addButtons[action])=='array' ? params.addButtons[action] : [params.addButtons[action]]);
			}
		}
		this.fx = new Fx.Tween(this.box,$extend((params.fxOptions||{duration:500,wait:false}),{property:this.modes[this.mode][0]}));
		this.walk((params.startItem||0),true,true);
		
		// Shiba Gallery
		// If responsive, make all image span's hidden
		this.wrapper = new Array();
		if (this.responsive) {
			this.origWidth = 0; //this.stage.offsetWidth;
			for(var i=0;i<this.items.length;i++){
				// wrap image in another span
				this.items[i].style.visibility = 'hidden';
				var children = this.items[i].getChildren();
//				console.log("children " + children.length);
				if (children.length <= 2)
					this.wrapper[i] = new Element('span').addClass('wrapper').wraps(this.imgs[i]);
				else {	
					this.wrapper[i] = new Element('span').addClass('wrapper');
					this.wrapper[i].style.visibility = 'visible';
					children.each(function(e) {
						self.wrapper[i].wraps(e);
					});
				}
				this.wrapper[i].style.width = this.items[i].style.width;
				this.wrapper[i].style.height = this.items[i].style.height;
//				this.imgs[i].style.visibility = 'visible';
			}
//			this.cap[0].style.visibility = 'visible';
			this.imgs[0].style.visibility = 'visible';

			shibaResizeFunctions.push(this.resize.bind(this));
			this.initResize();
		}		

	},

	addHandleButtons: function(handles){
		for(var i=0;i<handles.length;i++){
			handles[i].addEvent(this.handle_event,this.walk.bind(this,[i,true]));
		}
	},

	addActionButtons: function(action,buttons){
		for(var i=0; i<buttons.length; i++){
			switch(action){
				case 'previous': buttons[i].addEvent(this.button_event,this.previous.bind(this,[true])); break;
				case 'next': buttons[i].addEvent(this.button_event,this.next.bind(this,[true])); break;
				case 'play': buttons[i].addEvent(this.button_event,this.play.bind(this,[this.interval,'next',false])); break;
				case 'playback': buttons[i].addEvent(this.button_event,this.play.bind(this,[this.interval,'previous',false])); break;
				case 'stop': buttons[i].addEvent(this.button_event,this.stop.bind(this)); break;
			}
			this.buttons[action].push(buttons[i]);
		}
	},

	previous: function(manual){
		this.walk((this.currentIndex>0 ? this.currentIndex-1 : this.items.length-1),manual);
	},

	next: function(manual){
		this.walk((this.currentIndex<this.items.length-1 ? this.currentIndex+1 : 0),manual);
	},

	play: function(interval,direction,wait){
		this.stop();
		if(!wait){
			this[direction](false);
		}
		this._play = this[direction].periodical(interval,this,[false]);
	},

	stop: function(){
		$clear(this._play);
	},


	walk: function(item,manual,noFx){
		if(item!=this.currentIndex){
			this.currentIndex=item;
			this.previousIndex = this.currentIndex + (this.currentIndex>0 ? -1 : this.items.length-1);
			this.nextIndex = this.currentIndex + (this.currentIndex<this.items.length-1 ? 1 : 1-this.items.length);
			if(manual){
				this.stop();
			}
			if(noFx){
				this.fx.cancel().set((this.size*-this.currentIndex)+'px');
			}else{
				// Shiba Gallery change - added cancel() in case previous fx is still running
				this.fx.cancel().start(this.size*-this.currentIndex);
			}
			
			if(manual && this.autoPlay){
				this.play(this.interval,'next',true);
				this.autoPlay = false; // Shiba Gallery change - Only autoPlay once at the start
			}
			if(this.onWalk){
				this.onWalk((this.items[this.currentIndex] || null), (this.handles && this.handles[this.currentIndex] ? this.handles[this.currentIndex] : null));
			}
			if (this.responsive && this.wrapper)
				this.resizeImg(); // Shiba Gallery		
			

		}
	},
	
	// Shiba Gallery
	active: function(i) {
		var num = i>0 ? i : this.currentIndex,
			l = this.links[num];
//		console.log(jQuery._data( l, "events" ));
		jQuery(l).click();

//		l.click(); // works
//		l.fireEvent('click'); // nope
//		var evt = document.createEvent('MouseEvents'); // goes directly to file - no slimbox
//		evt.initEvent('click', true, false);
//		l.dispatchEvent(evt);
		
	},

	initResize : function() {
		var img = this.imgs;
		
		// Need to check that first image is loaded
		if (img[0].naturalWidth <= 1) { // Image not loaded
			window.setTimeout(this.initResize.bind(this), 1000);
		} else
			this.origWidth = 0; this.resize();
	},

	resize: function() {
		if (this.stage.offsetWidth == this.origWidth)
			return;
		this.origWidth = this.stage.offsetWidth;
		this.resizeImg();		
	},
	
	resizeImg: function() {
		var imgs = this.imgs;
		var stage = this.stage;
		var m = this.imgs[this.currentIndex];
		var p = this.wrapper[this.currentIndex];

		m.style.visibility = 'visible';

		// Set visibility of previous & next image to be hidden
		if (imgs[this.c+1]) {
			imgs[this.c+1].style.visibility = 'hidden';
		}
		if (imgs[this.c-1]) {
			imgs[this.c-1].style.visibility = 'hidden';
		}

		// resize thumbnail container width
		if (this.aspect) {
			stage.style.height = (this.aspect * stage.offsetWidth)+'px';

			p.style.width = stage.offsetWidth + 'px';
			p.style.height = stage.offsetHeight + 'px';

			shibaGallery.updateImg(m);
			return;
		} else if (this.panel) {
			p.style.width = stage.offsetWidth + 'px';
			return;			
		}

		p.style.width = stage.offsetWidth + 'px';
		p.style.height = stage.offsetHeight + 'px';

		shibaGallery.adjustWidth(m, stage, this.crop);

	}
		

	
});