<?php
/**
 * Template: Quiz Setting Metabox.
 *
 * @package BlueDolphin\Lms\Admin
 */

?>
<div class="bdlms-quiz-settings">
    <ul>
        <li>
            <div class="bdlms-setting-label">
                Duration
            </div>
            <div class="bdlms-setting-option">
                <input type="text" class="bdlms-setting-number-input">
                <select>
                    <option>option 1</option>
                    <option>option 2</option>
                    <option>option 3</option>
                </select>
            </div>
        </li>
        <li>
            <div class="bdlms-setting-label">
                Passing Marks
            </div>
            <div class="bdlms-setting-option">
                <input type="text" class="bdlms-setting-number-input" value="20">
            </div>
        </li>
        <li>
            <div class="bdlms-setting-label">
                Negative Marking
            </div>
            <div class="bdlms-setting-option">
                <div class="bdlms-setting-checkbox">
                    <input type="checkbox" id="bdlms-neg-mark">
                    <label for="bdlms-neg-mark">Each question that answer wrongly, the total point is deducted exactly
                        from
                        the question's point.</label>
                </div>
            </div>
        </li>
        <li>
            <div class="bdlms-setting-label">
                Review
            </div>
            <div class="bdlms-setting-option">
                <div class="bdlms-setting-checkbox">
                    <input type="checkbox" id="bdlms-review">
                    <label for="bdlms-review">Allow students to review this quiz after they finish the quiz.</label>
                </div>
            </div>
        </li>
        <li>
            <div class="bdlms-setting-label">
                Show Correct Answer
            </div>
            <div class="bdlms-setting-option">
                <div class="bdlms-setting-checkbox">
                    <input type="checkbox" id="bdlms-show-ans">
                    <label for="bdlms-show-ans">Allow students to view the correct answer to the question in reviewing
                        this quiz.</label>
                </div>
            </div>
        </li>
    </ul>
</div>