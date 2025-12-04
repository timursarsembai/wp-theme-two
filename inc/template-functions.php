<?php
/**
 * Template Helper Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get translation pairs
 */
function get_translation_pairs( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	
	$pairs = get_post_meta( $post_id, 'translation_pairs', true );
	return is_array( $pairs ) ? $pairs : array();
}

/**
 * Display translation pairs with pagination and footnotes
 */
function the_translation_pairs( $post_id = null ) {
	$pairs = get_translation_pairs( $post_id );
	
	if ( empty( $pairs ) ) {
		return;
	}
	
	$pairs_per_page = 20;
	$total_pairs = count( $pairs );
	$total_pages = ceil( $total_pairs / $pairs_per_page );
	$has_pagination = $total_pairs > $pairs_per_page;
	
	$footnote_label = __( 'Footnote', 'islamic-scholars' );
	$copy_link_label = __( 'Copy link', 'islamic-scholars' );
	$link_copied_label = __( 'Link copied!', 'islamic-scholars' );
	?>
	<div class="translation-pairs-container" data-pairs-per-page="<?php echo $pairs_per_page; ?>">
		<div class="translation-pairs">
			<?php foreach ( $pairs as $index => $pair ) : 
				$has_footnote = ! empty( $pair['footnote_original'] ) || ! empty( $pair['footnote_translation'] );
				$page_num = floor( $index / $pairs_per_page ) + 1;
				$pair_number = $index + 1;
			?>
				<div class="translation-pair<?php echo $has_pagination && $page_num > 1 ? ' hidden-pair' : ''; ?>" id="pair-<?php echo $pair_number; ?>" data-pair-id="<?php echo $index; ?>" data-page="<?php echo $page_num; ?>">
					<div class="pair-number-wrapper">
						<a href="#pair-<?php echo $pair_number; ?>" class="pair-number" data-pair="<?php echo $pair_number; ?>" title="<?php echo esc_attr( $copy_link_label ); ?>">#<?php echo $pair_number; ?></a>
					</div>
					<div class="translation-pair-content">
						<div class="translation-pair-original" dir="rtl">
							<div class="arabic-text">
								<?php echo wp_kses_post( $pair['original'] ); ?>
							</div>
							<?php if ( ! empty( $pair['footnote_original'] ) ) : ?>
								<div class="pair-footnote pair-footnote-original">
									<div class="footnote-content">
										<?php echo wp_kses_post( $pair['footnote_original'] ); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="translation-pair-translation">
							<div>
								<?php echo wp_kses_post( $pair['translation'] ); ?>
							</div>
							<?php if ( ! empty( $pair['footnote_translation'] ) ) : ?>
								<div class="pair-footnote pair-footnote-translation">
									<div class="footnote-content">
										<?php echo wp_kses_post( $pair['footnote_translation'] ); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		
		<?php if ( $has_pagination ) : ?>
		<div class="pairs-pagination">
			<button type="button" class="pagination-btn pagination-prev" disabled>
				<span aria-hidden="true">←</span>
				<?php _e( 'Previous', 'islamic-scholars' ); ?>
			</button>
			<span class="pagination-info">
				<?php printf( __( 'Page %1$s of %2$s', 'islamic-scholars' ), '<span class="current-page">1</span>', '<span class="total-pages">' . $total_pages . '</span>' ); ?>
			</span>
			<button type="button" class="pagination-btn pagination-next">
				<?php _e( 'Next', 'islamic-scholars' ); ?>
				<span aria-hidden="true">→</span>
			</button>
		</div>
		<?php endif; ?>
		
		<script>
		(function() {
			const container = document.querySelector('.translation-pairs-container');
			const pairs = container.querySelectorAll('.translation-pair');
			const pairsPerPage = <?php echo $pairs_per_page; ?>;
			const totalPages = <?php echo $total_pages; ?>;
			const hasPagination = <?php echo $has_pagination ? 'true' : 'false'; ?>;
			let currentPage = 1;
			
			const prevBtn = container.querySelector('.pagination-prev');
			const nextBtn = container.querySelector('.pagination-next');
			const currentPageEl = container.querySelector('.current-page');
			
			const copyLinkLabel = <?php echo json_encode( $copy_link_label ); ?>;
			const linkCopiedLabel = <?php echo json_encode( $link_copied_label ); ?>;
			
			function showPage(page, scroll = true) {
				currentPage = page;
				pairs.forEach(pair => {
					const pairPage = parseInt(pair.dataset.page);
					if (pairPage === page) {
						pair.classList.remove('hidden-pair');
					} else {
						pair.classList.add('hidden-pair');
					}
				});
				
				if (currentPageEl) {
					currentPageEl.textContent = page;
				}
				if (prevBtn) prevBtn.disabled = page === 1;
				if (nextBtn) nextBtn.disabled = page === totalPages;
				
				if (scroll) {
					container.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}
			}
			
			if (hasPagination && prevBtn && nextBtn) {
				prevBtn.addEventListener('click', () => {
					if (currentPage > 1) showPage(currentPage - 1);
				});
				
				nextBtn.addEventListener('click', () => {
					if (currentPage < totalPages) showPage(currentPage + 1);
				});
			}
			
			// Copy link functionality
			container.querySelectorAll('.pair-number').forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const pairNum = this.dataset.pair;
					const url = window.location.origin + window.location.pathname + '#pair-' + pairNum;
					
					navigator.clipboard.writeText(url).then(() => {
						this.classList.add('copied');
						this.title = linkCopiedLabel;
						setTimeout(() => {
							this.classList.remove('copied');
							this.title = copyLinkLabel;
						}, 2000);
					});
				});
			});
			
			// Handle hash on page load - navigate to correct page and scroll to pair
			function handleHash() {
				const hash = window.location.hash;
				if (hash && hash.startsWith('#pair-')) {
					const pairNum = parseInt(hash.replace('#pair-', ''));
					if (pairNum >= 1 && pairNum <= pairs.length) {
						const pairIndex = pairNum - 1;
						const targetPage = Math.floor(pairIndex / pairsPerPage) + 1;
						
						if (hasPagination && targetPage !== currentPage) {
							showPage(targetPage, false);
						}
						
						setTimeout(() => {
							const targetPair = document.getElementById('pair-' + pairNum);
							if (targetPair) {
								targetPair.scrollIntoView({ behavior: 'smooth', block: 'center' });
								targetPair.classList.add('pair-highlight');
								setTimeout(() => {
									targetPair.classList.remove('pair-highlight');
								}, 2000);
							}
						}, 100);
					}
				}
			}
			
			// Run on load
			handleHash();
			
			// Listen for hash changes
			window.addEventListener('hashchange', handleHash);
		})();
		</script>
	</div>
	<?php
}

