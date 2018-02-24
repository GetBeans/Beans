(function($){
	"use strict";

	var beansFieldImage = function( element, options ) {
		this.container = $( element );
		this.isMultiple = this.container.find( '.bs-images-wrap' ).data( 'multiple' );
		this.init();
	}

	beansFieldImage.prototype = {
		constructor: beansFieldImage,

		addImage: function( element ) {

			var $this = this,
				media_iframe,
				field = element.parents( '.bs-field-wrap' );

			// Set media iframe.
			media_iframe = wp.media( {
				multiple: $this.isMultiple
			} );

			// Run a callback when an image is selected.
			media_iframe.on( 'select', function() {
				var selection = media_iframe.state().get( 'selection' );

				if ( ! selection ) {
					return;
				}

				selection.each( function( attachment ) {

					// Clone the the template field.
					var newField = field.find( '.bs-image-wrap.bs-image-template' )
						.clone()
						.removeClass( 'bs-image-template' );
					var updatedfield = $this.updateImage( newField, attachment );

					// Finally append the field
					field.find( '.bs-images-wrap' ).append( updatedfield );

					// Hide the button if multiple images are not permissible.
					if ( ! $this.isMultiple ) {
						element.hide();
					}
				});
			});

			// Open the iframe.
			media_iframe.open();
		},

		editImage: function( element ) {
			var $this = this,
				media_iframe,
				field = element.parents( '.bs-field-wrap' );

			// Set media iframe.
			media_iframe = wp.media( {
				multiple: false
			} );

			// Select image on edit.
			media_iframe.on( 'open', function() {
				var selection = media_iframe.state().get( 'selection' ),
					id = element.parents( '.bs-image-wrap' ).find( 'input[type=hidden]' ).val(),
					attachment = wp.media.model.Attachment.get( id );

				attachment.fetch();
				selection.add( attachment ? [ attachment ] : [] );
			});

			// Run a callback when an image is selected.
			media_iframe.on( 'select', function() {
				var selection = media_iframe.state().get( 'selection' );

				if ( ! selection ) {
					return;
				}

				selection.each( function( attachment ) {
					$this.updateImage( element.parents( '.bs-image-wrap' ), attachment );
				});

			});

			// Open the iframe.
			media_iframe.open();
		},

		updateImage: function( field, attachment ) {

			if ( "thumbnail" in attachment.attributes.sizes ) {
				var attachment_url = attachment.attributes.sizes[ 'thumbnail' ].url;
			} else {
				var attachment_url = attachment.attributes.url;
			}

			// Set value and remove disabled attribute.
			field.find( 'input[type=hidden]' )
				.attr( 'value', attachment.id )
				.removeAttr( 'disabled' );

			// Set the image source.
			field.find( 'img' ).attr( 'src', attachment_url );

			return field;

		},

		deleteImage: function( element ) {

			element.closest( '.bs-image-wrap' ).remove();

			if ( ! this.isMultiple ) {
				this.container.find( '.bs-add-image' ).show();
			}

		},

		sortable: function() {

			if ( ! this.isMultiple ) {
				return;
			}

			var $this = this;

			this.container.find( '.bs-images-wrap' ).sortable( {
				handle: '.bs-toolbar .bs-button-menu',
				placeholder: "bs-image-placeholder",
				cursor: 'move',
				start: function( e, ui ){
					ui.placeholder.height( $this.container.find( '.bs-image-wrap' ).outerHeight() - 6 );
					ui.placeholder.width( $this.container.find(' .bs-image-wrap' ).outerWidth() - 6 );
				}
			} );

		},

		init: function() {
			this.sortable();
			this.listen();
		},

		listen: function() {
			var $this = this;

			this.container.on( 'click', '.bs-add-image', function() {
				$this.addImage( $(this) );
			});

			this.container.on( 'click', '.bs-button-trash', function( e ) {
				e.preventDefault();

				$this.deleteImage( $(this) );
			});

			this.container.on( 'click', '.bs-button-edit', function( e ) {
				e.preventDefault();

				$this.editImage( $(this) );
			});

		}

	};

	 $.fn[ 'beansFieldImage' ] = function ( options ) {

		return this.each( function() {

			if ( ! $.data( this, 'plugin_beansFieldImages' ) ) {
				$.data( this, 'plugin_beansFieldImage', new beansFieldImage( this, options ) );
			}
		});
	};

	// Fire the plugin.
	$( document ).ready( function( $ ) {
		 $( '.bs-field.bs-image' ).beansFieldImage();
	});

})(jQuery);
