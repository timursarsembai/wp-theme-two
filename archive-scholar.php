<?php
/**
 * Scholar archive template
 */

get_header(); ?>

<main class="site-content container">
	<header class="entry-header" style="margin-bottom: var(--spacing-2xl);">
		<h1 class="entry-title"><?php _e( 'Scholars', 'islamic-scholars' ); ?></h1>
		<p style="color: var(--color-text-light); font-size: var(--fs-lg); max-width: 75ch;">
			<?php _e( 'Explore the lives and works of Islamic scholars throughout history.', 'islamic-scholars' ); ?>
		</p>
	</header>

	<!-- Century Filter -->
	<div class="archive-filters card" style="margin-bottom: var(--spacing-2xl); padding: var(--spacing-lg);">
		<div id="scholar-filter" style="display: flex; flex-wrap: wrap; gap: var(--spacing-lg); align-items: flex-end;">
			<?php
			$centuries = get_terms( array(
				'taxonomy'   => 'centuries',
				'hide_empty' => true,
				'orderby'    => 'slug',
				'order'      => 'ASC',
			) );
			
			if ( ! is_wp_error( $centuries ) && ! empty( $centuries ) ) :
				?>
				<div>
					<label for="century-filter" style="font-weight: 600; display: block; margin-bottom: var(--spacing-xs);">
						<?php _e( 'Century', 'islamic-scholars' ); ?>
					</label>
					<select id="century-filter" style="padding: 8px; min-width: 200px;">
						<option value=""><?php _e( 'All Centuries', 'islamic-scholars' ); ?></option>
						<?php foreach ( $centuries as $century ) : ?>
							<option value="<?php echo esc_attr( $century->slug ); ?>">
								<?php echo esc_html( $century->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div id="filter-loading" style="display: none; color: var(--color-text-light);">
				<?php _e( 'Loading...', 'islamic-scholars' ); ?>
			</div>
		</div>
	</div>

	<!-- Scholars Grid -->
	<div id="scholars-grid" class="scholars-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">
		<?php
		$args = array(
			'post_type'      => 'scholar',
			'posts_per_page' => 24,
			'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'death_year',
			'order'          => 'ASC',
		);

		$scholars_query = new WP_Query( $args );

		if ( $scholars_query->have_posts() ) :
			while ( $scholars_query->have_posts() ) :
				$scholars_query->the_post();
				get_template_part( 'template-parts/content', 'scholar-card' );
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>' . __( 'No scholars found.', 'islamic-scholars' ) . '</p>';
		endif;
		?>
	</div>

	<!-- Pagination -->
	<nav id="scholars-pagination" class="pagination">
		<?php
		$big = 999999999;
		echo paginate_links( array(
			'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total'   => $scholars_query->max_num_pages,
			'type'    => 'list',
		) );
		?>
	</nav>

	<script>
	(function() {
		const centuryFilter = document.getElementById('century-filter');
		const scholarsGrid = document.getElementById('scholars-grid');
		const pagination = document.getElementById('scholars-pagination');
		const loading = document.getElementById('filter-loading');
		
		if (!centuryFilter) return;
		
		centuryFilter.addEventListener('change', function() {
			filterScholars();
		});
		
		function filterScholars(page = 1) {
			const century = centuryFilter.value;
			
			loading.style.display = 'inline';
			scholarsGrid.style.opacity = '0.5';
			
			const formData = new FormData();
			formData.append('action', 'islamic_scholars_filter_scholars');
			formData.append('nonce', '<?php echo wp_create_nonce( 'islamic_scholars_ajax' ); ?>');
			formData.append('century', century);
			formData.append('page', page);
			
			fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					scholarsGrid.innerHTML = data.data.html;
					pagination.innerHTML = data.data.pagination;
					
					// Re-attach pagination click handlers
					attachPaginationHandlers();
				}
				loading.style.display = 'none';
				scholarsGrid.style.opacity = '1';
			})
			.catch(error => {
				console.error('Error:', error);
				loading.style.display = 'none';
				scholarsGrid.style.opacity = '1';
			});
		}
		
		function attachPaginationHandlers() {
			pagination.querySelectorAll('a.page-numbers').forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const url = new URL(this.href);
					const page = url.searchParams.get('paged') || 1;
					filterScholars(page);
					window.scrollTo({ top: scholarsGrid.offsetTop - 100, behavior: 'smooth' });
				});
			});
		}
		
		// Initial pagination handlers
		attachPaginationHandlers();
	})();
	</script>
</main>

<?php get_footer();