/**
 * Get scholar by ID
 */
function get_scholar_data( $scholar_id ) {
	if ( ! $scholar_id ) {
		return null;
	}

	$scholar = get_post( $scholar_id );
	if ( ! $scholar || $scholar->post_type !== 'scholar' ) {
		return null;
	}

	return array(
		'id' => $scholar->ID,
		'name' => $scholar->post_title,
		'full_name' => get_post_meta( $scholar->ID, 'full_name', true ),
		'kunyah' => get_post_meta( $scholar->ID, 'kunyah', true ),
		'bio' => $scholar->post_content,
		'birth_year' => intval( get_post_meta( $scholar->ID, 'birth_year', true ) ),
		'death_year' => intval( get_post_meta( $scholar->ID, 'death_year', true ) ),
		'teachers' => (array) get_post_meta( $scholar->ID, 'teachers', true ),
		'image' => get_the_post_thumbnail_url( $scholar->ID, 'medium' ),
		'url' => get_permalink( $scholar->ID ),
	);
}

/**
 * Get scholars by century
 */
function get_scholars_by_century( $century_term_id ) {
	return get_posts( array(
		'post_type' => 'scholar',
		'tax_query' => array(
			array(
				'taxonomy' => 'centuries',
				'field' => 'term_id',
				'terms' => $century_term_id,
			),
		),
		'posts_per_page' => -1,
		'orderby' => 'meta_value_num',
		'meta_key' => 'death_year',
		'order' => 'ASC',
	) );
}

/**
 * Get all centuries ordered
 */
function get_all_centuries_ordered() {
	$terms = get_terms( array(
		'taxonomy' => 'centuries',
		'hide_empty' => false,
		'orderby' => 'name',
		'order' => 'ASC',
	) );

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	// Extract century numbers and sort numerically
	usort( $terms, function( $a, $b ) {
		preg_match( '/\d+/', $a->name, $matches_a );
		preg_match( '/\d+/', $b->name, $matches_b );
		$num_a = isset( $matches_a[0] ) ? intval( $matches_a[0] ) : 0;
		$num_b = isset( $matches_b[0] ) ? intval( $matches_b[0] ) : 0;
		return $num_a - $num_b;
	});

	return $terms;
}

