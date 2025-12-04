<?php
/**
 * Default post card template part
 */
?>
<div class="card post-card" style="margin-bottom: var(--spacing-lg);">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="post-thumbnail">
			<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: auto; border-radius: 4px; display: block; margin-bottom: var(--spacing-md);' ) ); ?>
		</a>
	<?php endif; ?>

	<div class="card-content">
		<h3 class="card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="card-meta" style="font-size: var(--fs-sm); color: var(--color-text-light); display: flex; gap: var(--spacing-lg); flex-wrap: wrap; margin-bottom: var(--spacing-md);">
			<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
			<?php
			$categories = get_the_category();
			if ( $categories ) {
				$cat_names = wp_list_pluck( $categories, 'name' );
				echo '<span>' . implode( ', ', $cat_names ) . '</span>';
			}
			?>
		</div>

		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>

		<a href="<?php the_permalink(); ?>" style="color: var(--color-primary-dark); font-weight: 600; font-size: var(--fs-sm); display: inline-block; margin-top: var(--spacing-md);">
			<?php _e( 'Read more →', 'islamic-scholars' ); ?>
		</a>
	</div>
</div>
