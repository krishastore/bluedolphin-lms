import Plyr from 'plyr';

// Change the second argument to your options:
// 	
const player = new Plyr(".lesson-video", {
	// debug: true,
	tooltips: {
		controls: true,
	},
	captions: { active: true }
});
player.on('ended', function() {
	var nextPageLink = jQuery('.bdlms-next-btn').attr('href');
	window.location.href = nextPageLink;
	return false;
});
// Expose player so it can be used from the console
window.player = player;
