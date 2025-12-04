<?php
/**
 * Compact post card template (no excerpt)
 */
?>
<div class="card post-card post-card-compact" style="margin-bottom: var(--spacing-lg);">
	<div class="card-content">
		<h3 class="card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="card-meta" style="font-size: var(--fs-sm); color: var(--color-text-light); display: flex; gap: var(--spacing-lg); flex-wrap: wrap; margin-bottom: var(--spacing-md);">
			<span><?php echo esc_html( get_the_date( 'j F, Y' ) ); ?></span>
			<?php
			$categories = get_the_category();
			if ( $categories ) {
				$cat_links = array_map( function( $cat ) {
					return '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
				}, $categories );
				echo '<span>' . implode( ', ', $cat_links ) . '</span>';
			}
			?>
		</div>

		<a href="<?php the_permalink(); ?>" style="color: var(--color-primary-dark); font-weight: 600; font-size: var(--fs-sm); display: inline-block;">
			<?php _e( 'Read more →', 'islamic-scholars' ); ?>
		</a>
	</div>
</div>
