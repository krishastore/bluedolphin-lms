jQuery( document ).ready( function( $ ) {
    $( document ).on( 'change', '#bdlms_answer_type', function() {
        var type = $( this ).val();
        $( '.bdlms-answer-group' ).addClass( 'hidden' );
        $( '.bdlms-answer-group#' + type ).removeClass( 'hidden' );
    } );
} );