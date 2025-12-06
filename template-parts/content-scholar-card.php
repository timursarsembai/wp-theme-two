<?php
/**
 * Scholar card template part
 */
?>
<div class="card scholar-card" style="margin-bottom: var(--spacing-lg);">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-thumbnail">
			<?php the_post_thumbnail( 'medium', array( 
				'style' => 'width: 100%; height: auto; border-radius: 4px; display: block; margin-bottom: var(--spacing-md);',
				'loading' => 'lazy',
				'decoding' => 'async',
			) ); ?>
		</a>
	<?php endif; ?>

	<div class="card-content">
		<p style="font-size: var(--fs-sm); color: var(--color-text-light); margin-bottom: var(--spacing-xs); text-transform: uppercase; font-weight: 600;">
			<?php _e( 'Scholar', 'islamic-scholars' ); ?>
		</p>

		<h3 class="card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php
		$birth_year = intval( get_post_meta( get_the_ID(), 'birth_year', true ) );
		$death_year = intval( get_post_meta( get_the_ID(), 'death_year', true ) );
		if ( $birth_year ) {
			?>
			<p style="color: var(--color-primary-dark); font-weight: 600; margin-bottom: var(--spacing-md);">
				<?php 
				if ( $death_year ) {
					printf( __( '%d–%d AH', 'islamic-scholars' ), $birth_year, $death_year );
				} else {
					printf( __( '%d AH – present', 'islamic-scholars' ), $birth_year );
				}
				?>
			</p>
			<?php
		}
		?>

		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>

		<a href="<?php the_permalink(); ?>" style="color: var(--color-primary-dark); font-weight: 600; font-size: var(--fs-sm); display: inline-block; margin-top: var(--spacing-md);">
			<?php _e( 'View profile →', 'islamic-scholars' ); ?>
		</a>
	</div>
</div>
