/**
 * Gallery Filter — admin.js
 * Media picker for the project Gallery Images field.
 */
( function ( $ ) {
	'use strict';

	$( function () {
		$( '.gf-gallery-field' ).each( function () {
			var $field   = $( this );
			var $ids     = $field.find( '.gf-gallery-ids' );
			var $preview = $field.find( '.gf-gallery-preview' );

			function syncIds() {
				var ids = $preview.find( 'li' ).map( function () {
					return $( this ).data( 'id' );
				} ).get();
				$ids.val( ids.join( ',' ) );
			}

			function addItem( id, url ) {
				$preview.append(
					'<li data-id="' + id + '">' +
						'<img src="' + url + '" alt="" />' +
						'<button type="button" class="gf-gallery-remove" aria-label="Remove image">&times;</button>' +
					'</li>'
				);
			}

			// ── Open the media frame ──
			$field.on( 'click', '.gf-gallery-add', function ( e ) {
				e.preventDefault();

				var frame = wp.media( {
					title:    'Select Gallery Images',
					multiple: true,
					library:  { type: 'image' },
					button:   { text: 'Use these images' }
				} );

				// Pre-select whatever is already chosen.
				frame.on( 'open', function () {
					var selection = frame.state().get( 'selection' );
					var current   = ( $ids.val() || '' ).split( ',' ).filter( Boolean );
					current.forEach( function ( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						selection.add( attachment );
					} );
				} );

				frame.on( 'select', function () {
					var selection = frame.state().get( 'selection' );
					$preview.empty();
					selection.each( function ( attachment ) {
						var att = attachment.toJSON();
						var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
						addItem( att.id, url );
					} );
					syncIds();
				} );

				frame.open();
			} );

			// ── Remove a single image ──
			$field.on( 'click', '.gf-gallery-remove', function ( e ) {
				e.preventDefault();
				$( this ).closest( 'li' ).remove();
				syncIds();
			} );

			// ── Clear all ──
			$field.on( 'click', '.gf-gallery-clear', function ( e ) {
				e.preventDefault();
				$preview.empty();
				syncIds();
			} );
		} );
	} );

} )( jQuery );
