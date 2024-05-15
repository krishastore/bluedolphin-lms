import Plyr from 'plyr';

// Change the second argument to your options:
// https://github.com/sampotts/plyr/#options
const player = new Plyr(".lesson-video", {
	// debug: true,
	tooltips: {
		controls: true,
	},
	captions: { active: true },
});

// Expose player so it can be used from the console
window.player = player;
