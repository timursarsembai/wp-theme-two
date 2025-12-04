<?php
/**
 * Page template
 */

get_header(); ?>

<main class="site-content container">
	<article class="page">
		<?php
		while ( have_posts() ) :
			the_post();

			islamic_scholars_breadcrumb();
			?>

			<header class="entry-header" style="margin-bottom: var(--spacing-2xl);">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div style="margin-bottom: var(--spacing-2xl);">
					<?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; border-radius: 8px;' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

		endwhile;
		?>
	</article>
</main>

<?php get_footer();
