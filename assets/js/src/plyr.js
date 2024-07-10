import Plyr from 'plyr';

// Change the second argument to your options:
// https://github.com/sampotts/plyr/#options
const player = new Plyr(".lesson-video", {
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

var key = btoa(window.location.pathname).substring(70);

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
		player.currentTime = parseFloat(storedTime);
	});
}
// Expose player so it can be used from the console
window.player = player;