/**
 * Get students of a scholar (scholars who list this scholar as teacher)
 */
function get_scholar_students( $scholar_id ) {
	global $wpdb;

	// teachers хранятся как serialized массив, например: a:2:{i:0;i:123;i:1;i:456;}
	// Нужно искать ID как элемент массива
	$pattern = ';"' . intval( $scholar_id ) . '";';
	
	$students = $wpdb->get_col( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} 
		WHERE meta_key = 'teachers' 
		AND (meta_value LIKE %s OR meta_value LIKE %s)
		AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'scholar' AND post_status = 'publish')",
		'%' . $wpdb->esc_like( $pattern ) . '%',  // Для serialized array
		'%"' . intval( $scholar_id ) . '"%'       // Fallback для JSON
	) );

	return $students ? $students : array();
}

/**
 * Get breadcrumb
 */
function islamic_scholars_breadcrumb() {
	$separator = ' / ';
	$home = __( 'Home', 'islamic-scholars' );
	$output = '';

	if ( ! is_home() && ! is_front_page() ) {
		$output .= '<nav class="breadcrumbs">';
		$output .= '<a href="' . home_url() . '">' . $home . '</a>' . $separator;

		if ( is_category() ) {
			$output .= '<span>' . get_category_title() . '</span>';
		} elseif ( is_singular( 'scholar' ) ) {
			// Scholar CPT - add archive link
			$scholars_label = __( 'Scholars', 'islamic-scholars' );
			$scholars_url = get_post_type_archive_link( 'scholar' );
			$output .= '<a href="' . esc_url( $scholars_url ) . '">' . $scholars_label . '</a>' . $separator;
			$output .= '<span>' . get_the_title() . '</span>';
		} elseif ( is_single() ) {
			$categories = get_the_category();
			if ( $categories ) {
				foreach ( $categories as $cat ) {
					$output .= '<a href="' . get_category_link( $cat->term_id ) . '">' . $cat->name . '</a>' . $separator;
				}
			}
			$output .= '<span>' . get_the_title() . '</span>';
		} elseif ( is_page() ) {
			$output .= '<span>' . get_the_title() . '</span>';
		} elseif ( is_search() ) {
			$output .= '<span>' . __( 'Search results for', 'islamic-scholars' ) . ' "' . get_search_query() . '"</span>';
		} elseif ( is_post_type_archive( 'scholar' ) ) {
			$output .= '<span>' . __( 'Scholars', 'islamic-scholars' ) . '</span>';
		}

		$output .= '</nav>';
	}

	echo wp_kses_post( $output );
}

/**
 * Get post metadata display
 */
