/**
 * This file contains the functions needed for the inline edit and show answers.
 *
 * @since 1.0.0
 * @output assets/js/questions.js
 */

window.wp = window.wp || {};

/**
 * Manages the quick edit and bulk edit windows for editing posts or pages.
 *
 * @namespace questionBank
 *
 * @since 1.0.0
 *
 * @type {Object}
 *
 * @property {string} type The type of inline editor.
 * @property {string} what The prefix before the post ID.
 *
 */
( function( $, wp ) {

    window.questionBank = {

        inlineEditQuestion : function() {
             // we create a copy of the WP inline edit post function
            if ( 'undefined' !== typeof inlineEditPost ) {
                var $wp_inline_edit = inlineEditPost.edit;

                // and then we overwrite the function with our own code
                inlineEditPost.edit = function( id ) {

                    // "call" the original WP edit function
                    // we don't want to leave WordPress hanging

                    $wp_inline_edit.apply( this, arguments );

                    // now we take care of our business

                    // get the post ID
                    var $post_id = 0;
                    if ( typeof( id ) == 'object' ) {
                        $post_id = parseInt( this.getId( id ) );
                    }

                    if ( $post_id > 0 ) {
                        // define the edit row
                        var $edit_row = $( '#edit-' + $post_id );
                        var $post_row = $( '#post-' + $post_id );

                        // get the data
                        var $levels = $( '.column-levels', $post_row ).text();
                        // populate the data
                        $( '.inline-edit-levels select', $edit_row ).val( $levels?.toLowerCase() );
                    }
                };
                $( document ).on( 'click', '.show_answer a', function() {
                    var t = inlineEditPost, q = questionBank;
                    var $this = $( this ).parents( 'tr' );
                    var id = t.getId( $this );

                    q.hideAnswers();

                    // Add the new edit row with an extra blank row underneath to maintain zebra striping.
                    var editRow = $('#inline-edit').clone(true);
                    var showAnswerHtml = $( '#show_answer' ).html();
                    editRow.html(showAnswerHtml);

                    $( 'td', editRow ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );

                    // Remove the ID from the copied row and let the `for` attribute reference the hidden ID.
                    $( 'td', editRow ).find('#quick-edit-legend').removeAttr('id');
                    $( 'td', editRow ).find('p[id^="quick-edit-"]').removeAttr('id');

                    $(t.what+id).removeClass('is-expanded').hide().after(editRow).after('<tr class="hidden"></tr>');
                    
                    $(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
                } );
            }
        },

        /**
         * Initializes the inline editor.
         */
        init : function() {
            this.inlineEditQuestion();

            // Show / Hide answers.
            $( document ).on( 'change', '#bdlms_answer_type', function() {
                var type = $( this ).val();
                $( '.bdlms-answer-group' ).addClass( 'hidden' );
                $( '.bdlms-answer-group#' + type ).removeClass( 'hidden' );
            } );

            // Inline quick edit.
            $( document ).on( 'click', '.button-link.editinline', function() {
                $( '.inline-edit-private' ).parents( 'div.inline-edit-group' ).remove();
                var rightCustomBox = jQuery( '.inline-edit-col-right:not(.inline-edit-levels):visible' );
                var selectedStatus = jQuery( 'select', rightCustomBox ).val();
                jQuery( ' > *', rightCustomBox ).appendTo( '.inline-edit-col-left:visible' );
                jQuery( '.inline-edit-col-left:visible select[name="_status"]' ).val( selectedStatus );
            } );
        },

        /**
         * Hide answers.
         */
        hideAnswers : function () {
            // Hide previous opned inline editor.
            $( '.inline-editor' ).prev().prev('tr').show();
            $( '.inline-editor' ).prev('tr.hidden').remove();
            $( '.inline-editor' ).remove();
        }
        
    };

    $( function() { questionBank.init(); } );
})( jQuery, window.wp );