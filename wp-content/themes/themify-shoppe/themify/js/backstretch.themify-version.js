/*v2.0.4*/
(function(a,d,p){

a.fn.tb_backstretch=function(c,b){
	/*Begin Themify*/
	if(typeof b!=='undefined'){
            if("undefined" === typeof b.mode){
                if (typeof themifyScript!="undefined" && "undefined" != typeof themifyScript.backgroundMode) {
                    b.mode = themifyScript.backgroundMode;
                }
                else if(typeof themifyVars!="undefined" && "undefined" != typeof themifyVars.backgroundMode){
                    b.mode = themifyVars.backgroundMode;
                }
            }
            if("undefined" === typeof b.position){
                    if (typeof themifyScript!="undefined" && "undefined" != typeof themifyScript.backgroundPosition && "" != typeof themifyScript.backgroundPosition) {
                        b.position = themifyScript.backgroundPosition;
                    }
                    else if(typeof themifyVars!="undefined" && "undefined" != typeof themifyVars.backgroundPosition && "" != typeof themifyVars.backgroundPosition){
                        b.position = themifyVars.backgroundPosition;
                    }
            }
            if('undefined'===typeof b.is_first){
                     b.is_first = true;
            }
	}
	/*End Themify*/

(c===p||0===c.length)&&a.error("No images were supplied for Backstretch");0===a(d).scrollTop()&&d.scrollTo(0,0);return this.each(function(){var d=a(this),g=d.data("tb_backstretch");if(g){if("string"==typeof c&&"function"==typeof g[c]){g[c](b);return}b=a.extend(g.options,b);g.destroy(!0)}g=new q(this,c,b);d.data("tb_backstretch",g)})};a.tb_backstretch=function(c,b){return a("body").tb_backstretch(c,b).data("tb_backstretch")};a.expr[":"].tb_backstretch=function(c){return a(c).data("tb_backstretch")!==p};a.fn.tb_backstretch.defaults={centeredX:!0,centeredY:!0,duration:5E3,fade:0,mode:'',position:''};var r={left:0,top:0,overflow:"hidden",margin:0,padding:0,height:"100%",width:"100%",zIndex:-999999},s={position:"absolute",display:"none",margin:0,padding:0,border:"none",width:"auto",height:"auto",maxHeight:"none",maxWidth:"none",zIndex:-999999},q=function(c,b,e){this.options=a.extend({},a.fn.tb_backstretch.defaults,e||{});this.images=a.isArray(b)?b:[b];a.each(this.images,function(){a("<img />")[0].src=this});this.isBody=c===document.body;this.$container=a(c);this.$root=this.isBody?l?a(d):a(document):this.$container;c=this.$container.children(".tb_backstretch").first();this.$wrap=c.length?c:a('<div class="tb_backstretch"></div>').css(r).appendTo(this.$container);this.isBody||(c=this.$container.css("position"),b=this.$container.css("zIndex"),this.$container.css({position:"static"===c?"relative":c,zIndex:"auto"===b?0:b,backgroundImage:"none"}),this.$wrap.css({zIndex:-999998}));this.$wrap.css({position:this.isBody&&l?"fixed":"absolute"});this.index=0;this.show(this.index,this.options.is_first);a(d).on("resize.tb_backstretch",a.proxy(this.resize,this)).on("orientationchange.tb_backstretch",a.proxy(function(){this.isBody&&0===d.pageYOffset&&(d.scrollTo(0,1),this.resize())},this))};q.prototype={resize:function(){try{var a={left:0,top:0},b=this.isBody?this.$root.width():this.$root.innerWidth(),e=b,g=this.isBody?d.innerHeight?d.innerHeight:this.$root.height():this.$root.innerHeight(),j=e/this.$img.data("ratio"),f;

	/*Begin Themify*/
	
	if ("best-fit" === this.options.mode) {
            var imageRatio = parseFloat(this.$img.data("ratio")),
                    containerRatio = e / g;

            if (containerRatio > imageRatio) {
                    this.$img.addClass("best-fit-vertical").removeClass("best-fit-horizontal");
            } else {
                    this.$img.addClass("best-fit-horizontal").removeClass("best-fit-vertical");
            }
	}
	/*End Themify*/

j>=g?(f=(j-g)/2,this.options.centeredY&&(a.top="-"+f+"px")):(j=g,e=j*this.$img.data("ratio"),f=(e-b)/2,this.options.centeredX&&(a.left="-"+f+"px"));this.$wrap.css({width:b,height:g}).find("img:not(.deleteable)").css({width:e,height:j}).css(a)}catch(h){}return this},show:function(c, is_first){if(!(Math.abs(c)>this.images.length-1)){var b=this,e=b.$wrap.find("img").addClass("deleteable"),d={relatedTarget:b.$container[0]};b.$container.trigger(a.Event("tb_backstretch.before",d),[b,c]);this.index=c;clearInterval(b.interval);b.$img=a("<img />").css(s).bind("load",function(f){var h=this.width||a(f.target).width();f=this.height||a(f.target).height();a(this).data("ratio",h/f);

	/*Begin Themify*/
	if(typeof b!=='undefined'){
            if ("best-fit" === b.options.mode) {
                    a(this).parent().addClass("best-fit-wrap");
                    var imageRatio = h / f,
                            containerRatio = b.$wrap.width() / b.$wrap.height();
                    if (containerRatio > imageRatio) {
                            a(this).addClass("best-fit best-fit-vertical");
                    } else {
                            a(this).addClass("best-fit best-fit-horizontal");
                    }
            }
            else if ("fullcover" === b.options.mode && b.options.position) {
                    a(this).addClass("fullcover-"+ b.options.position);

            }else if ("kenburns-effect" === b.options.mode) {
				a(this).parent().addClass("kenburns-effect");
			}

            if(is_first){
                    a(this).show();
            }
	}
	/*End Themify*/

a(this).fadeIn(b.options.speed||b.options.fade,function(){e.remove();b.paused||b.cycle();a(["after","show"]).each(function(){b.$container.trigger(a.Event("tb_backstretch."+this,d),[b,c])})});b.resize()}).appendTo(b.$wrap);b.$img.attr("src",b.images[c]);return b}},next:function(){return this.show(this.index<this.images.length-1?this.index+1:0,false)},prev:function(){return this.show(0===this.index?this.images.length-1:this.index-1,false)},pause:function(){this.paused=!0;return this},resume:function(){this.paused=!1;this.next();return this},cycle:function(){1<this.images.length&&(clearInterval(this.interval),this.interval=setInterval(a.proxy(function(){this.paused||this.next()},this),this.options.duration));return this},destroy:function(c){a(d).off("resize.tb_backstretch orientationchange.tb_backstretch");clearInterval(this.interval);c||this.$wrap.remove();this.$container.removeData("tb_backstretch")}};var l,f=navigator.userAgent,m=navigator.platform,e=f.match(/AppleWebKit\/([0-9]+)/),e=!!e&&e[1],h=f.match(/Fennec\/([0-9]+)/),h=!!h&&h[1],n=f.match(/Opera Mobi\/([0-9]+)/),t=!!n&&n[1],k=f.match(/MSIE ([0-9]+)/),k=!!k&&k[1];l=!((-1<m.indexOf("iPhone")||-1<m.indexOf("iPad")||-1<m.indexOf("iPod"))&&e&&534>e||d.operamini&&"[object OperaMini]"==={}.toString.call(d.operamini)||n&&7458>t||-1<f.indexOf("Android")&&e&&533>e||h&&6>h||"palmGetResource"in d&&e&&534>e||-1<f.indexOf("MeeGo")&&-1<f.indexOf("NokiaBrowser/8.5.0")||k&&6>=k)})(jQuery,window);
