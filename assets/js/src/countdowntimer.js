import countdowntimer from '@countdowntimer/dist/js/jQuery.countdownTimer.js';

jQuery(function ($) {
	
	var quizCountDownTimer = function(stopTimer) {
		var timerElement = $(document).find('.bdlms-quiz-countdown');
		if ( timerElement.length === 0 ) {
			return;
		}
		if ( stopTimer ) {
			timerElement.countdowntimer('pause', 'pause');
			return;
		}
		var startTimer = timerElement.data('timestamp');
		var minutes = startTimer / 60;
		var timerOptions = {
			minutes: minutes,
			seconds: 0,
			displayFormat: 'MS',
			regexpMatchFormat: '([0-9]{1,2}):([0-9]{1,2})',
			regexpReplaceWith: '$1m$2s',
			timeUp: function() {
				$('.bdlms-next-wizard').attr('disabled', true);
			}
		};
		timerElement.countdowntimer(timerOptions);
	};

	$('body').on('bdlms:show:step', function(e, data){
		e.preventDefault();
		if ( 1 === data.currentStepIndex ) {
			quizCountDownTimer(false);
		}
		if ( 'last' === data.currentStepPosition ) {
			quizCountDownTimer(true);
			$('.bdlms-next-wizard').hide();
		}
	});
});
