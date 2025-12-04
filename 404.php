<?php
/**
 * 404 page template
 */

get_header(); ?>

<main class="site-content container">
	<div style="text-align: center; padding: var(--spacing-2xl) 0;">
		<h1 class="entry-title" style="font-size: 72px; margin-bottom: var(--spacing-md); color: var(--color-primary-dark);">
			404
		</h1>
		<h2 style="margin-bottom: var(--spacing-lg); color: var(--color-text-light);">
			<?php _e( 'Page not found', 'islamic-scholars' ); ?>
		</h2>
		<p style="font-size: var(--fs-lg); color: var(--color-text-light); margin-bottom: var(--spacing-2xl); max-width: 60ch; margin-left: auto; margin-right: auto;">
			<?php _e( 'The page you are looking for does not exist. It might have been moved or deleted. Please use the search or navigation to find what you are looking for.', 'islamic-scholars' ); ?>
		</p>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button" style="display: inline-block;">
			<?php _e( 'Go to Homepage', 'islamic-scholars' ); ?>
		</a>
	</div>
</main>

<?php get_footer();
