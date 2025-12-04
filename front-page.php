<?php
/**
 * Front page template
 */

get_header(); ?>

<main class="site-content container">
	
	<!-- Tafsir Section -->
	<?php
	$tafsir_cat = get_category_by_slug( 'tafsir' );
	$tafsir_posts = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'category_name'  => 'tafsir',
	) );

	if ( $tafsir_posts->have_posts() ) :
		?>
		<section class="tafsir-section" style="margin-bottom: var(--spacing-2xl);">
			<h2 class="section-title" style="margin-bottom: var(--spacing-xl);">
				<a href="<?php echo $tafsir_cat ? esc_url( get_category_link( $tafsir_cat->term_id ) ) : '#'; ?>" style="color: var(--color-primary-dark); text-decoration: none;">
					<?php _e( 'Tafsir', 'islamic-scholars' ); ?> →
				</a>
			</h2>
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg);">
				<?php
				while ( $tafsir_posts->have_posts() ) :
					$tafsir_posts->the_post();
					get_template_part( 'template-parts/content', 'compact' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Hadith Section -->
	<?php
	$hadith_cat = get_category_by_slug( 'hadith' );
	$hadith_posts = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'category_name'  => 'hadith',
	) );

	if ( $hadith_posts->have_posts() ) :
		?>
		<section class="hadith-section" style="margin-bottom: var(--spacing-2xl);">
			<h2 class="section-title" style="margin-bottom: var(--spacing-xl);">
				<a href="<?php echo $hadith_cat ? esc_url( get_category_link( $hadith_cat->term_id ) ) : '#'; ?>" style="color: var(--color-primary-dark); text-decoration: none;">
					<?php _e( 'Hadith Collections', 'islamic-scholars' ); ?> →
				</a>
			</h2>
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg);">
				<?php
				while ( $hadith_posts->have_posts() ) :
					$hadith_posts->the_post();
					get_template_part( 'template-parts/content', 'compact' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Fatawa Section -->
	<?php
	$fatawa_cat = get_category_by_slug( 'fatawa' );
	$fatawa_posts = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'category_name'  => 'fatawa',
	) );

	if ( $fatawa_posts->have_posts() ) :
		?>
		<section class="fatawa-section" style="margin-bottom: var(--spacing-2xl);">
			<h2 class="section-title" style="margin-bottom: var(--spacing-xl);">
				<a href="<?php echo $fatawa_cat ? esc_url( get_category_link( $fatawa_cat->term_id ) ) : '#'; ?>" style="color: var(--color-primary-dark); text-decoration: none;">
					<?php _e( 'Fatawa', 'islamic-scholars' ); ?> →
				</a>
			</h2>
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg);">
				<?php
				while ( $fatawa_posts->have_posts() ) :
					$fatawa_posts->the_post();
					get_template_part( 'template-parts/content', 'compact' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Books Section -->
	<?php
	$books_cat = get_category_by_slug( 'books' );
	$books_posts = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'category_name'  => 'books',
	) );

	if ( $books_posts->have_posts() ) :
		?>
		<section class="books-section" style="margin-bottom: var(--spacing-2xl);">
			<h2 class="section-title" style="margin-bottom: var(--spacing-xl);">
				<a href="<?php echo $books_cat ? esc_url( get_category_link( $books_cat->term_id ) ) : '#'; ?>" style="color: var(--color-primary-dark); text-decoration: none;">
					<?php _e( 'Books, Articles and Epistles', 'islamic-scholars' ); ?> →
				</a>
			</h2>
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg);">
				<?php
				while ( $books_posts->have_posts() ) :
					$books_posts->the_post();
					get_template_part( 'template-parts/content', 'compact' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Scholars Section -->
	<?php
	$scholars = new WP_Query( array(
		'post_type'      => 'scholar',
		'posts_per_page' => 12,
		'orderby'        => 'meta_value_num',
		'meta_key'       => 'death_year',
		'order'          => 'ASC',
	) );

	if ( $scholars->have_posts() ) :
		?>
		<section class="scholars-section">
			<h2 class="section-title" style="margin-bottom: var(--spacing-xl);">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'scholar' ) ); ?>" style="color: var(--color-primary-dark); text-decoration: none;">
					<?php _e( 'Scholars', 'islamic-scholars' ); ?> →
				</a>
			</h2>
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-lg);">
				<?php
				while ( $scholars->have_posts() ) :
					$scholars->the_post();
					get_template_part( 'template-parts/content', 'scholar-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php get_footer();
