<?php
/**
 * Archive template (taxonomies and post types)
 */

get_header();
?>

<main class="site-content container">
	<header class="entry-header" style="margin-bottom: var(--spacing-2xl);">
		<?php
		if ( is_tax() ) {
			single_term_title( '<h1 class="entry-title">', '</h1>' );
			the_archive_description( '<div class="taxonomy-description" style="max-width: 75ch; color: var(--color-text-light); font-size: var(--fs-lg); line-height: var(--lh-relaxed);">', '</div>' );
		} elseif ( is_post_type_archive() ) {
			echo '<h1 class="entry-title">' . esc_html( post_type_object( get_post_type() )->labels->name ) . '</h1>';
		} else {
			the_archive_title( '<h1 class="entry-title">', '</h1>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
		}
		?>
	</header>

	<!-- Posts -->
	<?php
	if ( have_posts() ) :
		echo '<div class="archive-posts" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">';

		while ( have_posts() ) :
			the_post();

			if ( get_post_type() === 'scholar' ) {
				get_template_part( 'template-parts/content', 'scholar-card' );
			} else {
				get_template_part( 'template-parts/content' );
			}

		endwhile;

		echo '</div>';

		// Pagination
		echo '<nav class="pagination">';
		echo paginate_links( array(
			'type' => 'list',
		) );
		echo '</nav>';

	else :
		echo '<p>' . __( 'No posts found.', 'islamic-scholars' ) . '</p>';
	endif;
	?>
</main>

<?php get_footer();
