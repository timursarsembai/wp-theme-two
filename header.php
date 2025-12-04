<?php
/**
 * Header template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="site-header">
		<div class="container">
			<div class="header-inner">
				<div class="site-branding">
					<?php
					if ( has_custom_logo() ) {
						$custom_logo_id = get_theme_mod( 'custom_logo' );
						$site_name = get_bloginfo( 'name' );
						$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" title="<?php echo esc_attr( $site_name ); ?>" class="custom-logo">
						</a>
						<?php
					} else {
						?>
						<div class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php bloginfo( 'name' ); ?>
							</a>
						</div>
						<?php
					}
					?>
				</div>

				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'islamic-scholars' ); ?>">
					<span class="hamburger"></span>
				</button>

				<!-- Search Button -->
				<button class="search-toggle" aria-label="<?php esc_attr_e( 'Search', 'islamic-scholars' ); ?>" title="<?php esc_attr_e( 'Search', 'islamic-scholars' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="11" cy="11" r="8"></circle>
						<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
					</svg>
				</button>

				<nav id="primary-menu" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'islamic-scholars' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'fallback_cb' => 'wp_page_menu',
						'container' => false,
						'items_wrap' => '<ul class="nav-menu">%3$s</ul>',
					) );
					?>
				</nav>
			</div>

			<script>
			(function() {
				const toggle = document.querySelector('.menu-toggle');
				const nav = document.querySelector('.main-navigation');
				const body = document.body;
				
				if (toggle && nav) {
					toggle.addEventListener('click', function() {
						const expanded = this.getAttribute('aria-expanded') === 'true';
						this.setAttribute('aria-expanded', !expanded);
						nav.classList.toggle('is-open');
						body.classList.toggle('menu-open');
					});
					
					// Close menu on escape
					document.addEventListener('keydown', function(e) {
						if (e.key === 'Escape' && nav.classList.contains('is-open')) {
							toggle.setAttribute('aria-expanded', 'false');
							nav.classList.remove('is-open');
							body.classList.remove('menu-open');
						}
					});
					
					// Close menu when clicking a link
					nav.querySelectorAll('a').forEach(function(link) {
						link.addEventListener('click', function() {
							toggle.setAttribute('aria-expanded', 'false');
							nav.classList.remove('is-open');
							body.classList.remove('menu-open');
						});
					});
					
					// Close menu when clicking overlay
					document.addEventListener('click', function(e) {
						if (nav.classList.contains('is-open') && 
							!nav.contains(e.target) && 
							!toggle.contains(e.target)) {
							toggle.setAttribute('aria-expanded', 'false');
							nav.classList.remove('is-open');
							body.classList.remove('menu-open');
						}
					});
				}
			})();
			</script>
		</div>
	</header>

	<!-- Search Modal -->
	<div id="search-modal" class="search-modal" aria-hidden="true">
		<div class="search-modal-overlay"></div>
		<div class="search-modal-content">
			<button class="search-modal-close" aria-label="<?php esc_attr_e( 'Close search', 'islamic-scholars' ); ?>">&times;</button>
			
			<h2 class="search-modal-title"><?php _e( 'Search', 'islamic-scholars' ); ?></h2>
			
			<form id="advanced-search-form" class="search-form-advanced" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<div class="search-input-wrapper">
					<input type="search" name="s" id="search-input" placeholder="<?php esc_attr_e( 'Enter search query...', 'islamic-scholars' ); ?>" value="" autocomplete="off">
				</div>
				
				<!-- Hidden field for post_type, controlled by JS -->
				<input type="hidden" name="post_type" id="search-post-type" value="post">
				
				<div class="search-filters">
					<div class="search-filter-group" id="category-filter-group">
						<label for="search-category"><?php _e( 'Category:', 'islamic-scholars' ); ?></label>
						<select name="category_name" id="search-category">
							<option value=""><?php _e( 'All categories', 'islamic-scholars' ); ?></option>
							<?php
							$main_categories = array( 'tafsir', 'hadith', 'fatawa', 'books' );
							foreach ( $main_categories as $cat_slug ) :
								$category = get_category_by_slug( $cat_slug );
								if ( $category ) :
									?>
									<option value="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></option>
									<?php
								endif;
							endforeach;
							?>
							<option value="__scholars__"><?php _e( 'Scholars', 'islamic-scholars' ); ?></option>
						</select>
					</div>
				</div>
				
				<button type="submit" class="search-submit-btn">
					<?php _e( 'Search', 'islamic-scholars' ); ?>
				</button>
			</form>
		</div>
	</div>

	<script>
	(function() {
		// Search Modal
		const searchToggle = document.querySelector('.search-toggle');
		const searchModal = document.getElementById('search-modal');
		const searchClose = document.querySelector('.search-modal-close');
		const searchOverlay = document.querySelector('.search-modal-overlay');
		const searchInput = document.getElementById('search-input');
		const postTypeHidden = document.getElementById('search-post-type');
		const categorySelect = document.getElementById('search-category');
		
		if (searchToggle && searchModal) {
			// Open modal
			searchToggle.addEventListener('click', function() {
				searchModal.classList.add('is-open');
				searchModal.setAttribute('aria-hidden', 'false');
				document.body.classList.add('search-open');
				setTimeout(() => searchInput.focus(), 100);
			});
			
			// Close modal
			function closeSearchModal() {
				searchModal.classList.remove('is-open');
				searchModal.setAttribute('aria-hidden', 'true');
				document.body.classList.remove('search-open');
			}
			
			searchClose.addEventListener('click', closeSearchModal);
			searchOverlay.addEventListener('click', closeSearchModal);
			
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && searchModal.classList.contains('is-open')) {
					closeSearchModal();
				}
			});
			
			// Update post_type based on category selection
			categorySelect.addEventListener('change', function() {
				if (this.value === '__scholars__') {
					postTypeHidden.value = 'scholar';
					// Clear category_name for scholars search
					this.name = '';
				} else {
					postTypeHidden.value = 'post';
					this.name = 'category_name';
				}
			});
		}
	})();
	</script>

	<div id="page" class="site">
		<div id="content" class="site-content-wrapper">
