<?php
/**
 * Islamic Scholars Translation Theme - Functions
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );
define( 'THEME_VERSION', '1.0.0' );

/**
 * Load translations from l10n.php file
 * This is more reliable than MO files in WordPress 6.5+
 */
function islamic_scholars_load_translations() {
	$locale = determine_locale();
	if ( $locale === 'en_US' ) {
		return; // No translation needed for English
	}
	
	$l10n_file = THEME_DIR . '/languages/islamic-scholars-' . $locale . '.l10n.php';
	
	if ( ! file_exists( $l10n_file ) ) {
		return;
	}
	
	$translations = include $l10n_file;
	
	if ( ! is_array( $translations ) || ! isset( $translations['locale_data'] ) ) {
		return;
	}
	
	// Create MO object and populate with translations
	$mo = new MO();
	
	// Set plural forms header for Russian
	$mo->set_header( 'Plural-Forms', 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);' );
	
	$locale_data = $translations['locale_data']['islamic-scholars'];
	
	foreach ( $locale_data as $original => $trans_array ) {
		if ( $original === '' ) continue; // Skip header
		
		// Check if this is a plural entry with 'plural' and 'translations' keys
		if ( is_array( $trans_array ) && isset( $trans_array['plural'] ) && isset( $trans_array['translations'] ) ) {
			// Plural form with explicit plural key
			$entry = new Translation_Entry( array(
				'singular'     => $original,
				'plural'       => $trans_array['plural'],
				'translations' => $trans_array['translations'],
			) );
		} elseif ( is_array( $trans_array ) && count( $trans_array ) > 1 && isset( $trans_array[0] ) ) {
			// Plural form (array with numeric keys, multiple translations)
			$entry = new Translation_Entry( array(
				'singular'     => $original,
				'plural'       => $original,
				'translations' => $trans_array,
			) );
		} else {
			// Singular form
			$translation = is_array( $trans_array ) ? $trans_array[0] : $trans_array;
			$entry = new Translation_Entry( array(
				'singular'     => $original,
				'translations' => array( $translation ),
			) );
		}
		$mo->add_entry( $entry );
	}
	
	// Register the textdomain
	$GLOBALS['l10n']['islamic-scholars'] = $mo;
}

// Load translations as early as possible
add_action( 'after_setup_theme', 'islamic_scholars_load_translations', 1 );
// Also load on admin_init for admin pages
add_action( 'admin_init', 'islamic_scholars_load_translations', 1 );

// Include theme files (after textdomain is loaded)
require_once THEME_DIR . '/inc/cpt-taxonomies.php';
require_once THEME_DIR . '/inc/metaboxes.php';
require_once THEME_DIR . '/inc/template-functions.php';

/**
 * Theme Setup
 */
function islamic_scholars_setup() {
	// Add theme support
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 250,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	
	// Register menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'islamic-scholars' ),
		'footer' => __( 'Footer Menu', 'islamic-scholars' ),
	) );
}
add_action( 'after_setup_theme', 'islamic_scholars_setup' );

/**
 * Enqueue scripts and styles
 */
