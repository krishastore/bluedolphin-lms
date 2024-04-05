<?php
/**
 * Template: Quiz Questions Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>

<div class="bdlms-quiz-qus-wrap">
	<div class="bdlms-snackbar-notice"><p></p></div>
	<ul class="bdlms-quiz-qus-list bdlms-sortable-answers">
		<?php require_once BDLMS_TEMPLATEPATH . '/admin/quiz/question-list.php'; ?>
	</ul>
	<div class="bdlms-quiz-qus-footer">
		<a href="javascript:;" class="add-new-question button button-secondary"><?php esc_html_e( 'Add More Question', 'bluedolphin-lms' ); ?></a>
	</div>
</div>
<?php require_once BDLMS_TEMPLATEPATH . '/admin/quiz/modal-popup.php'; ?>
