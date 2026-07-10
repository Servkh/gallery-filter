/**
 * Gallery Filter — gallery-filter.js
 * Filtering + lightbox + before/after comparison slider. No jQuery or libraries.
 */
( function () {
	'use strict';

	// ── Comparison slider (shared) ──────────────────────────────────────────────
	// One set of document listeners drives whichever slider is being dragged.

	var activeSlider = null;

	document.addEventListener( 'mousemove', function ( e ) {
		if ( activeSlider ) activeSlider.move( e.clientX, e );
	} );
	document.addEventListener( 'mouseup', function () { activeSlider = null; } );
	document.addEventListener( 'touchmove', function ( e ) {
		if ( ! activeSlider ) return;
		var t = e.touches[0];
		if ( t ) activeSlider.move( t.clientX, e );
	}, { passive: false } );
	document.addEventListener( 'touchend', function () { activeSlider = null; } );

	function makeSlider( el, handle ) {
		var slider = { el: el, moved: false };

		function setPos( clientX ) {
			var rect = el.getBoundingClientRect();
			if ( ! rect.width ) return;
			var pos = ( clientX - rect.left ) / rect.width * 100;
			pos = Math.max( 0, Math.min( 100, pos ) );
			el.style.setProperty( '--gf-ba-pos', pos + '%' );
			if ( handle ) handle.setAttribute( 'aria-valuenow', Math.round( pos ) );
		}

		slider.start = function ( clientX ) { slider.moved = false; setPos( clientX ); };
		slider.move  = function ( clientX, e ) {
			slider.moved = true;
			setPos( clientX );
			if ( e && e.type === 'touchmove' && e.cancelable ) e.preventDefault();
		};

		el.addEventListener( 'mousedown', function ( e ) {
			e.preventDefault();
			activeSlider = slider;
			slider.start( e.clientX );
		} );
		el.addEventListener( 'touchstart', function ( e ) {
			var t = e.touches[0];
			if ( ! t ) return;
			activeSlider = slider;
			slider.start( t.clientX );
		}, { passive: true } );

		if ( handle ) {
			handle.addEventListener( 'keydown', function ( e ) {
				var cur = parseFloat( el.style.getPropertyValue( '--gf-ba-pos' ) );
				if ( isNaN( cur ) ) cur = 50;
				if ( e.key === 'ArrowLeft' )       cur = Math.max( 0, cur - 4 );
				else if ( e.key === 'ArrowRight' ) cur = Math.min( 100, cur + 4 );
				else return;
				e.preventDefault();
				e.stopPropagation();
				el.style.setProperty( '--gf-ba-pos', cur + '%' );
				handle.setAttribute( 'aria-valuenow', Math.round( cur ) );
			} );
		}

		return slider;
	}

	// ── Boot ────────────────────────────────────────────────────────────────────

	function init() {
		document.querySelectorAll( '.gf-wrapper' ).forEach( initWrapper );
	}

	function initWrapper( wrapper ) {
		initFilter( wrapper );
		initCompareCards( wrapper );
		initLightbox( wrapper );
	}

	// ── Filtering ────────────────────────────────────────────────────────────────

	function initFilter( wrapper ) {
		var buttons = wrapper.querySelectorAll( '.gf-filter-btn' );
		var cards   = wrapper.querySelectorAll( '.gf-card' );

		if ( ! buttons.length ) return;

		buttons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var filter = this.getAttribute( 'data-filter' );

				buttons.forEach( function ( b ) {
					b.classList.remove( 'is-active' );
					b.setAttribute( 'aria-pressed', 'false' );
				} );
				this.classList.add( 'is-active' );
				this.setAttribute( 'aria-pressed', 'true' );

				cards.forEach( function ( card ) {
					var show = filter === '*';
					if ( ! show ) {
						var cats = ( card.getAttribute( 'data-categories' ) || '' )
							.split( ' ' ).map( function ( c ) { return c.trim(); } ).filter( Boolean );
						show = cats.indexOf( filter ) !== -1;
					}
					if ( show ) {
						card.classList.remove( 'gf-hidden' );
						card.classList.remove( 'gf-visible' );
						void card.offsetWidth; // force reflow for animation restart
						card.classList.add( 'gf-visible' );
					} else {
						card.classList.add( 'gf-hidden' );
						card.classList.remove( 'gf-visible' );
					}
				} );
			} );
		} );
	}

	// ── Before/After cards ───────────────────────────────────────────────────────

	function initCompareCards( wrapper ) {
		wrapper.querySelectorAll( '.gf-card--ba' ).forEach( function ( card ) {
			if ( ! card.querySelector( '.gf-ba' ) ) return;
			// The card is the slider surface; --gf-ba-pos lives on it so the
			// before-image clip, handle, and labels all inherit the position.
			card.__gfSlider = makeSlider( card, null );
		} );
	}

	// ── Lightbox ───────────────────────────────────────────────────────────────

	function initLightbox( wrapper ) {
		var lb = wrapper.querySelector( '.gf-lightbox' );
		if ( ! lb ) return;

		var lbImg      = lb.querySelector( '.gf-lb-img' );
		var lbTitle    = lb.querySelector( '.gf-lb-title' );
		var lbLocation = lb.querySelector( '.gf-lb-location' );
		var lbDesc     = lb.querySelector( '.gf-lb-desc' );
		var lbCounter  = lb.querySelector( '.gf-lb-counter' );
		var btnClose   = lb.querySelector( '.gf-lb-close' );
		var btnPrev    = lb.querySelector( '.gf-lb-prev' );
		var btnNext    = lb.querySelector( '.gf-lb-next' );
		var backdrop   = lb.querySelector( '.gf-lb-backdrop' );

		var lbBa       = lb.querySelector( '.gf-lb-ba' );
		var lbBaBefore = lb.querySelector( '.gf-lb-ba-before' );
		var lbBaAfter  = lb.querySelector( '.gf-lb-ba-after' );
		var lbBaHandle = lb.querySelector( '.gf-lb-ba-handle' );

		// A "slide" is either { compare:true, before, after } or { compare:false, url, alt }.
		var slides      = [];
		var current     = 0;
		var touchStartX = 0;
		var compareMode = false;

		if ( lbBa ) makeSlider( lbBa, lbBaHandle );

		// ── Open ──

		wrapper.querySelectorAll( '.gf-card.gf-has-gallery' ).forEach( function ( card ) {
			card.addEventListener( 'click', function ( e ) {
				// Let the external arrow link navigate normally
				if ( e.target.closest( '.gf-arrow[href]' ) ) return;
				// Ignore the click that ends a slider drag
				if ( card.__gfSlider && card.__gfSlider.moved ) {
					card.__gfSlider.moved = false;
					return;
				}
				openFromCard( card );
			} );

			// Keyboard: Enter / Space opens the lightbox
			card.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					card.click();
				}
			} );
		} );

		function openFromCard( card ) {
			slides = [];

			// Before/After comparison comes first (if a pair is set)
			var before = card.getAttribute( 'data-before' );
			var after  = card.getAttribute( 'data-after' );
			if ( lbBa && card.getAttribute( 'data-ba' ) && before && after ) {
				slides.push( { compare: true, before: before, after: after } );
			}

			// Then any gallery images
			var raw = card.getAttribute( 'data-gallery' );
			if ( raw ) {
				try {
					JSON.parse( raw ).forEach( function ( im ) {
						if ( im && im.url ) slides.push( { compare: false, url: im.url, alt: im.alt || '' } );
					} );
				} catch ( err ) {}
			}

			if ( ! slides.length ) return;

			current = 0;
			showSlide( 0 );
			revealLightbox(
				card.getAttribute( 'data-title' ) || '',
				card.getAttribute( 'data-description' ) || '',
				card.getAttribute( 'data-location' ) || ''
			);
		}

		function showSlide( idx ) {
			var s = slides[ idx ];
			if ( ! s ) return;

			if ( s.compare ) {
				setCompare( true );
				lbBaBefore.src = s.before;
				lbBaAfter.src  = s.after;
				lbBa.style.setProperty( '--gf-ba-pos', '50%' );
				if ( lbBaHandle ) lbBaHandle.setAttribute( 'aria-valuenow', '50' );
			} else {
				setCompare( false );
				lbImg.classList.remove( 'gf-lb-img--loaded' );
				// Attach the handler before setting src so cached images still fire it.
				lbImg.onload = function () { lbImg.classList.add( 'gf-lb-img--loaded' ); };
				lbImg.src = s.url;
				lbImg.alt = s.alt || '';
				if ( lbImg.complete && lbImg.naturalWidth ) {
					lbImg.classList.add( 'gf-lb-img--loaded' );
				}
			}

			var multi = slides.length > 1;
			btnPrev.style.display = multi ? '' : 'none';
			btnNext.style.display = multi ? '' : 'none';
			lbCounter.textContent = multi ? ( idx + 1 ) + ' / ' + slides.length : '';
		}

		function setCompare( on ) {
			compareMode = on;
			if ( on ) {
				lbImg.style.display = 'none';
				if ( lbBa ) lbBa.hidden = false;
			} else {
				lbImg.style.display = '';
				if ( lbBa ) lbBa.hidden = true;
			}
		}

		function revealLightbox( title, description, location ) {
			lbTitle.textContent = title;
			if ( lbLocation ) {
				lbLocation.textContent   = location || '';
				lbLocation.style.display = location ? '' : 'none';
			}
			if ( lbDesc ) {
				lbDesc.textContent   = description || '';
				lbDesc.style.display = description ? '' : 'none';
			}
			lb.hidden = false;
			document.body.classList.add( 'gf-lb-open' );
			lb.focus();
		}

		function closeLightbox() {
			lb.hidden = true;
			document.body.classList.remove( 'gf-lb-open' );
			slides  = [];
			current = 0;
		}

		function prev() {
			if ( slides.length < 2 ) return;
			current = ( current - 1 + slides.length ) % slides.length;
			showSlide( current );
		}

		function next() {
			if ( slides.length < 2 ) return;
			current = ( current + 1 ) % slides.length;
			showSlide( current );
		}

		// ── Controls ──

		btnClose.addEventListener( 'click', closeLightbox );
		backdrop.addEventListener( 'click', closeLightbox );
		btnPrev.addEventListener(  'click', prev );
		btnNext.addEventListener(  'click', next );

		// Keyboard navigation
		lb.setAttribute( 'tabindex', '-1' );
		document.addEventListener( 'keydown', function ( e ) {
			if ( lb.hidden ) return;
			if ( e.key === 'Escape' )     { closeLightbox(); return; }
			if ( e.key === 'ArrowLeft' )  prev();
			if ( e.key === 'ArrowRight' ) next();
		} );

		// Touch swipe navigates slides — but not while on a comparison slide,
		// where horizontal touch drives the slider handle instead.
		lb.addEventListener( 'touchstart', function ( e ) {
			if ( compareMode ) return;
			touchStartX = e.changedTouches[0].screenX;
		}, { passive: true } );

		lb.addEventListener( 'touchend', function ( e ) {
			if ( compareMode ) return;
			var diff = touchStartX - e.changedTouches[0].screenX;
			if ( Math.abs( diff ) > 40 ) {
				diff > 0 ? next() : prev();
			}
		}, { passive: true } );
	}

	// ── Boot ──────────────────────────────────────────────────────────────────

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Re-init after Elementor renders the widget in the editor
	if ( window.elementorFrontend ) {
		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/gallery_filter.default',
			function ( $scope ) { initWrapper( $scope[0] ); }
		);
	}
} )();
