<?php
/**
 * Template: Quiz Questions Metabox.
 *
 * @package BlueDolphin\Lms\Admin
 */

?>

<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>

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
                    <div class="bdlms-quiz-qus-toggle" data-accordion>
                        <svg class="icon" width="18" height="18">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__body">
                    <div class="bdlms-answer-wrap">
                        <div class="bdlms-quiz-name">
                            <input type="text" placeholder="Enter Your Question Name">
                        </div>
                        <div class="bdlms-answer-type">
                            <label for="answers_field">
                                <?php esc_html_e( 'Select Answer Type', 'bluedolphin-lms' ); ?>
                            </label>
                            <select>
                                <option><?php esc_html_e( 'True Or False ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Multi Choice ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Single Choice ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Fill In Blanks ', 'bluedolphin-lms' ); ?></option>
                            </select>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options ', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <input type="text" class="bdlms-option-value-input"
                                                        value="test hello" readonly>
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="radio">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-action"></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="bdlms-options-no">
                                                        A.
                                                    </div>
                                                    <input type="text">
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="checkbox">
                                            </li>
                                            <li class="bdlms-option-action">
                                                <button type="button" class="bdlms-remove-answer">
                                                    <svg class="icon" width="12" height="12">
                                                        <use
                                                            xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash">
                                                        </use>
                                                    </svg>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="bdlms-add-option">
                                        <button type="button"
                                            class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-action"></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list bdlms-sortable-answers">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="bdlms-options-no">
                                                        A.
                                                    </div>
                                                    <input type="text">
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="radio">
                                            </li>
                                            <li class="bdlms-option-action">
                                                <button type="button" class="bdlms-remove-answer">
                                                    <svg class="icon" width="12" height="12">
                                                        <use
                                                            xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash">
                                                        </use>
                                                    </svg>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="bdlms-add-option">
                                        <button type="button"
                                            class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-add-accepted-answers">
                                <h3><?php esc_html_e( 'Add Accepted Answers', 'bluedolphin-lms' ); ?></h3>
                                <ul>
                                    <li>
                                        <label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
                                        <input type="text">
                                    </li>
                                    <li>
                                        <label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
                                        <input type="text">
                                    </li>
                                </ul>
                                <div class="bdlms-add-option">
                                    <button type="button"
                                        class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-add-option hidden">
                            <button type="button"
                                class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                        </div>
                    </div>
                    <div class="bdlms-qus-setting-wrap">
                        <div class="bdlms-answer-type">
                            <label for="answers_field">
                                <?php esc_html_e( 'Question Settings', 'bluedolphin-lms' ); ?>
                            </label>
                        </div>
                        <div class="bdlms-qus-setting-header">
                            <div>
                                <label for="points_field">
                                    <?php esc_html_e( 'Marks/Points: ', 'bluedolphin-lms' ); ?>
                                </label>
                                <input type="number" step="1" min="1">
                            </div>
                            <div>
                                <label for="levels_field">
                                    <?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?>
                                </label>
                                <select>
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                </select>
                            </div>
                            <div>
                                <label><input type="checkbox"> Hide Question? </label>
                            </div>
                        </div>
                        <div class="bdlms-qus-setting-body">
                            <h3><?php esc_html_e( 'Show Feedback/Hint ', 'bluedolphin-lms' ); ?></h3>

                            <div class="bdlms-hint-box">
                                <label for="hint_field">
                                    <?php esc_html_e( 'Correctly Answered Feedback: ', 'bluedolphin-lms' ); ?>
                                    <div class="bdlms-tooltip">
                                        <svg class="icon" width="12" height="12">
                                            <use
                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
                                            </use>
                                        </svg>
                                        <span class="bdlms-tooltiptext">
                                            <?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?>
                                        </span>
                                    </div>
                                </label>
                                <textarea></textarea>
                            </div>
                            <div class="bdlms-hint-box">
                                <label for="explanation_field" style="color: #B20000;">
                                    <?php esc_html_e( 'Incorrectly Answered Feedback: ', 'bluedolphin-lms' ); ?>
                                    <div class="bdlms-tooltip">
                                        <svg class="icon" width="12" height="12">
                                            <use
                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
                                            </use>
                                        </svg>
                                        <span class="bdlms-tooltiptext">
                                            <?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?>
                                        </span>
                                    </div>
                                </label>
                                <textarea></textarea>
                            </div>

                            <div class="bdlms-add-option">
                                <button type="button"
                                    class="button button-primary"><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
                                <button type="button"
                                    class="button"><?php esc_html_e( 'Cancel', 'bluedolphin-lms' ); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__footer">
                    <a href="javascript:;" data-accordion>
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
                    <div class="bdlms-quiz-qus-toggle" data-accordion>
                        <svg class="icon" width="18" height="18">
                            <use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__body">
                    <div class="bdlms-answer-wrap">
                        <div class="bdlms-quiz-name">
                            <input type="text" placeholder="Enter Your Question Name">
                        </div>
                        <div class="bdlms-answer-type">
                            <label for="answers_field">
                                <?php esc_html_e( 'Select Answer Type', 'bluedolphin-lms' ); ?>
                            </label>
                            <select>
                                <option><?php esc_html_e( 'True Or False ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Multi Choice ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Single Choice ', 'bluedolphin-lms' ); ?></option>
                                <option><?php esc_html_e( 'Fill In Blanks ', 'bluedolphin-lms' ); ?></option>
                            </select>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options ', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <input type="text" class="bdlms-option-value-input"
                                                        value="test hello" readonly>
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="radio">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-action"></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="bdlms-options-no">
                                                        A.
                                                    </div>
                                                    <input type="text">
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="checkbox">
                                            </li>
                                            <li class="bdlms-option-action">
                                                <button type="button" class="bdlms-remove-answer">
                                                    <svg class="icon" width="12" height="12">
                                                        <use
                                                            xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash">
                                                        </use>
                                                    </svg>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="bdlms-add-option">
                                        <button type="button"
                                            class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-options-table">
                                <div class="bdlms-options-table__header">
                                    <ul class="bdlms-options-table__list">
                                        <li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-check-td">
                                            <?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
                                        <li class="bdlms-option-action"></li>
                                    </ul>
                                </div>
                                <div class="bdlms-options-table__body bdlms-sortable-answers">
                                    <div class="bdlms-options-table__list-wrap">
                                        <ul class="bdlms-options-table__list bdlms-sortable-answers">
                                            <li>
                                                <div class="bdlms-options-value">
                                                    <div class="bdlms-options-drag">
                                                        <svg class="icon" width="8" height="13">
                                                            <use
                                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="bdlms-options-no">
                                                        A.
                                                    </div>
                                                    <input type="text">
                                                </div>
                                            </li>
                                            <li class="bdlms-option-check-td">
                                                <input type="radio">
                                            </li>
                                            <li class="bdlms-option-action">
                                                <button type="button" class="bdlms-remove-answer">
                                                    <svg class="icon" width="12" height="12">
                                                        <use
                                                            xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash">
                                                        </use>
                                                    </svg>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="bdlms-add-option">
                                        <button type="button"
                                            class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-answer-group">
                            <div class="bdlms-add-accepted-answers">
                                <h3><?php esc_html_e( 'Add Accepted Answers', 'bluedolphin-lms' ); ?></h3>
                                <ul>
                                    <li>
                                        <label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
                                        <input type="text">
                                    </li>
                                    <li>
                                        <label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
                                        <input type="text">
                                    </li>
                                </ul>
                                <div class="bdlms-add-option">
                                    <button type="button"
                                        class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                                </div>
                            </div>
                        </div>

                        <div class="bdlms-add-option hidden">
                            <button type="button"
                                class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
                        </div>
                    </div>
                    <div class="bdlms-qus-setting-wrap">
                        <div class="bdlms-answer-type">
                            <label for="answers_field">
                                <?php esc_html_e( 'Question Settings', 'bluedolphin-lms' ); ?>
                            </label>
                        </div>
                        <div class="bdlms-qus-setting-header">
                            <div>
                                <label for="points_field">
                                    <?php esc_html_e( 'Marks/Points: ', 'bluedolphin-lms' ); ?>
                                </label>
                                <input type="number" step="1" min="1">
                            </div>
                            <div>
                                <label for="levels_field">
                                    <?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?>
                                </label>
                                <select>
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                </select>
                            </div>
                            <div>
                                <label><input type="checkbox"> Hide Question? </label>
                            </div>
                        </div>
                        <div class="bdlms-qus-setting-body">
                            <h3><?php esc_html_e( 'Show Feedback/Hint ', 'bluedolphin-lms' ); ?></h3>

                            <div class="bdlms-hint-box">
                                <label for="hint_field">
                                    <?php esc_html_e( 'Correctly Answered Feedback: ', 'bluedolphin-lms' ); ?>
                                    <div class="bdlms-tooltip">
                                        <svg class="icon" width="12" height="12">
                                            <use
                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
                                            </use>
                                        </svg>
                                        <span class="bdlms-tooltiptext">
                                            <?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?>
                                        </span>
                                    </div>
                                </label>
                                <textarea></textarea>
                            </div>
                            <div class="bdlms-hint-box">
                                <label for="explanation_field" style="color: #B20000;">
                                    <?php esc_html_e( 'Incorrectly Answered Feedback: ', 'bluedolphin-lms' ); ?>
                                    <div class="bdlms-tooltip">
                                        <svg class="icon" width="12" height="12">
                                            <use
                                                xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
                                            </use>
                                        </svg>
                                        <span class="bdlms-tooltiptext">
                                            <?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?>
                                        </span>
                                    </div>
                                </label>
                                <textarea></textarea>
                            </div>

                            <div class="bdlms-add-option">
                                <button type="button"
                                    class="button button-primary"><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
                                <button type="button"
                                    class="button"><?php esc_html_e( 'Cancel', 'bluedolphin-lms' ); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bdlms-quiz-qus-item__footer">
                    <a href="javascript:;" data-accordion>
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