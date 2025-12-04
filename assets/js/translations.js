/**
 * Translation pairs interaction script
 */

document.addEventListener( 'DOMContentLoaded', function() {
	const pairs = document.querySelectorAll( '.translation-pair' );

	pairs.forEach( pair => {
		const original = pair.querySelector( '.translation-pair-original' );
		const translation = pair.querySelector( '.translation-pair-translation' );

		// Highlight on hover
		pair.addEventListener( 'mouseenter', function() {
			pair.style.backgroundColor = 'rgba(192, 132, 0, 0.2)';
			if ( original ) original.style.opacity = '1';
			if ( translation ) translation.style.opacity = '1';
		});

		pair.addEventListener( 'mouseleave', function() {
			pair.style.backgroundColor = 'rgba(192, 132, 0, 0.05)';
		});

		// Make original and translation individually hoverable
		if ( original ) {
			original.addEventListener( 'mouseenter', function() {
				original.style.backgroundColor = 'rgba(17, 94, 89, 0.15)';
			});

			original.addEventListener( 'mouseleave', function() {
				original.style.backgroundColor = 'rgba(17, 94, 89, 0.03)';
			});
		}

		if ( translation ) {
			translation.addEventListener( 'mouseenter', function() {
				translation.style.backgroundColor = 'rgba(192, 132, 0, 0.08)';
			});

			translation.addEventListener( 'mouseleave', function() {
				translation.style.backgroundColor = 'transparent';
			});
		}
	});
});
