/*Rellax.js-1.0.0*/
(function (root, factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define([], factory);
	} else if (typeof module === 'object' && module.exports) {
		// Node. Does not work with strict CommonJS, but
		// only CommonJS-like environments that support module.exports,
		// like Node.
		module.exports = factory();
	} else {
		// Browser globals (root is window)
		root.Rellax = factory();
	}
}
(this, function () {
	var rellax_items = {};
	Rellax = function (elements, options) {
		"use strict";
		var self = Object.create(Rellax.prototype),
				posY = 0, // set it to -1 so the animate function gets called at least once
				screenY = 0,
				pause = false,
				checkPosition = true,
				didScroll;

		// check what requestAnimationFrame to use, and if
		// it's not supported, use the onscroll event
		var loop = window.requestAnimationFrame ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame ||
				window.msRequestAnimationFrame ||
				window.oRequestAnimationFrame ||
				function (callback) {
					setTimeout(callback, 1000 / 60);
				};

		// check which transform property to use
		var transformProp = window.transformProp || (function () {
			var testEl = document.createElement('div');
			if (testEl.style.transform === null) {
				var vendors = ['Webkit', 'Moz', 'ms'];
				for (var vendor in vendors) {
					if (testEl.style[ vendors[vendor] + 'Transform' ] !== undefined) {
						return vendors[vendor] + 'Transform';
					}
				}
			}
			return 'transform';
		})();
		// limit the given number in the range [min, max]
		var clamp = function (num, min, max) {
			return (num <= min) ? min : ((num >= max) ? max : num);
		};
		// Default Settings
		self.options = {
			speed: -2,
			center: false,
			round: true
		};
		// User defined options (might have more in the future)
		if (options) {
			Object.keys(options).forEach(function (key) {
				self.options[key] = options[key];
			});
		}

		// If some clown tries to crank speed, limit them to +-10
		self.options.speed = clamp(self.options.speed, -10, 10);
		var elem = Array.prototype.slice.call( elements ).filter( function( el ) { return el.offsetParent } );
		// Let's kick this script off
		// Build array for cached element values
		// Bind scroll and resize to animate method
		var init = function () {
			screenY = window.innerHeight;
			setPosition();
			// Get and cache initial position of all elements
			for (var i = 0, len = elem.length; i < len; ++i) {
				var index = elem[i].dataset.rellaxIndex !== undefined ? elem[i].dataset.rellaxIndex : Object.keys(rellax_items).length;
				elem[i].dataset.rellaxIndex = index;
				rellax_items[index] = {};
				rellax_items[index].el = elem[i];
				rellax_items[index].data = createBlock(elem[i]);
			}
			jQuery(window).off('tfsmartresize.tb_parallax').on('tfsmartresize.tb_parallax', animate)
			.on('scroll', function(){
				if ( checkPosition ) {
					checkPosition = false;
				}
				clearTimeout(didScroll);
				didScroll = setTimeout(function(){
					checkPosition = true;
				}, 2000);
			});

			// Fix Animation conflict
			for( var i in rellax_items ) {
				rellax_items[i].el.addEventListener( 'animationstart', function(e) {
					this.data.runningAnimation = true;
				}.bind( rellax_items[i] ), false );

				rellax_items[i].el.addEventListener( 'animationend', function() {
					this.data.runningAnimation = false;
				}.bind( rellax_items[i] ), false );
			}

			// Start the loop
			update();
			// The loop does nothing if the scrollPosition did not change
			// so call animate to make sure every element has their transforms
			animate();
		},
				// We want to cache the parallax blocks'
				// values: base, top, height, speed
				// el: is dom object, return: el cache values
				createBlock = function (el) {
					var dataPercentage = el.dataset.rellaxPercentage,
							dataSpeed = el.dataset.parallaxElementSpeed,
							reverse = el.dataset.parallaxElementReverse?true:false,
							// initializing at scrollY = 0 (top of browser)
							// ensures elements are positioned based on HTML layout.
							//
							// If the element has the percentage attribute, the posY needs to be
							// the current scroll position's value, so that the elements are still positioned based on HTML layout
							posY = dataPercentage || self.options.center ? (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) : 0,
							//blockTop = posY + el.getBoundingClientRect().top,
							blockTop = el.offsetTop + jQuery( el.offsetParent ).offset().top,
							blockHeight = el.offsetHeight || el.clientHeight || el.scrollHeight,
							// apparently parallax equation everyone uses
							percentage = dataPercentage ? dataPercentage : (posY - blockTop + screenY) / (blockHeight + screenY);
					if (self.options.center) {
						percentage = 0.5;
					}
					// Optional individual block speed as data attr, otherwise global speed
					// Check if has percentage attr, and limit speed to 5, else limit it to 10
					var speed = dataSpeed ? clamp(dataSpeed, -10, 10) : self.options.speed;
					if (dataPercentage || self.options.center) {
						speed = clamp(dataSpeed || self.options.speed, -5, 5);
					}
					var base = updatePosition(percentage, {data: { speed: speed }, el: el });
					return {
						base: base,
						top: blockTop,
						height: blockHeight,
						reverse:reverse,
						speed: speed,
						fade: el.dataset.parallaxFade ? true : false,
						style: el.style.cssText
					};
				},
				// set scroll position (posY)
				// side effect method is not ideal, but okay for now
				// returns true if the scroll changed, false if nothing happened
				setPosition = function () {

					if ( ! checkPosition ) return true;

					var oldY = posY;
					posY = window.pageYOffset !== undefined ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
					// scroll changed, return true
					return oldY !== posY;

				},
				// Ahh a pure function, gets new transform value
				// based on scrollPostion and speed
				// Allow for decimal pixel values
				updatePosition = function (percentage, block) {
					var currentPos = block.el.dataset.currentPos || 0,
						newPos = (0 - (percentage * ( block.data.speed / 10 ) )),
						value = (currentPos - ((currentPos - newPos) * 0.08));

					return self.options.round ? Math.round(value * 10) / 10 : value;
				},
				update = function () {
					if (setPosition() && pause === false) {
						animate();
					}
					// loop again
					loop(update);
				},
				// Transform3d on parallax element
				animate = function (e) {
					posY = window.pageYOffset;
					screenY = window.innerHeight;

					for (var i in rellax_items) {
						var block = rellax_items[i],
							position = getPosition( block );

						var translate = 'translate3d(0,' + position + 'px,0)';
						block.el.dataset.currentPos = position;

						if (block.data.fade) {
							var bounding = block.el.getBoundingClientRect(),
								offset = (bounding.bottom - screenY * 0.15);
							block.el.style['opacity'] = bounding.bottom >= 0 && offset <= block.data.height ? offset / block.data.height : (bounding.top > 0 ? 1 : '');
						}

						if( block.data.runningAnimation && block.el.style.position !== 'absolute' ) {
							block.el.style.position = 'relative';
							block.el.style.top = position + 'px';
							block.el.style[transformProp] = '';
						} else {
							if( block.el.style.top ) {
								block.el.style.position = block.el.style.top = '';
							}
							
							block.el.style[transformProp] = translate;
						}

					}
				},
				getPosition = function( el ) {
					var percentage = el.data.reverse ? ( ( el.data.top + el.data.height ) - posY ) - screenY : ( posY + screenY ) - ( el.data.top + el.data.height ),
						position = updatePosition(percentage, el);

					if ( el.data.reverse ) {
						position = Math.max( position, -( screenY - el.data.height ) );
						position = Math.min( position, ( screenY / 4 ) );
					}

					return position;
				};
		Rellax.destroy = function (index) {
			function destroy(item, i) {
				var el = item.el;
				el.style.cssText = item.data.style;
				el.removeAttribute('data-parallax-element-speed');
				el.removeAttribute('data-parallax-fade');
				el.removeAttribute('data-parallax-element-reverse');
				el.removeAttribute('data-current-pos');
				el.removeAttribute('data-rellax-index');

				delete el.dataset.rellaxIndex;
				delete el.dataset.currentPos;
				delete el.dataset.parallaxFade;
				delete el.dataset.parallaxElementSpeed;
				delete el.dataset.parallaxElementReverse;
				el.style['opacity'] = el.style[transformProp] = '';
				delete rellax_items[i];
			}
			if (index && rellax_items[index] !== undefined) {
				destroy(rellax_items[index], index);
			}
			else {
				for (var i in rellax_items) {
					destroy(rellax_items[i], i);
				}
			}
			if (Object.keys(rellax_items).length === 0) {
				pause = true;
				jQuery(window).off('tfsmartresize.tb_parallax')
			}
		};
		Rellax.disableCheckPosition = function() {
			checkPosition = false;
		};
		Rellax.enableCheckPosition = function() {
			checkPosition = true;
		};

		init();
		return self;
	};
	return Rellax;
}));