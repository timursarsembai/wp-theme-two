<?php
/**
 * Main index template
 */

get_header(); ?>

<main class="site-content container">
	<div class="site-content-inner">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

			if ( get_post_type() === 'scholar' ) {
				get_template_part( 'template-parts/content', 'scholar-card' );
			} else {
				get_template_part( 'template-parts/content' );
			}			endwhile;

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
	</div>
</main>

<?php get_footer();
