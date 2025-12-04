<?php
/**
 * Default single post template
 */

get_header(); ?>

<main class="site-content container">
	<article class="post">
		<?php
		while ( have_posts() ) :
			the_post();

			islamic_scholars_breadcrumb();
			?>

			<header class="entry-header" style="margin-bottom: var(--spacing-2xl);">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php the_post_metadata(); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div style="margin-bottom: var(--spacing-2xl);">
					<?php the_post_thumbnail( 'large', array( 
						'style' => 'width: 100%; height: auto; border-radius: 8px;',
						'fetchpriority' => 'high',
						'decoding' => 'async',
					) ); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<!-- Translation pairs (if this is a translation post) -->
			<?php
			$pairs = get_translation_pairs( get_the_ID() );
			if ( ! empty( $pairs ) ) :
				?>
				<div style="margin: var(--spacing-2xl) 0;">
					<?php the_translation_pairs( get_the_ID() ); ?>
				</div>
			<?php endif; ?>

			<!-- Translation metadata -->
			<?php
			$scholar_id = get_post_meta( get_the_ID(), 'scholar_id', true );
			$source = get_post_meta( get_the_ID(), 'source', true );
			
			if ( $scholar_id || $source ) :
				?>
				<div class="card" style="margin: var(--spacing-2xl) 0; padding: var(--spacing-lg); background-color: rgba(192, 132, 0, 0.05); border-left: 4px solid var(--color-accent);">
					<h4><?php _e( 'Translation Info', 'islamic-scholars' ); ?></h4>
					<?php if ( $scholar_id ) : ?>
						<p><strong><?php _e( 'Scholar:', 'islamic-scholars' ); ?></strong> <a href="<?php echo esc_url( get_permalink( $scholar_id ) ); ?>"><?php echo esc_html( get_the_title( $scholar_id ) ); ?></a></p>
					<?php endif; ?>
					<?php if ( $source ) : ?>
						<p><strong><?php _e( 'Source:', 'islamic-scholars' ); ?></strong> <?php echo esc_html( $source ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			// Comments
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</article>
</main>

<?php get_footer();