function islamic_scholars_enqueue_assets() {
	// Fonts
	wp_enqueue_style(
		'google-fonts',
		'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Inter:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap',
		array(),
		null
	);
	
	// Main stylesheet
	wp_enqueue_style(
		'islamic-scholars-style',
		THEME_URI . '/style.css',
		array(),
		THEME_VERSION
	);
	
	// Translation pair interaction script
	wp_enqueue_script(
		'islamic-scholars-translations',
		THEME_URI . '/assets/js/translations.js',
		array(),
		THEME_VERSION,
		true
	);
	
	// Chronology page script
	if ( is_page_template( 'page-chronology.php' ) ) {
		wp_enqueue_script(
			'islamic-scholars-chronology',
			THEME_URI . '/assets/js/chronology.js',
			array(),
			THEME_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'islamic_scholars_enqueue_assets' );

/**
 * Register widget areas
 */
function islamic_scholars_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Primary Sidebar', 'islamic-scholars' ),
		'id' => 'primary-sidebar',
		'description' => __( 'Main sidebar area', 'islamic-scholars' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area', 'islamic-scholars' ),
		'id' => 'footer-area',
		'description' => __( 'Footer widget area', 'islamic-scholars' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'islamic_scholars_widgets_init' );

/**
 * Modify archive queries for scholars filter
 */
function islamic_scholars_modify_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Handle centuries taxonomy archive with filter
	if ( $query->is_tax( 'centuries' ) ) {
		$filter_century = isset( $_GET['centuries'] ) ? sanitize_text_field( $_GET['centuries'] ) : '';
		
		// If empty filter selected (All Centuries), show all scholars
		if ( $filter_century === '' && isset( $_GET['centuries'] ) ) {
			// Remove the taxonomy query and show all scholars
			$query->set( 'post_type', 'scholar' );
			$query->set( 'tax_query', array() );
			// Override the queried object to prevent 404
			$query->is_tax = false;
			$query->is_post_type_archive = true;
		} elseif ( $filter_century ) {
			// Filter by specific century
			$query->set( 'tax_query', array(
				array(
					'taxonomy' => 'centuries',
					'field'    => 'slug',
					'terms'    => $filter_century,
				),
			) );
		}
	}
}
add_action( 'pre_get_posts', 'islamic_scholars_modify_archive_query' );

/**
 * Get century based on birth and death years
 */
function get_scholar_century( $birth_year, $death_year ) {
	if ( ! $birth_year || ! $death_year ) {
		return null;
	}

	$birth_century = ceil( $birth_year / 100 );
	$death_century = ceil( $death_year / 100 );

	// If spans multiple centuries, assign to death century
	if ( $death_century > $birth_century ) {
		return $death_century;
	}

	return $birth_century;
}

/**
 * Update existing century terms to translated names
 */
function islamic_scholars_update_century_terms() {
	// Get all century terms
	$centuries = get_terms( array(
		'taxonomy' => 'centuries',
		'hide_empty' => false,
	) );
	
	if ( is_wp_error( $centuries ) ) {
		return;
	}
	
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'Islamic Scholars: Updating ' . count( $centuries ) . ' century terms to ' . get_locale() . ' locale' );
	}
	
	// Update each century term name
	foreach ( $centuries as $term ) {
		// Extract the number from the term name/slug
		if ( preg_match( '/century-(\d+)/', $term->slug, $matches ) ) {
			$century_num = intval( $matches[1] );
			$new_name = sprintf( __( '%d Century AH', 'islamic-scholars' ), $century_num );
			
			// Update the term name if it changed
			if ( $term->name !== $new_name ) {
				wp_update_term( $term->term_id, 'centuries', array( 'name' => $new_name ) );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Islamic Scholars: Updated "' . $term->name . '" to "' . $new_name . '"' );
				}
			}
		}
	}
}
add_action( 'plugins_loaded', 'islamic_scholars_update_century_terms', 1 );
// Also run on frontend (but only once per page load)
add_action( 'wp', 'islamic_scholars_update_century_terms', 1 );

/**
 * Auto-assign scholar to century taxonomy on save
 */
function islamic_scholars_auto_assign_century( $post_id ) {
	if ( get_post_type( $post_id ) !== 'scholar' ) {
		return;
	}

	// Prevent infinite loops
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$birth_year = (int) get_post_meta( $post_id, 'birth_year', true );
	$death_year = (int) get_post_meta( $post_id, 'death_year', true );

	if ( ! $birth_year || ! $death_year ) {
		return;
	}

	$century = get_scholar_century( $birth_year, $death_year );

	if ( $century ) {
		$century_name = sprintf( __( '%d Century AH', 'islamic-scholars' ), $century );
		
		// Check if term already exists
		$century_term = get_term_by( 'name', $century_name, 'centuries' );
		
		if ( ! $century_term ) {
			// Create new term if it doesn't exist
			$result = wp_insert_term(
				$century_name,
				'centuries',
				array( 'slug' => 'century-' . $century )
			);
			
			if ( is_wp_error( $result ) ) {
				return;
			}
			
			$century_term_id = $result['term_id'];
		} else {
			$century_term_id = $century_term->term_id;
		}

		// Assign term to post
		wp_set_post_terms( $post_id, array( $century_term_id ), 'centuries', false );
	}
}
add_action( 'save_post_scholar', 'islamic_scholars_auto_assign_century' );

