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
			// Get scholars - support both new array and old single ID
			$scholar_ids = get_post_meta( get_the_ID(), 'scholar_ids', true );
			if ( ! is_array( $scholar_ids ) || empty( $scholar_ids ) ) {
				$old_scholar_id = get_post_meta( get_the_ID(), 'scholar_id', true );
				$scholar_ids = $old_scholar_id ? array( intval( $old_scholar_id ) ) : array();
			}
			$source = get_post_meta( get_the_ID(), 'source', true );
			$source_url = get_post_meta( get_the_ID(), 'source_url', true );
			
			if ( ! empty( $scholar_ids ) || $source ) :
				?>
				<div class="card" style="margin: var(--spacing-2xl) 0; padding: var(--spacing-lg); background-color: rgba(192, 132, 0, 0.05); border-left: 4px solid var(--color-accent);">
					<h4><?php _e( 'Translation Info', 'islamic-scholars' ); ?></h4>
					<?php if ( ! empty( $scholar_ids ) ) : ?>
						<p><strong><?php echo count( $scholar_ids ) > 1 ? __( 'Scholars:', 'islamic-scholars' ) : __( 'Scholar:', 'islamic-scholars' ); ?></strong> 
						<?php 
						$scholar_links = array();
						foreach ( $scholar_ids as $sid ) {
							$scholar_links[] = '<a href="' . esc_url( get_permalink( $sid ) ) . '">' . esc_html( get_the_title( $sid ) ) . '</a>';
						}
						echo implode( ', ', $scholar_links );
						?>
						</p>
					<?php endif; ?>
					<?php if ( $source ) : ?>
						<p><strong><?php _e( 'Source:', 'islamic-scholars' ); ?></strong> 
						<?php if ( $source_url ) : ?>
							<a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $source ); ?>
						<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			// Tags
			$tags = get_the_tags();
			if ( $tags ) :
				?>
				<div class="post-tags" style="margin: var(--spacing-xl) 0;">
					<strong><?php _e( 'Tags:', 'islamic-scholars' ); ?></strong>
					<div style="display: flex; flex-wrap: wrap; gap: var(--spacing-xs); margin-top: var(--spacing-sm);">
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" 
							   style="display: inline-block; padding: var(--spacing-xs) var(--spacing-sm); background-color: var(--color-bg-alt); border: 1px solid var(--color-border); border-radius: 4px; font-size: var(--fs-sm); color: var(--color-text); text-decoration: none; transition: all 0.2s ease;">
								<?php echo esc_html( $tag->name ); ?>
							</a>
						<?php endforeach; ?>
					</div>
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
