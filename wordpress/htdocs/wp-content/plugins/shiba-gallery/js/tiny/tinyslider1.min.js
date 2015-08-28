var TINY1={};

function shiba$(i){return document.getElementById(i)}
function shibashiba$shiba$(e,p){p=p||document; return p.getElementsByTagName(e)}

TINY1.slideshow=function(n){
	this.infoSpeed=this.imgSpeed=this.speed=10;
	this.thumbOpacity=this.navHover=70;
	this.navOpacity=25;
	this.scrollSpeed=5;
	this.letterbox='#000';
	this.responsive=false;
	this.crop=false;
	this.aspect = 0;
	this.n=n;
	this.c=0;
	this.a=[]
};

TINY1.slideshow.prototype={
	init:function(s,z,b,f,q){
		s=shiba$(s);
		var m=shibashiba$shiba$('li',s), i=0, w=0;
		var self = this;
		this.l=m.length;
		this.q=shiba$(q);
		this.f=shiba$(z);
		this.r=shiba$(this.info);
		this.o=parseInt(TINY1.style.val(z,'width'));
		if(this.thumbs){
			var u=shiba$(this.left), r=shiba$(this.right);
			u.onmouseover=new Function('TINY1.scroll.init("'+this.thumbs+'",-1,'+this.scrollSpeed+')');
			u.onmouseout=r.onmouseout=new Function('TINY1.scroll.cl("'+this.thumbs+'")');
			r.onmouseover=new Function('TINY1.scroll.init("'+this.thumbs+'",1,'+this.scrollSpeed+')');
			this.p=shiba$(this.thumbs)
		}

		for(i;i<this.l;i++){
			this.a[i]={};
			var h=m[i], a=this.a[i];
			// Shiba Gallery
			a.t=shibashiba$shiba$('a',h)[0];
//			a.t=shibashiba$shiba$('h3',h)[0].innerHTML;
			a.d=shibashiba$shiba$('p',h)[0].innerHTML;
			a.l=shibashiba$shiba$('a',h)[0]?shibashiba$shiba$('a',h)[0].href:'';
			a.p=shibashiba$shiba$('span',h)[0]; // contains the image span
//			a.p=shibashiba$shiba$('span',h)[0].innerHTML;
			if(this.thumbs){
				var g=shibashiba$shiba$('img',h)[0];
				g.style.width = g.getAttribute('width') + 'px';
				g.style.height = g.getAttribute('height') + 'px';
				this.p.appendChild(g);
				w+=parseInt(g.offsetWidth);
				if(i!=this.l-1){
					g.style.marginRight=this.spacing+'px';
					w+=this.spacing
				}
				this.p.style.width=w+'px'; 
				g.style.opacity=this.thumbOpacity/100;
				g.style.filter='alpha(opacity='+this.thumbOpacity+')';
				g.onmouseover=new Function('TINY1.alpha.set(this,100,5)');
				g.onmouseout=new Function('TINY1.alpha.set(this,'+this.thumbOpacity+',5)');
				g.onclick=new Function(this.n+'.pr('+i+',1)') // thumbnail click
			}
		}
		if(b&&f){
			b=shiba$(b);
			f=shiba$(f);
			b.style.opacity=f.style.opacity=this.navOpacity/100;
			b.style.filter=f.style.filter='alpha(opacity='+this.navOpacity+')';
			b.onmouseover=f.onmouseover=new Function('TINY1.alpha.set(this,'+this.navHover+',5)');
			b.onmouseout=f.onmouseout=new Function('TINY1.alpha.set(this,'+this.navOpacity+',5)');
			b.onclick=new Function(this.n+'.mv(-1,1)'); // back click
			f.onclick=new Function(this.n+'.mv(1,1)') // forward click
		}
		this.auto?this.is(0,0):this.is(0,1);
		if (this.responsive) {
			this.origWidth = this.f.offsetWidth;
			// Shiba Gallery - bind resize 
			shibaResizeFunctions.push(this.resize.bind(this));
		}
	},
	mv:function(d,c){
		var t=this.c+d;
		this.c=t=t<0?this.l-1:t>this.l-1?0:t;
		this.pr(t,c)
	},
	pr:function(t,c){
		clearTimeout(this.lt);
		if(c){
			clearTimeout(this.at)
		}
		this.c=t;
		this.is(t,c)
	},
	is:function(s,c){
		if(this.info){
			TINY1.height.set(this.r,1,this.infoSpeed/2,-1)
		}
		var i=new Image();
		i.style.opacity=0;
		i.style.filter='alpha(opacity=0)';
		this.i=i;
		i.onload=new Function(this.n+'.le('+s+','+c+')');
		i.src=this.a[s].p.innerHTML;
		i.style.width=this.a[s].p.style.width;
		i.style.height=this.a[s].p.style.height;
		i.style.padding = this.a[s].p.style.padding;
		
		if(this.thumbs){
			var a=shibashiba$shiba$('img',this.p), l=a.length, x=0;
			for(x;x<l;x++){
				a[x].style.borderColor=x!=s?'':this.active
			}
		}
	},
	resize:function() {
		// Only resize when parent container width has changed
		if (this.f.offsetWidth == this.origWidth)
			return;
		this.origWidth = this.f.offsetWidth;
		this.resizeImg();
	},
	
	resizeImg:function() {
		// Shiba Gallery - Make gallery mobile responsive by adjusting image widths and 
		// heights accordingly
		var m = this.i; // img

		if (this.aspect) {
			this.f.style.height = (this.aspect * this.f.offsetWidth)+'px';
			if (this.crop) {
				var w=parseInt(this.f.offsetWidth)-parseInt(m.offsetWidth),
					pw=Math.floor(w/2);
				if (w < 0)
					m.style.marginLeft=pw+'px';
				else	
					m.style.marginLeft='0px';
			}
			return;
		}

		shibaGallery.adjustWidth(m, this.f, this.crop);
	},
	le:function(s,c){
		this.f.appendChild(this.i);
		// Shiba Gallery
		if (this.responsive) {
			this.resizeImg();
		}
/*		var w=this.o-parseInt(this.i.offsetWidth);
		if(w>0){
			var l=Math.floor(w/2);
			this.i.style.borderLeft=l+'px solid transparent'; //+this.letterbox;
			this.i.style.borderRight=(w-l)+'px solid transparent'; //+this.letterbox
		}
*/		TINY1.alpha.set(this.i,100,this.imgSpeed);
		var n=new Function(this.n+'.nf('+s+')');
		this.lt=setTimeout(n,this.imgSpeed*100);
		if(!c){
			this.at=setTimeout(new Function(this.n+'.mv(1,0)'),this.speed*1000)
		}
		
		if (this.q!=null) {
		  if(this.a[s].l!=''){
			  // Shiba Gallery
			  var self = this;
			  this.q.onclick=function(e) {
				  self.a[self.c].t.click();
				  return false;
			  };
//			  this.q.onclick=new Function('window.location="'+this.a[s].l+'"');
			  this.q.onmouseover=new Function('this.className +="'+' '+this.link+'"');
			  this.q.onmouseout=new Function('this.className="ts_imglink"');
			  this.q.style.cursor='pointer'
		  }else{
			  this.q.onclick=this.q.onmouseover=null;
			  this.q.style.cursor='default'
		  }
		} else {
			// Shiba Gallery - not active
			this.i.onclick= new Function(this.n + ".mv(1, 1)");
		}
		var m=shibashiba$shiba$('img',this.f);
		if(m.length>2){
			this.f.removeChild(m[0])
		}
	},
	nf:function(s){
		if(this.info){
			s=this.a[s];
			// Shiba Gallery
			l=shibashiba$shiba$('h3',this.r)[0];
			if (l.lastChild) {
				l.removeChild(l.lastChild);
			}
			l.appendChild(s.t);
//			shibashiba$shiba$('h3',this.r)[0].innerHTML=s.t;
			shibashiba$shiba$('p',this.r)[0].innerHTML=s.d;
			this.r.style.height='auto';
			var h=parseInt(this.r.offsetHeight); 
			this.r.style.height=0;
			TINY1.height.set(this.r,h,this.infoSpeed,0)
		}
	}
};