/**
 * Custom excerpt length
 */
function islamic_scholars_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'islamic_scholars_excerpt_length' );

/**
 * Custom excerpt more
 */
function islamic_scholars_excerpt_more( $more ) {
	return ' ... <a href="' . esc_url( get_permalink() ) . '">' . __( 'Read more', 'islamic-scholars' ) . '</a>';
}
add_filter( 'excerpt_more', 'islamic_scholars_excerpt_more' );

/**
 * Disable comments on specific post types
 */
function islamic_scholars_disable_comments( $open, $post_id ) {
	$post_type = get_post_type( $post_id );
	if ( in_array( $post_type, array( 'scholar' ), true ) ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'islamic_scholars_disable_comments', 10, 2 );

/**
 * Add body classes
 */
function islamic_scholars_body_class( $classes ) {
	if ( is_singular( 'scholar' ) ) {
		$classes[] = 'template-scholar';
	}
	return $classes;
}
add_filter( 'body_class', 'islamic_scholars_body_class' );

/**
 * AJAX handler for category post filtering
 */
function islamic_scholars_filter_posts_ajax() {
	check_ajax_referer( 'islamic_scholars_ajax', 'nonce' );
	
	$category_id = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;
	$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
	$subcategory = isset( $_POST['subcategory'] ) ? sanitize_text_field( $_POST['subcategory'] ) : '';
	
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => 20,
		'post_status'    => 'publish',
	);
	
	// Category filter
	if ( $subcategory ) {
		$args['category_name'] = $subcategory;
	} elseif ( $category_id ) {
		$args['cat'] = $category_id;
	}
	
	// Search filter
	if ( $search ) {
		$args['s'] = $search;
	}
	
	$query = new WP_Query( $args );
	
	ob_start();
	
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part( 'template-parts/content', 'compact' );
		endwhile;
		wp_reset_postdata();
	else :
		echo '<p class="no-posts">' . __( 'No posts found.', 'islamic-scholars' ) . '</p>';
	endif;
	
	$html = ob_get_clean();
	
	// Generate pagination
	ob_start();
	echo paginate_links( array(
		'total'     => $query->max_num_pages,
		'current'   => 1,
		'prev_text' => __( '← Previous', 'islamic-scholars' ),
		'next_text' => __( 'Next →', 'islamic-scholars' ),
	) );
	$pagination = ob_get_clean();
	
	wp_send_json_success( array(
		'html'       => $html,
		'pagination' => $pagination,
		'count'      => $query->found_posts,
	) );
}
add_action( 'wp_ajax_islamic_scholars_filter_posts', 'islamic_scholars_filter_posts_ajax' );
add_action( 'wp_ajax_nopriv_islamic_scholars_filter_posts', 'islamic_scholars_filter_posts_ajax' );

/**
 * AJAX handler for scholars archive filtering
 */
