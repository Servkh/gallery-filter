/**
 * Gallery Filter — gallery-filter.js
 * Filtering + lightbox. No jQuery or external libraries.
 */
( function () {
	'use strict';

	function init() {
		document.querySelectorAll( '.gf-wrapper' ).forEach( initWrapper );
	}

	// ── Per-wrapper init ───────────────────────────────────────────────────────

	function initWrapper( wrapper ) {
		initFilter( wrapper );
		initLightbox( wrapper );
	}

	// ── Filtering ──────────────────────────────────────────────────────────────

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

	// ── Lightbox ───────────────────────────────────────────────────────────────

	function initLightbox( wrapper ) {
		var lb       = wrapper.querySelector( '.gf-lightbox' );
		if ( ! lb ) return;

		var lbImg     = lb.querySelector( '.gf-lb-img' );
		var lbTitle   = lb.querySelector( '.gf-lb-title' );
		var lbDesc    = lb.querySelector( '.gf-lb-desc' );
		var lbCounter = lb.querySelector( '.gf-lb-counter' );
		var btnClose  = lb.querySelector( '.gf-lb-close' );
		var btnPrev   = lb.querySelector( '.gf-lb-prev' );
		var btnNext   = lb.querySelector( '.gf-lb-next' );
		var backdrop  = lb.querySelector( '.gf-lb-backdrop' );

		var images    = [];
		var current   = 0;
		var touchStartX = 0;

		// ── Open ──

		wrapper.querySelectorAll( '.gf-card.gf-has-gallery' ).forEach( function ( card ) {
			card.addEventListener( 'click', function ( e ) {
				// If the click was on the external arrow link, let it navigate normally
				if ( e.target.closest( '.gf-arrow[href]' ) ) return;

				var raw = card.getAttribute( 'data-gallery' );
				if ( ! raw ) return;
				try { images = JSON.parse( raw ); } catch ( err ) { return; }
				if ( ! images.length ) return;

				current = 0;
				openLightbox(
					card.getAttribute( 'data-title' ) || '',
					card.getAttribute( 'data-description' ) || ''
				);
			} );

			// Keyboard: Enter / Space opens lightbox
			card.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					card.click();
				}
			} );
		} );

		function openLightbox( title, description ) {
			lbTitle.textContent = title;
			if ( lbDesc ) {
				lbDesc.textContent   = description || '';
				lbDesc.style.display = description ? '' : 'none';
			}
			showImage( current );
			lb.hidden = false;
			document.body.classList.add( 'gf-lb-open' );
			lb.focus();
		}

		function closeLightbox() {
			lb.hidden = true;
			document.body.classList.remove( 'gf-lb-open' );
			images  = [];
			current = 0;
		}

		// ── Show image ──

		function showImage( idx ) {
			var img = images[ idx ];
			lbImg.classList.remove( 'gf-lb-img--loaded' );
			// Attach the handler before setting src so cached images still fire it,
			// and reveal immediately if the image is already complete.
			lbImg.onload = function () {
				lbImg.classList.add( 'gf-lb-img--loaded' );
			};
			lbImg.src = img.url;
			lbImg.alt = img.alt || '';
			if ( lbImg.complete && lbImg.naturalWidth ) {
				lbImg.classList.add( 'gf-lb-img--loaded' );
			}
			lbCounter.textContent = ( idx + 1 ) + ' / ' + images.length;
			btnPrev.style.display = images.length > 1 ? '' : 'none';
			btnNext.style.display = images.length > 1 ? '' : 'none';
		}

		function prev() {
			current = ( current - 1 + images.length ) % images.length;
			showImage( current );
		}

		function next() {
			current = ( current + 1 ) % images.length;
			showImage( current );
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
			if ( e.key === 'Escape' )     closeLightbox();
			if ( e.key === 'ArrowLeft' )  prev();
			if ( e.key === 'ArrowRight' ) next();
		} );

		// Touch swipe
		lb.addEventListener( 'touchstart', function ( e ) {
			touchStartX = e.changedTouches[0].screenX;
		}, { passive: true } );

		lb.addEventListener( 'touchend', function ( e ) {
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

	// Re-init after Elementor renders widget in editor
	if ( window.elementorFrontend ) {
		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/gallery_filter.default',
			function ( $scope ) { initWrapper( $scope[0] ); }
		);
	}
} )();
