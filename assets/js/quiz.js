/**
 * This file contains the functions needed for handle quiz module.
 *
 * @since 1.0.0
 * @output assets/js/questions.js
 */

window.wp = window.wp || {};

/**
 * Manages the quick edit and bulk edit windows for editing posts or pages.
 *
 * @namespace quizModule
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
    $( '#add_new_question' ).dialog( {
        title: 'Add New Questions',
        dialogClass: 'wp-dialog',
        autoOpen: false,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
            my: "center",
            at: "center",
            of: window
        },
        open: function( event, ui ) {
        },
        create: function() {
        },
    } );

    $( '#questions_bank' ).dialog( {
        title: 'Questions Bank',
        dialogClass: 'wp-dialog',
        autoOpen: false,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
            my: "center",
            at: "center",
            of: window
        },
        open: function( event, ui ) {
        },
        create: function() {
        },
    } );

    $(document).on( 'click', '.add-new-question', function() {
        $( '#add_new_question' ).dialog( 'open' );
    } );
    $(document).on( 'click', '.open-questions-bank', function() {
        $( '#questions_bank' ).dialog( 'open' );
    } );
})( jQuery, window.wp );