function islamic_scholars_filter_scholars_ajax() {
	check_ajax_referer( 'islamic_scholars_ajax', 'nonce' );
	
	$century = isset( $_POST['century'] ) ? sanitize_text_field( $_POST['century'] ) : '';
	$page = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
	
	$args = array(
		'post_type'      => 'scholar',
		'posts_per_page' => 24,
		'paged'          => $page,
		'orderby'        => 'meta_value_num',
		'meta_key'       => 'death_year',
		'order'          => 'ASC',
	);
	
	if ( $century ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'centuries',
				'field'    => 'slug',
				'terms'    => $century,
			),
		);
	}
	
	$query = new WP_Query( $args );
	
	ob_start();
	
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part( 'template-parts/content', 'scholar-card' );
		endwhile;
		wp_reset_postdata();
	else :
		echo '<p>' . __( 'No scholars found.', 'islamic-scholars' ) . '</p>';
	endif;
	
	$html = ob_get_clean();
	
	// Generate pagination
	ob_start();
	$big = 999999999;
	echo paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'    => '?paged=%#%',
		'current'   => $page,
		'total'     => $query->max_num_pages,
		'type'      => 'list',
		'prev_text' => __( '← Previous', 'islamic-scholars' ),
		'next_text' => __( 'Next →', 'islamic-scholars' ),
	) );
	$pagination = ob_get_clean();
	
	wp_send_json_success( array(
		'html'       => $html,
		'pagination' => $pagination,
		'count'      => $query->found_posts,
	) );
}
add_action( 'wp_ajax_islamic_scholars_filter_scholars', 'islamic_scholars_filter_scholars_ajax' );
add_action( 'wp_ajax_nopriv_islamic_scholars_filter_scholars', 'islamic_scholars_filter_scholars_ajax' );

/**
 * Modify search query to handle post_type and taxonomy filters
 */
function islamic_scholars_modify_search_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		// Handle post type filter
		if ( isset( $_GET['post_type'] ) && ! empty( $_GET['post_type'] ) ) {
			$post_type = sanitize_text_field( $_GET['post_type'] );
			$query->set( 'post_type', $post_type );
		} else {
			// Search in both posts and scholars by default
			$query->set( 'post_type', array( 'post', 'scholar' ) );
		}
		
		// Handle century filter for scholars
		if ( isset( $_GET['centuries'] ) && ! empty( $_GET['centuries'] ) ) {
			$tax_query = $query->get( 'tax_query' ) ?: array();
			$tax_query[] = array(
				'taxonomy' => 'centuries',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $_GET['centuries'] ),
			);
			$query->set( 'tax_query', $tax_query );
		}
	}
	
	return $query;
}
add_action( 'pre_get_posts', 'islamic_scholars_modify_search_query' );

/**
 * Debug page for Scholar CPT (temporary - remove after debugging)
 */
