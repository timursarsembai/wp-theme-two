/**
 * Chronology page interactivity
 */

document.addEventListener( 'DOMContentLoaded', function() {
	const scholarCards = document.querySelectorAll( '.scholar-card' );

	scholarCards.forEach( card => {
		card.addEventListener( 'click', function( e ) {
			// Don't toggle on link click
			if ( e.target.tagName === 'A' ) {
				return;
			}

			// Close all other cards
			scholarCards.forEach( c => {
				if ( c !== card ) {
					c.classList.remove( 'active' );
					const connections = c.querySelector( '.scholar-connections' );
					if ( connections ) {
						connections.style.display = 'none';
					}
				}
			});

			// Toggle current card
			card.classList.toggle( 'active' );
			const connections = card.querySelector( '.scholar-connections' );
			if ( connections ) {
				connections.style.display = connections.style.display === 'none' ? 'block' : 'none';
			}
		});

		// Also allow keyboard navigation
		card.addEventListener( 'keydown', function( e ) {
			if ( e.key === 'Enter' || e.key === ' ' ) {
				e.preventDefault();
				card.click();
			}
		});
	});
});
