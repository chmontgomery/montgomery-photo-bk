//(function($){
var TINY={};

function T$(i){return document.getElementById(i)}
function T$$(e,p){return p.getElementsByTagName(e)}

TINY.slider=function(){
	/* 
		Shiba Gallery
	 	this.n = id of main div
		this.g = list of thumbnail <li>s
		this.m = list of image <li>s
		this.c = current slide number
		this.l = number of images
		this.w = original width
		this.x = div .slider or .shiba-stage
	 */
 
	function slide(n,p){this.n=n; this.init(p)}
	slide.prototype.init=function(p){
		
		// Shiba Gallery
		var self = this;
		this.responsive = p.responsive;
		this.crop = p.crop;
		this.aspect = p.aspect;
		this.navc = T$(p.navid);
		
		var s=this.x=T$(p.id), u=this.u=T$$('ul',s)[0], c=this.m=T$$('li',u), l=c.length, i=this.l=this.c=0; this.b=1;
		
		if(p.navid&&p.activeclass){this.g=T$$('li',T$(p.navid)); this.s=p.activeclass; }
		this.a=p.auto||0; this.p=p.resume||0; this.r=p.rewind||0; this.e=p.elastic||false; this.v=p.vertical||0; s.style.overflow='hidden';
		this.si = p.slideInterval||.1; // Shiba Gallery add 
		for(i;i<l;i++){if(c[i].parentNode==u){this.l++}}
		if(this.v){;
			u.style.top=0; this.h=p.height||c[0].offsetHeight; u.style.height=(this.l*this.h)+'px'
		}else{
			u.style.left=0; this.w=p.width||c[0].offsetWidth; u.style.width=(this.l*this.w)+'px'
		}
		this.nav(p.position||0);
		if(p.position){this.pos(p.position||0,this.a?1:0,1)}else if(this.a){this.auto()}
		if(p.left){this.sel(p.left)}
		if(p.right){this.sel(p.right)}
		
		// Shiba Gallery
		// Bind imglink to text links - this is to allow slimbox for active imglink
		var elements = T$$('a',u);
//		this.imglinks = new Array();
		this.textlinks = new Array();
		for(var i=0;i<elements.length;i++){
//      		if(elements[i].className == 'imglink') {
//         		this.imglinks.push(elements[i]);
//      		}
      		if(elements[i].className == 'shiba-link') {
         		this.textlinks.push(elements[i]);
      		}
   		}

		// Shiba Gallery - get all images, image spans, and captions
		this.img = T$$('img',u);
		this.cap = T$$('div',u);
		this.ispan = T$$('span',u);
		this.gdiv = this.x.parentNode.parentNode;

		this.imglink = T$(p.imgLink);
		if (this.imglink) {
			this.imglink.onclick=function(e){
				self.textlinks[self.c].click();
				return false;
			};
		} else { // bind image click to next
			for(var i=0;i<this.img.length;i++){
				this.img[i].onclick = function() {
					self.pos(self.c+1, 0, 1);
				};
			}
		}
			
		// If responsive, make all image li's hidden but all images visible
		if (this.responsive) {
			this.origWidth = 0; // this.gdiv.offsetWidth;
			for(var i=0;i<l;i++){
				this.m[i].style.visibility = 'hidden';				
//				this.img[i].style.visibility = 'visible';
			}
			this.cap[0].style.visibility = 'visible';
			this.img[0].style.visibility = 'visible';

			shibaResizeFunctions.push(this.resize.bind(this));
			this.initResize();
		} else {
			var cstyle = this.cap[0].currentStyle || document.defaultView.getComputedStyle(this.cap[0], null);
			this.capBottom = parseInt(cstyle.bottom);
			this.posCaption();
		}
	},
	slide.prototype.auto=function(){
		this.x.ai=setInterval(new Function(this.n+'.move(1,1,1)'),this.a*1000)
	},
	slide.prototype.move=function(d,a){
		var n=this.c+d;
		if(this.r){n=d==1?n==this.l?0:n:n<0?this.l-1:n}
		this.pos(n,a,1)
	},
	slide.prototype.initResize=function() {
		var img = this.img;
		
		// Need to check that first image is loaded
		if (img[0].naturalWidth <= 1) { // Image not loaded
			window.setTimeout(this.initResize.bind(this), 1000);
		} else
			this.origWidth = 0; this.resize();
	},

	slide.prototype.resize=function() {
		// Only resize when parent container width has changed
		if (this.gdiv.offsetWidth == this.origWidth)
			return;
		this.origWidth = this.gdiv.offsetWidth;
		this.resizeImg();
	},
	slide.prototype.posCaption = function() {
		if (!this.crop) return;
		var i = this.c;
		this.cap[i].style.bottom = this.img[i].offsetHeight - this.x.offsetHeight + this.capBottom + 'px';
	},
	slide.prototype.resizeImg=function() {
		// Shiba Gallery - Make gallery mobile responsive by adjusting image widths and 
		// heights accordingly	

		var c = this.c,
			m = this.img[c],
			parent = this.m[c],
			s = this.ispan[c],
			cap = this.cap[c];
		
		// Check if we have to make space for sliders
		this.x.style.width = '100%';
		var stageW = this.x.offsetWidth + (this.x.offsetLeft * 2);
		if (this.gdiv.offsetWidth < stageW) { // Check if slide space has been calculated
			var cstyle = this.x.currentStyle || document.defaultView.getComputedStyle(this.x, null),
				ml = parseInt(cstyle.marginLeft);
				
			stageW = this.x.offsetWidth - (ml * 2);
			this.x.style.width = stageW + 'px';
//			console.log(stageW);
		}
		// Only do this if there are thumbnails
//		console.log(this.navc.className);
		if (this.navc.className == 'thumb')
			this.navc.style.width = this.gdiv.offsetWidth+'px';

		m.style.visibility = 'visible';
		cap.style.visibility = 'visible';
		// Size caption according to image width
		cap.style.width = this.x.offsetWidth + 'px';

		// Set visibility of previous & next image to be hidden
		if (this.img[this.c+1]) {
			this.img[this.c+1].style.visibility = 'hidden';
			this.cap[this.c+1].style.visibility = 'hidden';
		}
		if (this.img[this.c-1]) {
			this.img[this.c-1].style.visibility = 'hidden';
			this.cap[this.c-1].style.visibility = 'hidden';
		}

		if (this.aspect) {
			// need to change gallery height
			this.gdiv.style.height = 'auto';
			
			this.x.style.height = (this.aspect * this.x.offsetWidth)+'px';

			s.style.width = this.x.offsetWidth + 'px';
			s.style.height = this.x.offsetHeight + 'px';
			
			shibaGallery.updateImg(m);
			// reposition caption bottom 
			if (cap.style.bottom)
				cap.style.bottom = parent.offsetHeight - this.x.offsetHeight + 'px';
			return;
		}

		s.style.width = this.x.offsetWidth + 'px';
		s.style.height = this.x.offsetHeight + 'px';

		shibaGallery.adjustWidth(m, this.x, this.crop);
	},
	
	slide.prototype.pos=function(p,a,m){
		/* Shiba Gallery
			p = slide number
			a = forward or backward?
			m = number of slides to move?
		*/
//		console.log("pos " + p + " " + a + " " + m);
		// Shiba Gallery - needs this to make sure visibility is properly set
		if (p >= this.img.length) p = 0; 
		var v=p; clearInterval(this.x.ai); clearInterval(this.x.si);
		if(!this.r){
			if(m){
				if(p==-1||(p!=0&&Math.abs(p)%this.l==0)){
					this.b++;
					for(var i=0;i<this.l;i++){this.u.appendChild(this.m[i].cloneNode(1))}
					this.v?this.u.style.height=(this.l*this.h*this.b)+'px':this.u.style.width=(this.l*this.w*this.b)+'px';
				}
				if(p==-1||(p<0&&Math.abs(p)%this.l==0)){
					this.v?this.u.style.top=(this.l*this.h*-1)+'px':this.u.style.left=(this.l*this.w*-1)+'px'; v=this.l-1
				}
			}else if(this.c>this.l&&this.b>1){
				v=(this.l*(this.b-1))+p; p=v
			}
		}
		var t=this.v?v*this.h*-1:v*this.w*-1, d=p<this.c?-1:1; this.c=v; var n=this.c%this.l; this.nav(n);
		if(this.e){t=t-(8*d)}
		this.x.si=setInterval(new Function(this.n+'.slide('+t+','+d+',1,'+a+')'), 10);
		// Shiba Gallery
		if (this.responsive) {
			this.resizeImg();
		} else { this.posCaption(); }
	},
	slide.prototype.nav=function(n){
		if(this.g){for(var i=0;i<this.l;i++){this.g[i].className=i==n?this.s:''}}
	},
	slide.prototype.slide=function(t,d,i,a){
		
		var o=this.v?parseInt(this.u.style.top):parseInt(this.u.style.left);
		if(o==t){
			clearInterval(this.x.si);
			if(this.e&&i<3){
				this.x.si=setInterval(new Function(this.n+'.slide('+(i==1?t+(12*d):t+(4*d))+','+(i==1?(-1*d):(-1*d))+','+(i==1?2:3)+','+a+')'), 10)
			}else{
				if(a||(this.a&&this.p)){this.auto()}
				if(this.b>1&&this.c%this.l==0){this.clear()}
			}
		}else{
			// Shiba Gallery change - replaced .1 with this.si
			var v=o-Math.ceil(Math.abs(t-o)*this.si)*d+'px';
			this.v?this.u.style.top=v:this.u.style.left=v
		}
	},
	slide.prototype.clear=function(){
		var c=T$$('li',this.u), t=i=c.length; this.v?this.u.style.top=0:this.u.style.left=0; this.b=1; this.c=0;
		for(i;i>0;i--){
			var e=c[i-1];
			if(t>this.l&&e.parentNode==this.u){this.u.removeChild(e); t--}
		}
	},
	slide.prototype.sel=function(i){
		var e=T$(i); e.onselectstart=e.onmousedown=function(){return false}
	}
	return{slide:slide}
}();
//})(jQuery);