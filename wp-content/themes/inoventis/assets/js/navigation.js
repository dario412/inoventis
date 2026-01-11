/**
 * Navigation Script
 * Handles mobile menu toggle functionality
 */
(function() {
	const menuToggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.main-navigation');
	const navMenu = document.querySelector('.nav-menu');

	if (!menuToggle || !navigation || !navMenu) {
		return;
	}

	menuToggle.addEventListener('click', function() {
		const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
		
		menuToggle.setAttribute('aria-expanded', !isExpanded);
		navMenu.classList.toggle('active');
	});

	// Close menu when clicking outside
	document.addEventListener('click', function(event) {
		if (window.innerWidth <= 768) {
			const isClickInside = navigation.contains(event.target);
			const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
			
			if (!isClickInside && isExpanded) {
				menuToggle.setAttribute('aria-expanded', 'false');
				navMenu.classList.remove('active');
			}
		}
	});

	// Close menu on window resize if it's open on desktop
	window.addEventListener('resize', function() {
		if (window.innerWidth > 768) {
			menuToggle.setAttribute('aria-expanded', 'false');
			navMenu.classList.remove('active');
		}
	});
})();

