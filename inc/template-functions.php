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
 * Display translation pairs
 */
function the_translation_pairs( $post_id = null ) {
	$pairs = get_translation_pairs( $post_id );
	
	if ( empty( $pairs ) ) {
		return;
	}
	?>
	<div class="translation-pairs">
		<?php foreach ( $pairs as $index => $pair ) : ?>
			<div class="translation-pair" data-pair-id="<?php echo $index; ?>">
				<div class="translation-pair-original" dir="rtl">
					<div class="arabic-text">
						<?php echo wp_kses_post( $pair['original'] ); ?>
					</div>
				</div>
				<div class="translation-pair-translation">
					<div>
						<?php echo wp_kses_post( $pair['translation'] ); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
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
