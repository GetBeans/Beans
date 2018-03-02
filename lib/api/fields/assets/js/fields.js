!(function($) {

    "use strict";

    var beansFields = function( element, options ) {

        this.container = $( element );
        this.init();
        this.listen();

    }

    beansFields.prototype = {

        constructor: beansFields,

        checkboxLabelToggle: function ( selector ) {

            selector.parent().find( 'input[type="checkbox"]' ).click();

        },

        imageradio: function( selector ) {

            selector.closest( 'fieldset' ).find( 'label' ).removeClass( 'selected' );
            selector.closest( 'label.bs-has-image' ).addClass( 'selected' );

        },

        slider: function( selector ) {

            var value = parseInt( selector.find( 'input[type=text]' ).val() ),
                min = parseInt( selector.attr( 'slider_min' ) ),
                max = parseInt( selector.attr( 'slider_max' ) ),
                interval = parseInt( selector.attr( 'slider_interval' ) );

            selector.slider({
                range: 'min',
                value: value,
                min: min,
                max: max,
                step: interval,
                slide: function( event, ui ) {

                    // Update visible output.
                    $(this).parent().find( '.bs-slider-value' ).text( ui.value );

                    // Update hidden input.
                    $(this).find( 'input[type=text]' )
                        .val( ui.value )
                        .keyup();

                }
            });

            // Remove href attribute to keep status bar from showing.
            selector.find( '.ui-slider-handle' ).removeAttr( 'href' );

        },

        activation: function( selector ) {

            if ( selector.is( ':checked' ) ) {

                selector.parent().next()
                    .removeClass( 'deactivated' );

            } else {

                selector.parent().next()
                    .addClass( 'deactivated' );

            }

        },

        readmore: function( selector ) {

            selector.parents( '.bs-field-description' ).find( '.bs-extended-content' ).slideToggle( 400, function() {

                if ( $( this ).is( ':visible' ) ) {
                    selector.text( 'Less...' );
                } else {
                    selector.text( 'More...' );
                }

            });

        },

        postbox: function( selector ) {

            // Close postboxes that should be closed.
            $( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );

            postboxes.add_postbox_toggles( selector.data( 'page' ) );

        },

        init: function() {

            var that = this;

            // Fire ui slider.
            this.container.find( '.bs-slider-wrap' ).each( function() {

                that.slider( $( this ) );

            });

            // Add active imageradio.
            this.container.find( '.bs-field.bs-radio .bs-has-image input:checked:enabled').closest('label').addClass('selected' );

            // Fire activation toggle.
            this.container.find( '.bs-field.bs-activation input[type="checkbox"]' ).each( function() {

                that.activation( $( this ) );

            });

            // Fire the postboxes.
            if ( ( typeof postboxes != 'undefined' ) && this.container.hasClass( 'bs-options' ) ) {
                this.postbox( this.container );
            }

        },
        listen: function() {

            var that = this;

            // Make checkbox legend toggling checkbox input on click.
            this.container.on( 'click', '.bs-checkbox-label', function( e ) {

                that.checkboxLabelToggle( $( this ) );

            });

            // Fire imageradio on click.
            this.container.on( 'click', '.bs-field.bs-radio label', function( e ) {

                that.imageradio( $( this ) );

            });

            // Fire activation toggle on click.
            this.container.on( 'click', '.bs-field.bs-activation input[type="checkbox"]', function() {

                that.activation( $( this ) );

            });

            // Fire readmore on click.
            this.container.on( 'click', '.bs-read-more', function( e ) {

                e.preventDefault();

                that.readmore( $( this ) );

            });

            // Reset confirmation.
            this.container.on( 'click', '[name="beans_reset_options"]', function(e) {

                return confirm( 'Are you sure you would like to reset?' );

            });

        }

    };

    $.fn[ 'beansFields' ] = function ( options ) {

        return this.each( function() {

            if ( ! $.data( this, 'plugin_beansFields' ) ) {
                $.data( this, 'plugin_beansFields', new beansFields( this, options ) );
            }

        });
    };

    // Fire the plugin.
    $( document ).ready( function( $ ) {

        $( '#edittag, #post-body, .bs-options' ).beansFields();

        // Wait for the control to be loaded before initialising.
        if ( wp.customize !== undefined ) {

            wp.customize.bind( 'ready', function() {

                $( '#customize-controls' ).beansFields();

            } );

        }

    });

})( window.jQuery );