TINY1.scroll=function(){
	return{
		init:function(e,d,s){
			e=typeof e=='object'?e:shiba$(e); var p=e.style.left||TINY1.style.val(e,'left'); e.style.left=p;
			var l=d==1?parseInt(e.offsetWidth)-parseInt(e.parentNode.offsetWidth):0; l=l<0?0:l;
			e.si=setInterval(function(){TINY1.scroll.mv(e,l,d,s)},20)
		},
		mv:function(e,l,d,s){			
			var c=parseInt(e.style.left); if(c==l){TINY1.scroll.cl(e)}else{var i=Math.abs(l+c); i=i<s?i:s; var n=c-i*d; e.style.left=n+'px'}
		},
		cl:function(e){e=typeof e=='object'?e:shiba$(e); clearInterval(e.si)}
	}
}();

TINY1.height=function(){
	return{
		set:function(e,h,s,d){
			e=typeof e=='object'?e:shiba$(e); var oh=e.offsetHeight, ho=e.style.height||TINY1.style.val(e,'height');
			ho=oh-parseInt(ho); var hd=oh-ho>h?-1:1; clearInterval(e.si); e.si=setInterval(function(){TINY1.height.tw(e,h,ho,hd,s)},20);
		},
		tw:function(e,h,ho,hd,s){
			var oh=e.offsetHeight-ho;
			if(oh==h){clearInterval(e.si)}else{if(oh!=h){e.style.height=oh+(Math.ceil(Math.abs(h-oh)/s)*hd)+'px'}}
		}
	}
}();

TINY1.alpha=function(){
	return{
		set:function(e,a,s){
			e=typeof e=='object'?e:shiba$(e); var o=e.style.opacity||TINY1.style.val(e,'opacity'),
			d=a>o*100?1:-1; e.style.opacity=o; clearInterval(e.ai); e.ai=setInterval(function(){TINY1.alpha.tw(e,a,d,s)},20)
		},
		tw:function(e,a,d,s){
			var o=Math.round(e.style.opacity*100);
			if(o==a){clearInterval(e.ai)}else{var n=o+Math.ceil(Math.abs(a-o)/s)*d; e.style.opacity=n/100; e.style.filter='alpha(opacity='+n+')'}
		}
	}
}();

TINY1.style=function(){return{val:function(e,p){e=typeof e=='object'?e:shiba$(e); return e.currentStyle?e.currentStyle[p]:document.defaultView.getComputedStyle(e,null).getPropertyValue(p)}}}();


