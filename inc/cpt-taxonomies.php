<?php
/**
 * Register Custom Post Types and Taxonomies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register Scholar CPT
 */
function islamic_scholars_register_scholar_cpt() {
	register_post_type( 'scholar', array(
		'labels' => array(
			'name' => __( 'Scholars', 'islamic-scholars' ),
			'singular_name' => __( 'Scholar', 'islamic-scholars' ),
			'add_new' => __( 'Add New Scholar', 'islamic-scholars' ),
			'add_new_item' => __( 'Add New Scholar', 'islamic-scholars' ),
			'edit_item' => __( 'Edit Scholar', 'islamic-scholars' ),
			'new_item' => __( 'New Scholar', 'islamic-scholars' ),
			'view_item' => __( 'View Scholar', 'islamic-scholars' ),
			'search_items' => __( 'Search Scholars', 'islamic-scholars' ),
		),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'scholar' ),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 7,
		'menu_icon' => 'dashicons-id',
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'islamic_scholars_register_scholar_cpt' );

/**
 * Register Centuries Taxonomy (only for scholar CPT)
 */
function islamic_scholars_register_centuries_tax() {
	register_taxonomy( 'centuries', array( 'scholar' ), array(
		'labels' => array(
			'name' => __( 'Centuries', 'islamic-scholars' ),
			'singular_name' => __( 'Century', 'islamic-scholars' ),
			'search_items' => __( 'Search Centuries', 'islamic-scholars' ),
			'all_items' => __( 'All Centuries', 'islamic-scholars' ),
			'parent_item' => __( 'Parent Century', 'islamic-scholars' ),
			'edit_item' => __( 'Edit Century', 'islamic-scholars' ),
			'update_item' => __( 'Update Century', 'islamic-scholars' ),
			'add_new_item' => __( 'Add New Century', 'islamic-scholars' ),
			'new_item_name' => __( 'New Century Name', 'islamic-scholars' ),
		),
		'hierarchical' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'century' ),
		'show_in_rest' => true,
		'show_ui' => true,
	) );
}
add_action( 'init', 'islamic_scholars_register_centuries_tax' );

/**
 * Flush rewrite rules on theme switch
 */
function islamic_scholars_theme_activation() {
	// Make sure CPTs and taxonomies are registered first
	islamic_scholars_register_scholar_cpt();
	islamic_scholars_register_centuries_tax();
	
	// Schedule flush on next page load if not already scheduled
	if ( ! wp_next_scheduled( 'islamic_scholars_flush_rewrites' ) ) {
		wp_schedule_single_event( time(), 'islamic_scholars_flush_rewrites' );
	}
}
add_action( 'after_switch_theme', 'islamic_scholars_theme_activation' );

/**
 * Perform rewrite flush
 */
function islamic_scholars_do_flush_rewrites() {
	flush_rewrite_rules( false );
}
add_action( 'islamic_scholars_flush_rewrites', 'islamic_scholars_do_flush_rewrites' );

/**
 * Also flush on init if needed (as a safety net)
 */
function islamic_scholars_maybe_flush_rewrites() {
	$flushed = get_option( 'islamic_scholars_rewrites_flushed' );
	$theme_version = get_option( 'islamic_scholars_theme_version' );
	
	if ( $theme_version !== THEME_VERSION || ! $flushed ) {
		flush_rewrite_rules( false );
		update_option( 'islamic_scholars_rewrites_flushed', true );
		update_option( 'islamic_scholars_theme_version', THEME_VERSION );
	}
}
add_action( 'init', 'islamic_scholars_maybe_flush_rewrites', 100 );
