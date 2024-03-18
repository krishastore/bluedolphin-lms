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
    // Store current screen data.
    window._inlineEditQuestion = {
        postId : 0,
        editRowId : 0,
    };

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
                    var editData = $(this).data('inline_edit');
                    var type = editData?.type;
                    q.hideAnswers();
                    window._inlineEditQuestion = {
                        postId: id,
                        editRowId: t.what+id
                    };
                    $(this).attr( 'aria-expanded', 'true' );

                    // Add the new edit row with an extra blank row underneath to maintain zebra striping.
                    var editRow = $('#inline-edit').clone(true);
                    var showAnswerHtml = $( '#show_answer' ).html();
                    editRow.html(showAnswerHtml);

                    $( 'td', editRow ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );

                    // Remove the ID from the copied row and let the `for` attribute reference the hidden ID.
                    $( 'td', editRow ).find('#quick-edit-legend').removeAttr('id');
                    $( 'td', editRow ).find('p[id^="quick-edit-"]').removeAttr('id');

                    $(t.what+id).removeClass('is-expanded').hide().after(editRow).after('<tr class="hidden"></tr>');
                    
                    // Set inline edit data.
                    $( 'input[name="post_title"]', editRow ).val( editData?.title );
                    $( 'select#bdlms_answer_type', editRow ).val( editData?.type );
                    $( '.marks-input input', editRow ).val( editData?.marks );

                    if ( editData[type] ) {
                        var optionList = $( '.bdlms-options-table__body .bdlms-options-table__list-wrap', $( '#' + type ) );
                        optionList.empty();
                        $.each( editData[type], function( n, i ) {
                            var optionListTpl = $( '#' + type + '_option' ).html();
                            optionListTpl = optionListTpl.replace( '{{VALUE}}', i.option );
                            optionListTpl = optionListTpl.replace( '{{checked}}', i.checked ? 'checked' : '' );
                            optionListTpl = optionListTpl.replace( '{{ANSWER_ID}}', n );
                            optionListTpl = optionListTpl.replace( '{{OPTION_NO}}', questionObject.alphabets[n] ?? '' );
                            $(optionListTpl).appendTo( optionList );
                        } );
                        $( '#' + type ).removeClass('hidden');
                    } else if( 'fill_blank' === type ) {
                        var mandatoryTpl = $('#fill_blank_mandatory').html();
                        mandatoryTpl = mandatoryTpl.replace( '{{VALUE}}', editData?.mandatory );
                        $( 'ul', '#' + type ).empty();
                        $( 'ul', '#' + type ).append( mandatoryTpl );
                        $.each( editData?.optional, function( i, optional ) {
                            var optionalTpl = $('#fill_blank_optional').html();
                            optionalTpl = optionalTpl.replace( '{{VALUE}}', optional );
                            $(optionalTpl).appendTo( $( 'ul', '#' + type ) );
                        } );
                        $( '#' + type ).removeClass('hidden');
                    }

                    // Trigger select type dropdown.
                    $( '#bdlms_answer_type' ).change();

                    // Show edit row.
                    $(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
                    q.initSortable(q);
                } );

                // Hide answer box.
                $( document ).on( 'click', '.bdlms-cancel-answer', this.hideAnswers );
            }
        },

        /**
         * Initializes the inline editor.
         */
        init : function() {
            var _this = this;
            _this.inlineEditQuestion();
            _this.initSortable(_this);

            // Show / Hide answers.
            $( document ).on( 'change', '#bdlms_answer_type', function() {
                var type = $( this ).val();
                if ( 'true_or_false' === type ) {
                    $( '.bdlms-add-option, .bdlms-show-ans-action .bdlms-add-answer' ).addClass( 'hidden' );
                } else {
                    $( '.bdlms-add-option, .bdlms-show-ans-action .bdlms-add-answer' ).removeClass( 'hidden' );
                }
                $( '.bdlms-answer-group, .inline-edit-col-left .bdlms-options-table, .inline-edit-col-left .bdlms-add-accepted-answers' ).addClass( 'hidden' );
                $( '#' + type ).removeClass( 'hidden' );
            } );
            $( '#bdlms_answer_type' ).change();

            // Inline quick edit.
            $( document ).on( 'click', '.button-link.editinline', function() {
                $( '.inline-edit-private' ).parents( 'div.inline-edit-group' ).remove();
                var rightCustomBox = jQuery( '.inline-edit-col-right:not(.inline-edit-levels):visible' );
                var selectedStatus = jQuery( 'select', rightCustomBox ).val();
                jQuery( ' > *', rightCustomBox ).appendTo( '.inline-edit-col-left:visible' );
                jQuery( '.inline-edit-col-left:visible select[name="_status"]' ).val( selectedStatus );
            } );
            // Remove answer.
            $( document ).on( 'click', '.bdlms-remove-answer', this.removeAnswer );
            // Add new answer.
            $( document ).on( 'click', '.bdlms-add-answer', this.addNewAnswer );
            // Save inline edit.
            $( document ).on( 'click', '.bdlms-save-answer', this.saveInlineEdit );
        },

        /**
         * Init sortable.
         */
        initSortable: function(obj) {
            var _this = obj;
            $( '.bdlms-sortable-answers', document ).sortable( {
                appendTo: 'parent',
                axis: 'y',
                containment: "parent",
                items: 'ul.bdlms-options-table__list',
                placeholder: "sortable-placeholder",
                forcePlaceholderSize: true,
                stop: function () {
                    _this.reorderAnswer();
                }
            } ).disableSelection();
        },

        /**
         * Hide answers.
         */
        hideAnswers : function () {
            // Hide previous opned inline editor.
            $( '.inline-editor' ).prev().prev('tr').show();
            $( '.inline-editor' ).prev('tr.hidden').remove();
            $( '.inline-editor' ).remove();
        },

        /**
         * Remove answer.
         */
        removeAnswer : function ( e ) {
            e.preventDefault();
            $( this ).parents( 'ul.bdlms-options-table__list' ).remove();
            questionBank.reorderAnswer();
        },

        /**
         * Reorder answer.
         */
        reorderAnswer : function () {
            $( '.bdlms-sortable-answers .bdlms-options-table__list:visible' ).each( function( index, item ) {
                var AnsId = questionObject.alphabets[index];
                $(item).find( '.bdlms-options-no' ).text( AnsId + '.' );
                $(item).find( '.bdlms-option-check-td input' ).val( index );
            } );
        },
        
        /**
         * Add new answer.
         */
        addNewAnswer : function() {
            var lastItem = $( 'ul.bdlms-options-table__list:visible:last, .bdlms-add-accepted-answers li:visible:last' );
            var newItem = lastItem.clone();
            newItem.find( 'input' ).val('').removeAttr('value');
            newItem.find('input:checkbox, input:radio').prop( 'checked', false ).removeAttr('checked');
            $( newItem ).insertAfter( lastItem );
            questionBank.reorderAnswer();
        },

        /**
         * Save inline edit answers.
         */
        saveInlineEdit : function () {
            var _this = $(this);
            var params, fields, page = $('.post_status_page').val() || '';
            var id = window._inlineEditQuestion.postId;

        $( 'table.widefat .spinner' ).addClass( 'is-active' );
            _this.attr('disabled', true);

            params = {
                action: 'inline-save',
                post_type: typenow,
                post_ID: id,
                edit_date: '',
                post_status: page
            };

            fields = $('#edit-'+id).find(':input').serialize();
            params = fields + '&' + $.param(params);

            // Make Ajax request.
            $.post( ajaxurl, params,
                function(r) {
                    var $errorNotice = $( '#edit-' + id + ' .inline-edit-save .notice-error' ),
                        $error = $errorNotice.find( '.error' );

                    $( 'table.widefat .spinner' ).removeClass( 'is-active' );

                    if (r) {
                        if ( -1 !== r.indexOf( '<tr' ) ) {
                            $(window._inlineEditQuestion.editRowId).siblings('tr.hidden').addBack().remove();
                            $('#edit-'+id).before(r).remove();
                            $( window._inlineEditQuestion.editRowId ).hide().fadeIn( 400, function() {
                                // Move focus back to the Quick Edit button. $( this ) is the row being animated.
                                $( this ).find( '[data-inline_edit]' )
                                .attr( 'aria-expanded', 'false' )
                                .trigger( 'focus' );
                                wp.a11y.speak( wp.i18n.__( 'Changes saved.' ) );
                            });
                        } else {
                            r = r.replace( /<.[^<>]*?>/g, '' );
                            $errorNotice.removeClass( 'hidden' );
                            $error.html( r );
                            wp.a11y.speak( $error.text() );
                        }
                    } else {
                        $errorNotice.removeClass( 'hidden' );
                        $error.text( wp.i18n.__( 'Error while saving the changes.' ) );
                        wp.a11y.speak( wp.i18n.__( 'Error while saving the changes.' ) );
                    }
                },
            'html');

        // Prevent submitting the form when pressing Enter on a focused field.
        return false;
        }
        
    };

    $( function() { questionBank.init(); } );
})( jQuery, window.wp );