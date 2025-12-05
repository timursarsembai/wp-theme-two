<?php
/**
 * Search results template with post type filtering
 */

get_header(); ?>

<main class="site-content container">
	<header class="entry-header" style="margin-bottom: var(--spacing-2xl);">
		<h1 class="entry-title">
			<?php printf( __( 'Search results for: %s', 'islamic-scholars' ), get_search_query() ); ?>
		</h1>
	</header>

	<!-- Search filters -->
	<div class="search-filters card" style="margin-bottom: var(--spacing-2xl); padding: var(--spacing-lg);">
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; flex-wrap: wrap; gap: var(--spacing-md); align-items: center;">
			<input type="hidden" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
			
			<div>
				<label style="font-weight: 600; display: block; margin-bottom: var(--spacing-xs);">
					<?php _e( 'Search in:', 'islamic-scholars' ); ?>
				</label>
				<div style="display: flex; flex-wrap: wrap; gap: var(--spacing-md);">
					<?php
				$post_types = array(
					'post' => __( 'Posts & Translations', 'islamic-scholars' ),
					'page' => __( 'Pages', 'islamic-scholars' ),
					'scholar' => __( 'Scholars', 'islamic-scholars' ),
				);					$selected_post_types = isset( $_GET['post_type'] ) ? (array) $_GET['post_type'] : array();
					if ( empty( $selected_post_types ) ) {
						$selected_post_types = array_keys( $post_types );
					}

					foreach ( $post_types as $post_type => $label ) :
						?>
						<label style="display: flex; align-items: center; gap: var(--spacing-xs); cursor: pointer;">
							<input 
								type="checkbox" 
								name="post_type[]" 
								value="<?php echo esc_attr( $post_type ); ?>"
								<?php checked( in_array( $post_type, $selected_post_types ), true ); ?>
							>
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<button type="submit" class="button" style="margin-top: auto;">
				<?php _e( 'Filter', 'islamic-scholars' ); ?>
			</button>
		</form>
	</div>

	<!-- Results -->
	<?php
	if ( have_posts() ) :
		echo '<div style="margin-bottom: var(--spacing-lg); color: var(--color-text-light); font-size: var(--fs-sm);">';
		printf(
			__( 'Found %d result(s)', 'islamic-scholars' ),
			$GLOBALS['wp_query']->found_posts
		);
		echo '</div>';

		echo '<div class="search-results" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">';

		while ( have_posts() ) :
			the_post();
			$post_type = get_post_type();
			?>
			<div class="card search-result">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" class="post-thumbnail">
						<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: auto; border-radius: 4px; display: block; margin-bottom: var(--spacing-md);' ) ); ?>
					</a>
				<?php endif; ?>

				<div>
					<p style="font-size: var(--fs-sm); color: var(--color-text-light); margin-bottom: var(--spacing-xs); text-transform: uppercase; font-weight: 600;">
						<?php echo esc_html( get_post_type_object( $post_type )->labels->singular_name ); ?>
					</p>

					<h3 style="margin-bottom: var(--spacing-md);">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<?php
					if ( $post_type === 'translation' ) {
						$scholar_id = get_post_meta( get_the_ID(), 'scholar_id', true );
						if ( $scholar_id ) {
							echo '<p style="color: var(--color-text-light); font-size: var(--fs-sm); margin-bottom: var(--spacing-md);">';
							printf( __( 'By %s', 'islamic-scholars' ), '<strong>' . esc_html( get_the_title( $scholar_id ) ) . '</strong>' );
							echo '</p>';
						}
					} elseif ( $post_type === 'scholar' ) {
						$birth_year = intval( get_post_meta( get_the_ID(), 'birth_year', true ) );
						$death_year = intval( get_post_meta( get_the_ID(), 'death_year', true ) );
						if ( $birth_year && $death_year ) {
							echo '<p style="color: var(--color-text-light); font-size: var(--fs-sm); margin-bottom: var(--spacing-md);">';
							printf( __( '%d–%d AH', 'islamic-scholars' ), $birth_year, $death_year );
							echo '</p>';
						}
					}
					?>

					<p class="card-meta" style="color: var(--color-text-light);">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
					</p>

					<a href="<?php the_permalink(); ?>" style="color: var(--color-primary-dark); font-weight: 600; font-size: var(--fs-sm); display: inline-block; margin-top: var(--spacing-md);">
						<?php _e( 'Read more →', 'islamic-scholars' ); ?>
					</a>
				</div>
			</div>
		<?php
		endwhile;

		echo '</div>';

		// Pagination
		echo '<nav class="pagination">';
		echo paginate_links( array(
			'type' => 'list',
		) );
		echo '</nav>';

	else :
		?>
		<div style="text-align: center; padding: var(--spacing-2xl) 0;">
			<p><?php _e( 'No results found. Please try a different search.', 'islamic-scholars' ); ?></p>
		</div>
	<?php
	endif;
	?>
</main>

<?php get_footer();