function get_post_metadata_display( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	$output = '<div class="entry-meta">';

	// Date
	$output .= '<span class="posted-on">';
	$output .= '<time datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">';
	$output .= esc_html( get_the_date( 'F j, Y', $post_id ) );
	$output .= '</time></span>';

	// Categories
	if ( get_post_type( $post_id ) === 'post' ) {
		$categories = get_the_category( $post_id );
		if ( $categories ) {
			$output .= '<span class="taxonomy-links">';
			$cat_links = array_map( function( $cat ) {
				return '<a href="' . get_category_link( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</a>';
			}, $categories );
			$output .= implode( ', ', $cat_links );
			$output .= '</span>';
		}
	}

	$output .= '</div>';

	return $output;
}

/**
 * Display post metadata
 */
function the_post_metadata( $post_id = null ) {
	echo wp_kses_post( get_post_metadata_display( $post_id ) );
}

/**
 * Output Open Graph and Schema.org structured data
 */
function islamic_scholars_seo_meta() {
	$site_name = get_bloginfo( 'name' );
	$site_url = home_url( '/' );
	$locale = get_locale();
	
	// Default image (site logo or placeholder)
	$default_image = '';
	if ( has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		$default_image = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	
	// Determine page type and get relevant data
	if ( is_singular( 'scholar' ) ) {
		// Scholar page
		$scholar_id = get_the_ID();
		$title = get_the_title();
		$full_name = get_post_meta( $scholar_id, 'full_name', true );
		$birth_year = intval( get_post_meta( $scholar_id, 'birth_year', true ) );
		$death_year = intval( get_post_meta( $scholar_id, 'death_year', true ) );
		$description = get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 );
		$url = get_permalink();
		$image = get_the_post_thumbnail_url( $scholar_id, 'large' ) ?: $default_image;
		$published = get_the_date( 'c' );
		$modified = get_the_modified_date( 'c' );
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'profile',
			'title'       => $title . ' | ' . $site_name,
			'description' => $description,
			'url'         => $url,
			'image'       => $image,
			'locale'      => $locale,
			'site_name'   => $site_name,
		) );
		
		// Schema.org Person
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => $title,
			'url'         => $url,
			'description' => $description,
		);
		
		if ( $full_name ) {
			$schema['alternateName'] = $full_name;
		}
		
		if ( $birth_year && $death_year ) {
			$schema['birthDate'] = $birth_year . ' AH';
			$schema['deathDate'] = $death_year . ' AH';
		}
		
		if ( $image ) {
			$schema['image'] = $image;
		}
		
		// Get teachers
		$teachers = (array) get_post_meta( $scholar_id, 'teachers', true );
		if ( ! empty( $teachers ) ) {
			$schema['knows'] = array();
			foreach ( $teachers as $teacher_id ) {
				$teacher = get_post( $teacher_id );
				if ( $teacher && $teacher->post_type === 'scholar' ) {
					$schema['knows'][] = array(
						'@type' => 'Person',
						'name'  => $teacher->post_title,
						'url'   => get_permalink( $teacher_id ),
					);
				}
			}
		}
		
		islamic_scholars_output_schema( $schema );
		
	} elseif ( is_singular( 'post' ) ) {
		// Article/Translation page
		$post_id = get_the_ID();
		$title = get_the_title();
		$description = get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 );
		$url = get_permalink();
		$image = get_the_post_thumbnail_url( $post_id, 'large' ) ?: $default_image;
		$published = get_the_date( 'c' );
		$modified = get_the_modified_date( 'c' );
		$author = get_the_author();
		$scholar_id = get_post_meta( $post_id, 'scholar_id', true );
		$source = get_post_meta( $post_id, 'source', true );
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'article',
			'title'       => $title . ' | ' . $site_name,
			'description' => $description,
			'url'         => $url,
			'image'       => $image,
			'locale'      => $locale,
			'site_name'   => $site_name,
			'published'   => $published,
			'modified'    => $modified,
			'author'      => $author,
		) );
		
		// Schema.org Article
		$schema = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => $title,
			'url'              => $url,
			'description'      => $description,
			'datePublished'    => $published,
			'dateModified'     => $modified,
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => $url,
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => $site_name,
				'url'   => $site_url,
			),
		);
		
		if ( $image ) {
			$schema['image'] = $image;
			$schema['publisher']['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $default_image ?: $image,
			);
		}
		
		// If linked to a scholar, add author info
		if ( $scholar_id ) {
			$scholar = get_post( $scholar_id );
			if ( $scholar ) {
				$schema['author'] = array(
					'@type' => 'Person',
					'name'  => $scholar->post_title,
					'url'   => get_permalink( $scholar_id ),
				);
			}
		}
		
		if ( $source ) {
			$schema['isBasedOn'] = $source;
		}
		
		// Categories
		$categories = get_the_category( $post_id );
		if ( $categories ) {
			$schema['articleSection'] = wp_list_pluck( $categories, 'name' );
		}
		
		islamic_scholars_output_schema( $schema );
		
	} elseif ( is_front_page() || is_home() ) {
		// Homepage
		$title = $site_name;
		$description = get_bloginfo( 'description' );
		$url = $site_url;
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'website',
			'title'       => $title,
			'description' => $description,
			'url'         => $url,
			'image'       => $default_image,
			'locale'      => $locale,
			'site_name'   => $site_name,
		) );
		
		// Schema.org WebSite with SearchAction
		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'name'            => $site_name,
			'url'             => $site_url,
			'description'     => $description,
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => $site_url . '?s={search_term_string}',
				),
				'query-input' => 'required name=search_term_string',
			),
		);
		
		if ( $default_image ) {
			$schema['image'] = $default_image;
		}
		
		islamic_scholars_output_schema( $schema );
		
	} elseif ( is_category() || is_tax( 'centuries' ) ) {
		// Category or Taxonomy archive
		$term = get_queried_object();
		$title = $term->name . ' | ' . $site_name;
		$description = $term->description ?: sprintf( __( 'Browse %s', 'islamic-scholars' ), $term->name );
		$url = get_term_link( $term );
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'website',
			'title'       => $title,
			'description' => $description,
			'url'         => $url,
			'image'       => $default_image,
			'locale'      => $locale,
			'site_name'   => $site_name,
		) );
		
		// Schema.org CollectionPage
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'CollectionPage',
			'name'        => $term->name,
			'url'         => $url,
			'description' => $description,
		);
		
		islamic_scholars_output_schema( $schema );
		
	} elseif ( is_post_type_archive( 'scholar' ) ) {
		// Scholars archive
		$title = __( 'Scholars', 'islamic-scholars' ) . ' | ' . $site_name;
		$description = __( 'Explore the lives and works of Islamic scholars throughout history.', 'islamic-scholars' );
		$url = get_post_type_archive_link( 'scholar' );
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'website',
			'title'       => $title,
			'description' => $description,
			'url'         => $url,
			'image'       => $default_image,
			'locale'      => $locale,
			'site_name'   => $site_name,
		) );
		
		// Schema.org CollectionPage
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'CollectionPage',
			'name'        => __( 'Scholars', 'islamic-scholars' ),
			'url'         => $url,
			'description' => $description,
		);
		
		islamic_scholars_output_schema( $schema );
		
	} elseif ( is_search() ) {
		// Search results
		$query = get_search_query();
		$title = sprintf( __( 'Search results for "%s"', 'islamic-scholars' ), $query ) . ' | ' . $site_name;
		$url = get_search_link( $query );
		
		// Open Graph
		islamic_scholars_output_og_tags( array(
			'type'        => 'website',
			'title'       => $title,
			'description' => $title,
			'url'         => $url,
			'image'       => $default_image,
			'locale'      => $locale,
			'site_name'   => $site_name,
		) );
	}
}

