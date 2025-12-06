<?php
/**
 * Chronology page template
 * Template Name: Chronology of Scholars
 */

get_header(); ?>

<main class="site-content container">
	<div class="chronology-page">
		<header class="entry-header" style="margin-bottom: var(--spacing-2xl); text-align: center;">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<p class="entry-subtitle" style="color: var(--color-text-light); font-size: var(--fs-lg);">
				<?php _e( 'Explore the transmission of Islamic knowledge through the centuries and the connections between scholars.', 'islamic-scholars' ); ?>
			</p>
		</header>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<div class="chronology-intro entry-content" style="max-width: 75ch; margin-bottom: var(--spacing-2xl);">
					<?php the_content(); ?>
				</div>
			<?php
			endwhile;
		endif;
		?>

		<!-- Search and Filter -->
		<div class="chronology-filters card" style="margin-bottom: var(--spacing-2xl); padding: var(--spacing-lg);">
			<div style="display: flex; flex-wrap: wrap; gap: var(--spacing-lg); align-items: flex-end;">
				<div style="flex: 1; min-width: 200px;">
					<label for="scholar-search" style="font-weight: 600; display: block; margin-bottom: var(--spacing-xs);">
						<?php _e( 'Search scholars', 'islamic-scholars' ); ?>
					</label>
					<input type="text" id="scholar-search" placeholder="<?php esc_attr_e( 'Enter scholar name...', 'islamic-scholars' ); ?>" style="width: 100%; padding: 8px; border: 1px solid var(--color-border); border-radius: 4px;">
				</div>
				<div>
					<label for="century-filter" style="font-weight: 600; display: block; margin-bottom: var(--spacing-xs);">
						<?php _e( 'Century', 'islamic-scholars' ); ?>
					</label>
					<select id="century-filter" style="padding: 8px;">
						<option value=""><?php _e( 'All Centuries', 'islamic-scholars' ); ?></option>
						<?php
						$all_centuries = get_all_centuries_ordered();
						foreach ( $all_centuries as $cent ) :
							?>
							<option value="<?php echo esc_attr( $cent->slug ); ?>"><?php echo esc_html( $cent->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<p id="search-results-count" style="margin-top: var(--spacing-md); font-size: var(--fs-sm); color: var(--color-text-light); display: none;"></p>
		</div>

		<!-- Centuries -->
		<div class="chronology-container" style="display: grid; gap: var(--spacing-2xl);">
			<?php
			$centuries = get_all_centuries_ordered();

			if ( ! empty( $centuries ) ) :
				foreach ( $centuries as $century_term ) :
					$scholars = get_scholars_by_century( $century_term->term_id );

					if ( ! empty( $scholars ) ) :
						?>
						<section class="chronology-century" data-century="<?php echo esc_attr( $century_term->slug ); ?>" style="padding: var(--spacing-xl); background-color: var(--color-white); border-radius: 8px; border-left: 4px solid var(--color-primary-dark);">
							<h2 class="century-title" style="margin-bottom: var(--spacing-lg);">
								<a href="<?php echo esc_url( get_term_link( $century_term ) ); ?>" style="color: var(--color-primary-dark); text-decoration: none;">
									<?php echo esc_html( $century_term->name ); ?>
								</a>
							</h2>

							<div class="scholars-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: var(--spacing-lg);">
								<?php foreach ( $scholars as $scholar ) : ?>
									<div class="scholar-card card" data-scholar-id="<?php echo $scholar->ID; ?>" style="cursor: pointer; position: relative;">
										<?php
										$birth_year = intval( get_post_meta( $scholar->ID, 'birth_year', true ) );
										$death_year = intval( get_post_meta( $scholar->ID, 'death_year', true ) );
										$teachers = (array) get_post_meta( $scholar->ID, 'teachers', true );
										$students = get_scholar_students( $scholar->ID );
										?>
										<h3 style="font-size: var(--fs-lg); margin-bottom: var(--spacing-md);">
											<a href="<?php echo esc_url( get_permalink( $scholar->ID ) ); ?>">
												<?php echo esc_html( $scholar->post_title ); ?>
											</a>
										</h3>
										<?php if ( $birth_year ) : ?>
											<p style="color: var(--color-text-light); font-size: var(--fs-sm);">
												<?php 
												if ( $death_year ) {
													printf( __( '%d–%d AH', 'islamic-scholars' ), $birth_year, $death_year );
												} else {
													printf( __( '%d AH – present', 'islamic-scholars' ), $birth_year );
												}
												?>
											</p>
										<?php endif; ?>
										<p style="color: var(--color-text-light); font-size: var(--fs-sm); margin-top: var(--spacing-sm);">
											<?php echo wp_trim_words( $scholar->post_content, 15 ); ?>
										</p>

										<!-- Highlight connections - always show if there are teachers or students -->
										<?php if ( ! empty( $teachers ) || ! empty( $students ) ) : ?>
											<div class="scholar-connections" style="margin-top: var(--spacing-md); display: none; padding-top: var(--spacing-md); border-top: 1px solid var(--color-border);">
												<?php if ( ! empty( $teachers ) ) : ?>
													<div class="teachers-section" style="margin-bottom: var(--spacing-md);">
														<p style="font-size: var(--fs-sm); font-weight: 600; color: var(--color-accent-secondary); margin-bottom: var(--spacing-xs);">
															<?php _e( 'Teachers:', 'islamic-scholars' ); ?>
														</p>
														<ul style="list-style: none; font-size: var(--fs-sm);">
															<?php foreach ( $teachers as $teacher_id ) : ?>
																<li style="padding: 2px 0;">
																	→ <?php echo esc_html( get_the_title( $teacher_id ) ); ?>
																</li>
															<?php endforeach; ?>
														</ul>
													</div>
												<?php endif; ?>

												<?php if ( ! empty( $students ) ) : ?>
													<div class="students-section">
														<p style="font-size: var(--fs-sm); font-weight: 600; color: var(--color-accent); margin-bottom: var(--spacing-xs);">
															<?php _e( 'Students:', 'islamic-scholars' ); ?>
														</p>
														<ul style="list-style: none; font-size: var(--fs-sm);">
															<?php foreach ( $students as $student_id ) : ?>
																<li style="padding: 2px 0;">
																	← <?php echo esc_html( get_the_title( $student_id ) ); ?>
																</li>
															<?php endforeach; ?>
														</ul>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<p><?php _e( 'No scholars found.', 'islamic-scholars' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</main>

<script>
document.addEventListener( 'DOMContentLoaded', function() {
	const searchInput = document.getElementById('scholar-search');
	const centuryFilter = document.getElementById('century-filter');
	const resultsCount = document.getElementById('search-results-count');
	const centurySections = document.querySelectorAll('.chronology-century');
	const scholarCards = document.querySelectorAll('.scholar-card');

	function filterScholars() {
		const searchTerm = searchInput.value.toLowerCase().trim();
		const selectedCentury = centuryFilter.value;
		let visibleCount = 0;
		let totalCount = scholarCards.length;

		centurySections.forEach(section => {
			const centurySlug = section.dataset.century;
			const cards = section.querySelectorAll('.scholar-card');
			let visibleInSection = 0;

			// Check if century matches filter
			const centuryMatches = !selectedCentury || centurySlug === selectedCentury;

			cards.forEach(card => {
				const scholarNameEl = card.querySelector('h3 a');
				const scholarName = scholarNameEl ? scholarNameEl.textContent.toLowerCase().trim() : '';
				const searchMatches = !searchTerm || scholarName.includes(searchTerm);

				if (centuryMatches && searchMatches) {
					card.style.display = '';
					visibleInSection++;
					visibleCount++;
					// Highlight search term
					if (searchTerm) {
						card.style.boxShadow = '0 0 0 2px var(--color-accent)';
					} else {
						card.style.boxShadow = '';
					}
				} else {
					card.style.display = 'none';
					card.style.boxShadow = '';
				}
			});

			// Hide entire section if no visible cards or century doesn't match
			section.style.display = (centuryMatches && visibleInSection > 0) ? '' : 'none';
		});

		// Show results count
		if (searchTerm || selectedCentury) {
			resultsCount.style.display = 'block';
			resultsCount.textContent = '<?php _e( 'Found:', 'islamic-scholars' ); ?> ' + visibleCount + ' <?php _e( 'of', 'islamic-scholars' ); ?> ' + totalCount;
		} else {
			resultsCount.style.display = 'none';
		}
	}

	searchInput.addEventListener('input', filterScholars);
	centuryFilter.addEventListener('change', filterScholars);

	// Scholar card click to show connections
	scholarCards.forEach( card => {
		card.addEventListener( 'click', function(e) {
			// Don't trigger if clicking a link
			if (e.target.tagName === 'A') return;
			
			scholarCards.forEach( c => {
				if ( c !== card ) {
					c.classList.remove( 'active' );
					const conn = c.querySelector( '.scholar-connections' );
					if (conn) conn.style.display = 'none';
				}
			});

			card.classList.toggle( 'active' );
			const connections = card.querySelector( '.scholar-connections' );
			if (connections) {
				connections.style.display = connections.style.display === 'none' ? 'block' : 'none';
			}
		});
	});
});
</script>

<style>
.scholar-card.active {
	background-color: rgba(192, 132, 0, 0.1) !important;
	border-color: var(--color-accent) !important;
}
.century-title a:hover {
	text-decoration: underline;
}
</style>

<?php get_footer();
