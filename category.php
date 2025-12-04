<?php
/**
 * Category archive template with AJAX search and filter
 */

get_header();

$category = get_queried_object();
$parent_cat = $category->parent ? get_category( $category->parent ) : null;

// Get child categories for filter (if parent category)
$child_cats = get_categories( array(
	'parent'     => $category->term_id,
	'hide_empty' => false,
) );
?>

<main class="site-content container">
	<header class="archive-header" style="margin-bottom: var(--spacing-xl);">
		<?php if ( $parent_cat ) : ?>
			<a href="<?php echo esc_url( get_category_link( $parent_cat->term_id ) ); ?>" style="color: var(--color-primary); text-decoration: none; font-size: 0.9rem;">
				← <?php echo esc_html( $parent_cat->name ); ?>
			</a>
		<?php endif; ?>
		<h1 class="archive-title" style="font-size: 2rem; color: var(--color-primary-dark); margin: var(--spacing-sm) 0;">
			<?php echo esc_html( $category->name ); ?>
		</h1>
		<?php if ( $category->description ) : ?>
			<p class="archive-description" style="color: var(--color-text-muted);">
				<?php echo esc_html( $category->description ); ?>
			</p>
		<?php endif; ?>
	</header>

	<!-- Posts container -->
	<div id="posts-container" class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg);">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'compact' );
			endwhile;
		else :
			?>
			<p class="no-posts"><?php _e( 'No posts found.', 'islamic-scholars' ); ?></p>
		<?php endif; ?>
	</div>

	<!-- Pagination -->
	<div id="pagination" style="margin-top: var(--spacing-xl);">
		<?php
		the_posts_pagination( array(
			'mid_size'  => 2,
			'prev_text' => __( '← Previous', 'islamic-scholars' ),
			'next_text' => __( 'Next →', 'islamic-scholars' ),
		) );
		?>
	</div>
</main>

<?php get_footer();