/**
 * Output Open Graph meta tags
 */
function islamic_scholars_output_og_tags( $data ) {
	?>
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="<?php echo esc_attr( $data['type'] ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $data['title'] ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $data['description'] ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $data['url'] ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( $data['site_name'] ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( $data['locale'] ); ?>">
	<?php if ( ! empty( $data['image'] ) ) : ?>
	<meta property="og:image" content="<?php echo esc_url( $data['image'] ); ?>">
	<?php endif; ?>
	<?php if ( ! empty( $data['published'] ) ) : ?>
	<meta property="article:published_time" content="<?php echo esc_attr( $data['published'] ); ?>">
	<?php endif; ?>
	<?php if ( ! empty( $data['modified'] ) ) : ?>
	<meta property="article:modified_time" content="<?php echo esc_attr( $data['modified'] ); ?>">
	<?php endif; ?>
	
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $data['title'] ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $data['description'] ); ?>">
	<?php if ( ! empty( $data['image'] ) ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $data['image'] ); ?>">
	<?php endif; ?>
	<?php
}

/**
 * Output Schema.org JSON-LD
 */
function islamic_scholars_output_schema( $schema ) {
	?>
	<script type="application/ld+json">
	<?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?>
	</script>
	<?php
}

/**
 * Output breadcrumb Schema.org markup
 */
function islamic_scholars_breadcrumb_schema() {
	if ( is_home() || is_front_page() ) {
		return;
	}
	
	$items = array();
	$position = 1;
	
	// Home
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => __( 'Home', 'islamic-scholars' ),
		'item'     => home_url( '/' ),
	);
	
	if ( is_singular( 'scholar' ) ) {
		// Scholars archive
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => __( 'Scholars', 'islamic-scholars' ),
			'item'     => get_post_type_archive_link( 'scholar' ),
		);
		// Current scholar
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_singular( 'post' ) ) {
		// Category
		$categories = get_the_category();
		if ( $categories ) {
			$cat = $categories[0];
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $cat->name,
				'item'     => get_category_link( $cat->term_id ),
			);
		}
		// Current post
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_category() ) {
		$cat = get_queried_object();
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $cat->name,
			'item'     => get_category_link( $cat->term_id ),
		);
	} elseif ( is_post_type_archive( 'scholar' ) ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => __( 'Scholars', 'islamic-scholars' ),
			'item'     => get_post_type_archive_link( 'scholar' ),
		);
	}
	
	if ( count( $items ) > 1 ) {
		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
		
		islamic_scholars_output_schema( $schema );
	}
}

/**
 * Add canonical URL
 */
function islamic_scholars_canonical_url() {
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_category() || is_tax() ) {
		$url = get_term_link( get_queried_object() );
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_post_type() );
	} elseif ( is_home() || is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_search() ) {
		$url = get_search_link( get_search_query() );
	} else {
		return;
	}
	
	if ( $url && ! is_wp_error( $url ) ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}
}
