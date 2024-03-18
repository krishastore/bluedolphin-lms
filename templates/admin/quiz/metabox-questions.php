<?php
/**
 * Template: Quiz Questions Metabox.
 *
 * @package BlueDolphin\Lms\Admin
 */

?>

<div class="bdlms-quiz-qus-wrap">
    <ul class="bdlms-quiz-qus-list bdlms-sortable-answers">
        <li>
            <div class="bdlms-quiz-qus-item">
                <div class="bdlms-quiz-qus-item__header">
                    <div class="bdlms-options-drag">
                        <svg class="icon" width="8" height="13">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
                        </svg>
                    </div>
                    <div class="bdlms-quiz-qus-name">
                        <span>What is React JS?</span>
                        <span class="bdlms-quiz-qus-point">1 Point</span>
                    </div>
                    <div class="bdlms-quiz-qus-toggle">
                        <svg class="icon" width="18" height="18">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__body">
                </div>
                <div class="bdlms-quiz-qus-item__footer">
                    <a href="javascript:;">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
                        </svg>
                        Edit
                    </a>
                    <a href="javascript:;">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#duplicate"></use>
                        </svg>
                        Duplicate
                    </a>
                    <a href="javascript:;" class="bdlms-delete-link">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
                        </svg>
                        Delete
                    </a>
                </div>
            </div>
        </li>
        <li>
            <div class="bdlms-quiz-qus-item">
                <div class="bdlms-quiz-qus-item__header">
                    <div class="bdlms-options-drag">
                        <svg class="icon" width="8" height="13">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
                        </svg>
                    </div>
                    <div class="bdlms-quiz-qus-name">
                        <span>What is UI/UX?</span>
                        <span class="bdlms-quiz-qus-point">1 Point</span>
                    </div>
                    <div class="bdlms-quiz-qus-toggle">
                        <svg class="icon" width="18" height="18">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__body">
                </div>
                <div class="bdlms-quiz-qus-item__footer">
                    <a href="javascript:;">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
                        </svg>
                        Edit
                    </a>
                    <a href="javascript:;">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#duplicate"></use>
                        </svg>
                        Duplicate
                    </a>
                    <a href="javascript:;" class="bdlms-delete-link">
                        <svg class="icon" width="12" height="12">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
                        </svg>
                        Delete
                    </a>
                </div>
            </div>
        </li>
    </ul>
    <div class="bdlms-quiz-qus-footer">
        <a href="javascript:;" class="add-new-question button">Add New Questions</a>
        <a href="javascript:;" class="open-questions-bank button">Open Questions Bank</a>
    </div>
</div>


<div id="add_new_question" class="hidden bdlms-add-qus-modal" style="max-width:463px">
    <div class="bdlms-btn-group">
        <button class="button button-primary">Create Your Own</button>
        <button class="button open-questions-bank">Add From Existing</button>
    </div>
    <p>
        <strong>Tips:</strong>
    </p>
    <p>
        Add from existing helps you to add question from your question bank which are stored.
    </p>
</div>


<div id="questions_bank" class="hidden" style="max-width:463px">
    <div class="bdlms-qus-bank-modal">
        <input type="text" placeholder="Type here to search for the question" class="bdlms-qus-bank-search">

        <div class="bdlms-qus-list">
            <ul>
                <li>
                    <div class="bdlms-setting-checkbox">
                        <input type="checkbox" id="bdlms-qus-1">
                        <label for="bdlms-qus-1">What is React JS? <strong>(Technical, Coding)</strong></label>
                    </div>
                </li>
                <li>
                    <div class="bdlms-setting-checkbox">
                        <input type="checkbox" id="bdlms-qus-2">
                        <label for="bdlms-qus-2">What is UI/UX? <strong>(Technical, Design)</strong></label>
                    </div>
                </li>
            </ul>
        </div>

        <div class="bdlms-qus-bank-add">
            <button class="button button-primary">Add</button>
            <span class="bdlms-qus-selected">1 Selected</span>
        </div>
    </div>
</div>