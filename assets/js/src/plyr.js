/* global StlmsObject */
import Plyr from 'plyr';

// Change the second argument to your options:
// https://github.com/sampotts/plyr/#options
const player = new Plyr(".lesson-video", {
	tooltips: {
		controls: true,
	},
	captions: { active: true },
	iconUrl: StlmsObject.iconUrl,
	blankVideo: StlmsObject.blankVideo
});
player.on('ended', function() {
	var nextPageLink = jQuery('.stlms-next-btn').attr('href');
	window.location.href = nextPageLink;
	return false;
});

var key;
jQuery(window).on("load", function () {
    const activeLesson = jQuery(".stlms-lesson-list li.active");
    if (activeLesson.length) {
        key = activeLesson.find(".stlms-lesson-class").attr("data-key");
    }
});

// Function to save the current time
const saveCurrentTime = () => {
	const currentTime = player.currentTime;
	localStorage.setItem(key, currentTime);
  };
  
  // Event listener for saving time before the page unloads
  window.addEventListener('beforeunload', saveCurrentTime);
  
  // Retrieve and set the current time when the page loads
  const storedTime = localStorage.getItem(key);
  if (storedTime) {
	player.once('play', () => {
		player.currentTime = Number.parseFloat(storedTime);
	});
}
// Expose player so it can be used from the console
window.player = player;