function islamic_scholars_debug_page() {
	// Only accessible to admins via query string
	if ( ! isset( $_GET['islamic_scholars_debug'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	add_action( 'wp_footer', function() {
		// Check if we're on a scholar page
		$is_scholar = is_singular( 'scholar' );
		?>
		<div id="islamic-scholars-debug" style="background: #f5f5f5; border: 2px solid #0073aa; padding: 20px; margin: 20px; font-family: monospace; font-size: 12px; max-width: 1200px; overflow: auto;">
			<h2 style="margin-top: 0;">🔍 Scholar CPT Debug</h2>
			
			<?php
			// If on scholar page, test loading that specific scholar
			if ( $is_scholar ) {
				echo '<h3 style="color: #0073aa;">📄 ' . __( 'Current Scholar Page', 'islamic-scholars' ) . '</h3>';
				$scholar_id = get_the_ID();
				$scholar = get_post( $scholar_id );
				
				echo '<p><strong>' . __( 'Post ID:', 'islamic-scholars' ) . '</strong> ' . $scholar_id . '</p>';
				echo '<p><strong>' . __( 'Title:', 'islamic-scholars' ) . '</strong> ' . $scholar->post_title . '</p>';
				echo '<p><strong>' . __( 'Status:', 'islamic-scholars' ) . '</strong> ' . $scholar->post_status . '</p>';
				
				// Try to load all the meta
				$birth_year = intval( get_post_meta( $scholar_id, 'birth_year', true ) );
				$death_year = intval( get_post_meta( $scholar_id, 'death_year', true ) );
				$full_name = get_post_meta( $scholar_id, 'full_name', true );
				$kunyah = get_post_meta( $scholar_id, 'kunyah', true );
				$teachers = get_post_meta( $scholar_id, 'teachers', true );
				
				echo '<p><strong>' . __( 'Birth Year:', 'islamic-scholars' ) . '</strong> ' . ( $birth_year ?: __( '(empty)', 'islamic-scholars' ) ) . '</p>';
				echo '<p><strong>' . __( 'Death Year:', 'islamic-scholars' ) . '</strong> ' . ( $death_year ?: __( '(empty)', 'islamic-scholars' ) ) . '</p>';
				echo '<p><strong>' . __( 'Full Name:', 'islamic-scholars' ) . '</strong> ' . ( $full_name ?: __( '(empty)', 'islamic-scholars' ) ) . '</p>';
				echo '<p><strong>' . __( 'Kunyah:', 'islamic-scholars' ) . '</strong> ' . ( $kunyah ?: __( '(empty)', 'islamic-scholars' ) ) . '</p>';
				
				if ( is_array( $teachers ) ) {
					echo '<p><strong>' . __( 'Teachers:', 'islamic-scholars' ) . '</strong> ' . count( $teachers ) . ' (array)</p>';
					foreach ( $teachers as $teacher_id ) {
						echo '<p style="margin-left: 20px;">→ ' . sprintf( __( 'Teacher ID: %s = %s', 'islamic-scholars' ), $teacher_id, get_the_title( $teacher_id ) ) . '</p>';
					}
				} else {
					echo '<p><strong>' . __( 'Teachers:', 'islamic-scholars' ) . '</strong> ' . sprintf( __( 'Not an array: %s', 'islamic-scholars' ), gettype( $teachers ) ) . '</p>';
				}
				
				// Test get_scholar_students
				if ( function_exists( 'get_scholar_students' ) ) {
					$students = get_scholar_students( $scholar_id );
					echo '<p><strong>' . __( 'Students:', 'islamic-scholars' ) . '</strong> ' . count( $students ) . '</p>';
					if ( ! empty( $students ) ) {
						foreach ( $students as $student_id ) {
							echo '<p style="margin-left: 20px;">← ' . sprintf( __( 'Student ID: %s = %s', 'islamic-scholars' ), $student_id, get_the_title( $student_id ) ) . '</p>';
						}
					}
				}
				
				// Test centuries
				$centuries = wp_get_post_terms( $scholar_id, 'centuries' );
				echo '<p><strong>' . __( 'Centuries:', 'islamic-scholars' ) . '</strong> ';
				if ( is_wp_error( $centuries ) ) {
					echo '<span style="color: red;">' . __( 'ERROR', 'islamic-scholars' ) . ' - ' . $centuries->get_error_message() . '</span>';
				} elseif ( empty( $centuries ) ) {
					echo '<span style="color: orange;">' . __( 'No centuries assigned', 'islamic-scholars' ) . '</span>';
				} else {
					echo implode( ', ', wp_list_pluck( $centuries, 'name' ) );
				}
				echo '</p>';
				
				echo '<hr style="margin: 20px 0;">';
			}
			
			// Global debug info (same as before)
			?>
			<h3><?php _e( '1. Scholar CPT Registration', 'islamic-scholars' ); ?></h3>
			<?php
			$post_type = get_post_type_object( 'scholar' );
			if ( $post_type ) {
				echo '<p style="color: green;">✓ ' . __( 'Scholar CPT is registered', 'islamic-scholars' ) . '</p>';
			} else {
				echo '<p style="color: red;">✗ ' . __( 'Scholar CPT NOT registered', 'islamic-scholars' ) . '</p>';
			}
			
			echo '<h3>' . __( '2. Centuries Taxonomy', 'islamic-scholars' ) . '</h3>';
			$tax = get_taxonomy( 'centuries' );
			if ( $tax ) {
				echo '<p style="color: green;">✓ ' . __( 'Centuries taxonomy is registered', 'islamic-scholars' ) . '</p>';
				echo '<p>' . sprintf( __( 'Applied to: %s', 'islamic-scholars' ), implode( ', ', $tax->object_type ) ) . '</p>';
			} else {
				echo '<p style="color: red;">✗ ' . __( 'Centuries taxonomy NOT registered', 'islamic-scholars' ) . '</p>';
			}
			
			echo '<h3>' . __( '3. Scholars in Database', 'islamic-scholars' ) . '</h3>';
			$scholars = get_posts( array(
				'post_type' => 'scholar',
				'posts_per_page' => -1,
				'post_status' => 'any',
			) );
			echo '<p>' . sprintf( __( 'Total scholars: %s', 'islamic-scholars' ), '<strong>' . count( $scholars ) . '</strong>' ) . '</p>';
			
			if ( ! empty( $scholars ) ) {
				echo '<table style="width: 100%; border-collapse: collapse; margin: 10px 0; background: white;">';
				echo '<tr style="background: #ddd;">';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'ID', 'islamic-scholars' ) . '</th>';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'Title', 'islamic-scholars' ) . '</th>';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'Status', 'islamic-scholars' ) . '</th>';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'Birth-Death', 'islamic-scholars' ) . '</th>';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'Century', 'islamic-scholars' ) . '</th>';
				echo '<th style="padding: 5px; border: 1px solid #999;">' . __( 'Link', 'islamic-scholars' ) . '</th>';
				echo '</tr>';
				
				foreach ( array_slice( $scholars, 0, 10 ) as $scholar ) {
					$birth = get_post_meta( $scholar->ID, 'birth_year', true );
					$death = get_post_meta( $scholar->ID, 'death_year', true );
					$centuries = wp_get_post_terms( $scholar->ID, 'centuries' );
					$century_name = ( ! is_wp_error( $centuries ) && ! empty( $centuries ) ) ? $centuries[0]->name : __( '(none)', 'islamic-scholars' );
					$url = get_permalink( $scholar->ID );
					$status = $scholar->post_status;
					
					echo '<tr style="border-bottom: 1px solid #ddd;">';
					echo '<td style="padding: 5px; border: 1px solid #ddd;">' . $scholar->ID . '</td>';
					echo '<td style="padding: 5px; border: 1px solid #ddd;">' . $scholar->post_title . '</td>';
					echo '<td style="padding: 5px; border: 1px solid #ddd;">' . $status . '</td>';
					echo '<td style="padding: 5px; border: 1px solid #ddd;">' . $birth . '–' . $death . ' AH</td>';
					echo '<td style="padding: 5px; border: 1px solid #ddd;">' . $century_name . '</td>';
					echo '<td style="padding: 5px; border: 1px solid #ddd;"><a href="' . esc_url( $url ) . '" target="_blank">' . __( 'View', 'islamic-scholars' ) . '</a></td>';
					echo '</tr>';
				}
				
				echo '</table>';
			}
			
			echo '<h3>' . __( '4. Rewrite Rules', 'islamic-scholars' ) . '</h3>';
			global $wp_rewrite;
			$rules = $wp_rewrite->rules;
			$scholar_rules = array_filter( $rules, function( $rule ) {
				return strpos( $rule, 'scholar' ) !== false;
			}, ARRAY_FILTER_USE_KEY );
			
			if ( ! empty( $scholar_rules ) ) {
				echo '<p style="color: green;">✓ ' . sprintf( __( 'Scholar rewrite rules found (%s rules)', 'islamic-scholars' ), count( $scholar_rules ) ) . '</p>';
			} else {
				echo '<p style="color: red;">✗ ' . __( 'NO scholar rewrite rules found', 'islamic-scholars' ) . '</p>';
				echo '<p><strong>' . __( 'FIX:', 'islamic-scholars' ) . '</strong> ' . __( 'Go to Settings → Permalinks → Save Changes', 'islamic-scholars' ) . '</p>';
			}
			?>
		</div>
		<?php
	}, 999 );
}
add_action( 'wp', 'islamic_scholars_debug_page' );

