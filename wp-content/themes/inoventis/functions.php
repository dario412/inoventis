<?php
/**
 * Inoventis Theme Functions
 *
 * @package Inoventis
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme version
 */
define( 'INOVENTIS_VERSION', '1.0.0' );
define( 'INOVENTIS_PATH', get_template_directory() );
define( 'INOVENTIS_URI', get_template_directory_uri() );

/**
 * Setup theme
 */
function inoventis_setup() {
	// Add theme support
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	
	// Block editor support
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'custom-units', array( 'px', 'em', 'rem', 'vh', 'vw', '%' ) );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'link-color' );
	
	// Full Site Editing support (commented out to use PHP templates)
	// add_theme_support( 'block-templates' );
	// add_theme_support( 'block-template-parts' );
	
	// Register navigation menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'inoventis' ),
		'footer'  => __( 'Footer Menu', 'inoventis' ),
	) );
}
add_action( 'after_setup_theme', 'inoventis_setup' );

/**
 * Add custom CSS classes to menu items for mega menu support
 * 
 * This allows you to add CSS classes in the WordPress menu editor
 * Classes: mega-menu, mega-menu-2cols, mega-menu-3cols, mega-menu-4cols
 */
function inoventis_add_custom_nav_classes( $classes, $item, $args ) {
	// Allow custom classes added via menu editor
	if ( isset( $item->classes ) && is_array( $item->classes ) ) {
		// Check for mega menu classes
		foreach ( $item->classes as $class ) {
			if ( strpos( $class, 'mega-menu' ) !== false ) {
				$classes[] = $class;
			}
		}
	}
	
	return $classes;
}
add_filter( 'nav_menu_css_class', 'inoventis_add_custom_nav_classes', 10, 3 );

/**
 * Enqueue theme styles and scripts
 */
function inoventis_enqueue_assets() {
	// Enqueue theme stylesheet (load first)
	wp_enqueue_style(
		'inoventis-style',
		get_stylesheet_uri(),
		array(),
		INOVENTIS_VERSION . '.' . filemtime( get_stylesheet_directory() . '/style.css' )
	);
	
	// Enqueue navigation styles
	$navigation_css = INOVENTIS_PATH . '/assets/css/navigation.css';
	if ( file_exists( $navigation_css ) ) {
		wp_enqueue_style(
			'inoventis-navigation',
			INOVENTIS_URI . '/assets/css/navigation.css',
			array(),
			filemtime( $navigation_css )
		);
	} else {
		// Fallback: If file doesn't exist, log for debugging
		error_log( 'Navigation CSS file not found: ' . $navigation_css );
	}
	
	// Enqueue navigation script
	$navigation_js = INOVENTIS_PATH . '/assets/js/navigation.js';
	if ( file_exists( $navigation_js ) ) {
		wp_enqueue_script(
			'inoventis-navigation',
			INOVENTIS_URI . '/assets/js/navigation.js',
			array(),
			filemtime( $navigation_js ),
			true
		);
	}
	
	// Enqueue custom blocks frontend styles if they exist
	$frontend_style = INOVENTIS_PATH . '/build/style-index.css';
	if ( file_exists( $frontend_style ) ) {
		wp_enqueue_style(
			'inoventis-blocks-style',
			INOVENTIS_URI . '/build/style-index.css',
			array(),
			filemtime( $frontend_style )
		);
	}
	
	// Enqueue product blocks accordion script on product pages
	if ( is_singular( 'product' ) ) {
		wp_add_inline_script(
			'inoventis-navigation',
			"
			document.addEventListener('DOMContentLoaded', function() {
				const accordionToggles = document.querySelectorAll('.product-characteristics-block .characteristic-toggle');
				
				accordionToggles.forEach(toggle => {
					toggle.addEventListener('click', function() {
						const isExpanded = this.getAttribute('aria-expanded') === 'true';
						const content = this.nextElementSibling;
						
						// Close all other items (optional - remove this if you want multiple open)
						accordionToggles.forEach(otherToggle => {
							if (otherToggle !== this) {
								otherToggle.setAttribute('aria-expanded', 'false');
								otherToggle.nextElementSibling.style.maxHeight = '0';
								otherToggle.nextElementSibling.style.padding = '0 1.5rem';
							}
						});
						
						// Toggle current item
						this.setAttribute('aria-expanded', !isExpanded);
						if (!isExpanded) {
							content.style.maxHeight = content.scrollHeight + 'px';
							content.style.padding = '1.5rem';
						} else {
							content.style.maxHeight = '0';
							content.style.padding = '0 1.5rem';
						}
					});
				});
			});
			",
			'after'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'inoventis_enqueue_assets' );

/**
 * Register custom blocks
 */
function inoventis_register_blocks() {
	// Get all block directories
	$blocks_dir = INOVENTIS_PATH . '/blocks';
	
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}
	
	// Scan blocks directory
	$blocks = array_filter( glob( $blocks_dir . '/*' ), 'is_dir' );
	
	foreach ( $blocks as $block_path ) {
		$block_name = basename( $block_path );
		$block_json = $block_path . '/block.json';
		
		// Register block if block.json exists
		if ( file_exists( $block_json ) ) {
			register_block_type( $block_json );
		}
	}
}
add_action( 'init', 'inoventis_register_blocks' );

/**
 * Register Product Custom Field Blocks
 */
function inoventis_register_product_blocks() {
	// Only register for product post type
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	// Register meta fields for block editor
	register_post_meta( 'product', '_product_short_description', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_specifications', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'title' => array(
							'type' => 'string',
						),
						'value' => array(
							'type' => 'string',
						),
					),
				),
			),
		),
		'single'         => true,
		'type'           => 'array',
		'default'        => array(),
		'sanitize_callback' => function( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}
			$sanitized = array();
			foreach ( $value as $item ) {
				if ( is_array( $item ) ) {
					$sanitized[] = array(
						'title' => sanitize_text_field( $item['title'] ?? '' ),
						'value' => sanitize_text_field( $item['value'] ?? '' ),
					);
				}
			}
			return $sanitized;
		},
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_characteristics', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'title' => array(
							'type' => 'string',
						),
						'text'  => array(
							'type' => 'string',
						),
					),
				),
			),
		),
		'single'         => true,
		'type'           => 'array',
		'default'        => array(),
		'sanitize_callback' => function( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}
			$sanitized = array();
			foreach ( $value as $item ) {
				if ( is_array( $item ) ) {
					$sanitized[] = array(
						'title' => sanitize_text_field( $item['title'] ?? '' ),
						'text'  => sanitize_textarea_field( $item['text'] ?? '' ),
					);
				}
			}
			return $sanitized;
		},
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_detailed_images', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'image_id' => array(
							'type' => 'integer',
						),
						'text'     => array(
							'type' => 'string',
						),
					),
				),
			),
		),
		'single'         => true,
		'type'           => 'array',
		'default'        => array(),
		'sanitize_callback' => function( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}
			$sanitized = array();
			foreach ( $value as $item ) {
				if ( is_array( $item ) ) {
					$sanitized[] = array(
						'image_id' => absint( $item['image_id'] ?? 0 ),
						'text'     => sanitize_text_field( $item['text'] ?? '' ),
					);
				}
			}
			return $sanitized;
		},
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_gallery', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type' => 'integer',
				),
			),
		),
		'single'         => true,
		'type'           => 'array',
		'default'        => array(),
		'sanitize_callback' => function( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}
			return array_filter( array_map( 'absint', $value ) );
		},
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_related_products_title', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
		'default'      => 'Слични производи',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	register_post_meta( 'product', '_product_related_products_ids', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type' => 'integer',
				),
			),
		),
		'single'         => true,
		'type'           => 'array',
		'default'        => array(),
		'sanitize_callback' => function( $value ) {
			if ( ! is_array( $value ) ) {
				return array();
			}
			return array_filter( array_map( 'absint', $value ) );
		},
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	) );

	// Load render.php files to define render functions
	$render_files = array(
		'product-short-description',
		'product-specifications',
		'product-characteristics',
		'product-detailed-images',
		'product-gallery',
		'product-related-products',
	);

	foreach ( $render_files as $block_name ) {
		$render_file = INOVENTIS_PATH . '/blocks/' . $block_name . '/render.php';
		if ( file_exists( $render_file ) ) {
			require_once $render_file;
		}
	}

	// Product Short Description Block - use block.json from blocks directory
	if ( file_exists( INOVENTIS_PATH . '/blocks/product-short-description/block.json' ) ) {
		$block_type = register_block_type( INOVENTIS_PATH . '/blocks/product-short-description/block.json' );
		if ( $block_type && function_exists( 'inoventis_render_product_short_description_block' ) ) {
			$block_type->render_callback = 'inoventis_render_product_short_description_block';
		}
	}

	// Register other product blocks from blocks directory
	$product_blocks = array(
		'product-specifications' => 'inoventis_render_product_specifications_block',
		'product-characteristics' => 'inoventis_render_product_characteristics_block',
		'product-detailed-images' => 'inoventis_render_product_detailed_images_block',
		'product-gallery' => 'inoventis_render_product_gallery_block',
		'product-related-products' => 'inoventis_render_product_related_products_block',
	);

	foreach ( $product_blocks as $block_name => $render_callback ) {
		$block_path = INOVENTIS_PATH . '/blocks/' . $block_name . '/block.json';
		if ( file_exists( $block_path ) && function_exists( $render_callback ) ) {
			$block_type = register_block_type( $block_path );
			if ( $block_type ) {
				$block_type->render_callback = $render_callback;
			}
		}
	}
}
add_action( 'init', 'inoventis_register_product_blocks', 10 );

/**
 * Enqueue the compiled block editor script
 */
function inoventis_enqueue_block_editor_assets() {
	global $post_type;
	
	// Only enqueue for product post type
	if ( $post_type !== 'product' ) {
		return;
	}
	
	$block_editor_path = INOVENTIS_PATH . '/build/index.js';
	$asset_file = INOVENTIS_PATH . '/build/index.asset.php';
	
	if ( ! file_exists( $block_editor_path ) || ! file_exists( $asset_file ) ) {
		return;
	}
	
			$asset = require $asset_file;
		
		wp_enqueue_script(
		'inoventis-block-editor',
			INOVENTIS_URI . '/build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	
	// Auto-insert product blocks script
	wp_add_inline_script(
		'inoventis-block-editor',
		"
		( function() {
			function autoInsertProductBlocks() {
				if ( typeof wp === 'undefined' || typeof wp.data === 'undefined' || typeof wp.blocks === 'undefined' ) {
					return;
				}
				
				const { select, dispatch } = wp.data;
				const { createBlock } = wp.blocks;
				
				if ( ! select || ! dispatch || ! createBlock ) {
					return;
				}
				
				try {
					const blocks = select( 'core/block-editor' )?.getBlocks() || [];
					const hasShortDesc = blocks.some( block => block.name === 'inoventis/product-short-description' );
					const hasSpecs = blocks.some( block => block.name === 'inoventis/product-specifications' );
					const hasChars = blocks.some( block => block.name === 'inoventis/product-characteristics' );
					const hasImages = blocks.some( block => block.name === 'inoventis/product-detailed-images' );
					const hasRelated = blocks.some( block => block.name === 'inoventis/product-related-products' );
					
					if ( hasShortDesc && hasSpecs && hasChars && hasImages && hasRelated ) {
						return; // All blocks already exist
					}
					
					const newBlocks = [];
					if ( ! hasShortDesc ) {
						newBlocks.push( createBlock( 'inoventis/product-short-description' ) );
					}
					if ( ! hasSpecs ) {
						newBlocks.push( createBlock( 'inoventis/product-specifications' ) );
					}
					if ( ! hasChars ) {
						newBlocks.push( createBlock( 'inoventis/product-characteristics' ) );
					}
					if ( ! hasImages ) {
						newBlocks.push( createBlock( 'inoventis/product-detailed-images' ) );
					}
					if ( ! hasRelated ) {
						newBlocks.push( createBlock( 'inoventis/product-related-products' ) );
					}
					
					if ( newBlocks.length > 0 && dispatch( 'core/block-editor' ) ) {
						dispatch( 'core/block-editor' ).insertBlocks( newBlocks, 0 );
					}
				} catch ( e ) {
					console.error( 'Error auto-inserting product blocks:', e );
				}
			}
			
			// Wait for WordPress to be ready
			if ( document.readyState === 'loading' ) {
				document.addEventListener( 'DOMContentLoaded', function() {
					setTimeout( autoInsertProductBlocks, 2000 );
				} );
			} else {
				setTimeout( autoInsertProductBlocks, 2000 );
			}
			
			// Also listen for editor ready
			if ( typeof wp !== 'undefined' && wp.data && wp.data.subscribe ) {
				wp.data.subscribe( function() {
					setTimeout( autoInsertProductBlocks, 3000 );
				} );
			}
		} )();
		",
		'after'
	);
}
add_action( 'enqueue_block_editor_assets', 'inoventis_enqueue_block_editor_assets' );

/**
 * Register custom block category for product blocks
 */
function inoventis_register_product_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'inoventis-product',
				'title' => __( 'Product Fields', 'inoventis' ),
				'icon'  => 'products',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'inoventis_register_product_block_category', 10, 1 );

/**
 * Register Products Custom Post Type
 */
function inoventis_register_products_post_type() {
	$labels = array(
		'name'                  => _x( 'Products', 'Post type general name', 'inoventis' ),
		'singular_name'         => _x( 'Product', 'Post type singular name', 'inoventis' ),
		'menu_name'             => _x( 'Products', 'Admin Menu text', 'inoventis' ),
		'name_admin_bar'        => _x( 'Product', 'Add New on Toolbar', 'inoventis' ),
		'add_new'               => __( 'Add New', 'inoventis' ),
		'add_new_item'          => __( 'Add New Product', 'inoventis' ),
		'new_item'              => __( 'New Product', 'inoventis' ),
		'edit_item'             => __( 'Edit Product', 'inoventis' ),
		'view_item'             => __( 'View Product', 'inoventis' ),
		'all_items'             => __( 'All Products', 'inoventis' ),
		'search_items'          => __( 'Search Products', 'inoventis' ),
		'parent_item_colon'     => __( 'Parent Products:', 'inoventis' ),
		'not_found'             => __( 'No products found.', 'inoventis' ),
		'not_found_in_trash'    => __( 'No products found in Trash.', 'inoventis' ),
		'featured_image'        => _x( 'Product Featured Image', 'Overrides the "Featured Image" phrase', 'inoventis' ),
		'set_featured_image'    => _x( 'Set product featured image', 'Overrides the "Set featured image" phrase', 'inoventis' ),
		'remove_featured_image' => _x( 'Remove product featured image', 'Overrides the "Remove featured image" phrase', 'inoventis' ),
		'use_featured_image'    => _x( 'Use as product featured image', 'Overrides the "Use as featured image" phrase', 'inoventis' ),
		'archives'              => _x( 'Product archives', 'The post type archive label used in nav menus', 'inoventis' ),
		'insert_into_item'      => _x( 'Insert into product', 'Overrides the "Insert into post"/"Insert into page" phrase', 'inoventis' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this product', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase', 'inoventis' ),
		'filter_items_list'     => _x( 'Filter products list', 'Screen reader text for the filter links', 'inoventis' ),
		'items_list_navigation' => _x( 'Products list navigation', 'Screen reader text for the pagination', 'inoventis' ),
		'items_list'            => _x( 'Products list', 'Screen reader text for the items list', 'inoventis' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'products' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'          => 'dashicons-products',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'product', $args );
}
add_action( 'init', 'inoventis_register_products_post_type', 5 );

/**
 * Register Product Category Taxonomy
 */
function inoventis_register_product_category_taxonomy() {
	$labels = array(
		'name'              => _x( 'Product Categories', 'taxonomy general name', 'inoventis' ),
		'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'inoventis' ),
		'search_items'      => __( 'Search Categories', 'inoventis' ),
		'all_items'         => __( 'All Categories', 'inoventis' ),
		'parent_item'       => null,
		'parent_item_colon' => null,
		'edit_item'         => __( 'Edit Category', 'inoventis' ),
		'update_item'       => __( 'Update Category', 'inoventis' ),
		'add_new_item'      => __( 'Add New Category', 'inoventis' ),
		'new_item_name'     => __( 'New Category Name', 'inoventis' ),
		'menu_name'         => __( 'Categories', 'inoventis' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'product-category' ),
		'show_in_rest'      => true,
		'meta_box_cb'       => 'post_categories_meta_box',
	);

	register_taxonomy( 'product_category', array( 'product' ), $args );
}
add_action( 'init', 'inoventis_register_product_category_taxonomy' );

/**
 * Register Product Subcategory Taxonomy
 */
function inoventis_register_product_subcategory_taxonomy() {
	$labels = array(
		'name'              => _x( 'Product Subcategories', 'taxonomy general name', 'inoventis' ),
		'singular_name'     => _x( 'Product Subcategory', 'taxonomy singular name', 'inoventis' ),
		'search_items'      => __( 'Search Subcategories', 'inoventis' ),
		'all_items'         => __( 'All Subcategories', 'inoventis' ),
		'parent_item'       => __( 'Parent Category', 'inoventis' ),
		'parent_item_colon' => __( 'Parent Category:', 'inoventis' ),
		'edit_item'         => __( 'Edit Subcategory', 'inoventis' ),
		'update_item'       => __( 'Update Subcategory', 'inoventis' ),
		'add_new_item'      => __( 'Add New Subcategory', 'inoventis' ),
		'new_item_name'     => __( 'New Subcategory Name', 'inoventis' ),
		'menu_name'         => __( 'Subcategories', 'inoventis' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'product-subcategory' ),
		'show_in_rest'      => true,
		'meta_box_cb'       => 'post_categories_meta_box',
	);

	register_taxonomy( 'product_subcategory', array( 'product' ), $args );
}
add_action( 'init', 'inoventis_register_product_subcategory_taxonomy' );

/**
 * Add custom meta box for single category and subcategory selection
 */
function inoventis_add_product_taxonomy_meta_box() {
	remove_meta_box( 'product_categorydiv', 'product', 'side' );
	remove_meta_box( 'product_subcategorydiv', 'product', 'side' );
	add_meta_box(
		'product_taxonomies',
		__( 'Product Categories', 'inoventis' ),
		'inoventis_product_taxonomy_meta_box_callback',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'inoventis_add_product_taxonomy_meta_box' );

/**
 * Custom meta box callback for product categories
 */
function inoventis_product_taxonomy_meta_box_callback( $post ) {
	wp_nonce_field( 'inoventis_product_taxonomies', 'inoventis_product_taxonomies_nonce' );

	// Get selected terms
	$selected_category = wp_get_post_terms( $post->ID, 'product_category', array( 'fields' => 'ids' ) );
	$selected_subcategory = wp_get_post_terms( $post->ID, 'product_subcategory', array( 'fields' => 'ids' ) );

	// Get all categories
	$categories = get_terms( array(
		'taxonomy'   => 'product_category',
		'hide_empty' => false,
	) );

	$category_id = ! empty( $selected_category ) ? $selected_category[0] : '';
	$subcategory_id = ! empty( $selected_subcategory ) ? $selected_subcategory[0] : '';

	// Get parent category of selected subcategory (if subcategory is already selected)
	$parent_category_id = '';
	if ( $subcategory_id ) {
		$subcat_term = get_term( $subcategory_id, 'product_subcategory' );
		if ( $subcat_term && ! is_wp_error( $subcat_term ) ) {
			$parent_category_id = get_term_meta( $subcat_term->term_id, 'parent_product_category', true );
			// If we have a selected subcategory but no category, use the parent
			if ( ! $category_id && $parent_category_id ) {
				$category_id = $parent_category_id;
			}
		}
	}

	// Get subcategories for the selected category
	$category_subcats = array();
	if ( $category_id ) {
		$all_subcategories = get_terms( array(
			'taxonomy'   => 'product_subcategory',
			'hide_empty' => false,
		) );
		
		// Filter subcategories by parent category
		foreach ( $all_subcategories as $subcat ) {
			$parent_cat_id = get_term_meta( $subcat->term_id, 'parent_product_category', true );
			if ( $parent_cat_id == $category_id ) {
				$category_subcats[] = $subcat;
			}
		}
	}

	?>
	<div id="product-taxonomies-box">
		<p>
			<label for="product_category"><strong><?php _e( 'Category:', 'inoventis' ); ?></strong></label>
			<select name="product_category" id="product_category" style="width: 100%;">
				<option value=""><?php _e( '— Select —', 'inoventis' ); ?></option>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $category_id, $category->term_id ); ?>>
						<?php echo esc_html( $category->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="product_subcategory"><strong><?php _e( 'Subcategory:', 'inoventis' ); ?></strong></label>
			<select name="product_subcategory" id="product_subcategory" style="width: 100%;">
				<option value=""><?php _e( '— Select Category First —', 'inoventis' ); ?></option>
				<?php if ( $category_id && ! empty( $category_subcats ) ) : ?>
					<?php foreach ( $category_subcats as $subcat ) : ?>
						<option value="<?php echo esc_attr( $subcat->term_id ); ?>" <?php selected( $subcategory_id, $subcat->term_id ); ?>>
							<?php echo esc_html( $subcat->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
	</div>
	<?php
}

/**
 * Save product taxonomy selections
 */
function inoventis_save_product_taxonomies( $post_id ) {
	// Check nonce
	if ( ! isset( $_POST['inoventis_product_taxonomies_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['inoventis_product_taxonomies_nonce'], 'inoventis_product_taxonomies' ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Save category (single selection)
	if ( isset( $_POST['product_category'] ) ) {
		$category_id = intval( $_POST['product_category'] );
		if ( $category_id > 0 ) {
			wp_set_object_terms( $post_id, array( $category_id ), 'product_category' );
		} else {
			wp_set_object_terms( $post_id, array(), 'product_category' );
		}
	}

	// Save subcategory (single selection)
	if ( isset( $_POST['product_subcategory'] ) ) {
		$subcategory_id = intval( $_POST['product_subcategory'] );
		if ( $subcategory_id > 0 ) {
			// Verify that subcategory belongs to selected category
			$parent_category_id = get_term_meta( $subcategory_id, 'parent_product_category', true );
			$category_id_check = isset( $_POST['product_category'] ) ? intval( $_POST['product_category'] ) : 0;
			if ( $parent_category_id == $category_id_check ) {
				wp_set_object_terms( $post_id, array( $subcategory_id ), 'product_subcategory' );
			} else {
				// If subcategory doesn't belong to selected category, remove it
				wp_set_object_terms( $post_id, array(), 'product_subcategory' );
			}
		} else {
			wp_set_object_terms( $post_id, array(), 'product_subcategory' );
		}
	}
}
add_action( 'save_post_product', 'inoventis_save_product_taxonomies' );

/**
 * Add featured image field to product category taxonomy
 */
function inoventis_add_category_featured_image_field( $term ) {
	$image_id = get_term_meta( $term->term_id, 'category_featured_image', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	?>
	<tr class="form-field term-image-wrap">
		<th scope="row">
			<label for="category_featured_image"><?php _e( 'Featured Image', 'inoventis' ); ?></label>
		</th>
		<td>
			<div class="category-image-wrapper">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />
				<?php endif; ?>
				<input type="hidden" name="category_featured_image" id="category_featured_image" value="<?php echo esc_attr( $image_id ); ?>" />
				<button type="button" class="button button-secondary" id="upload_category_image_button">
					<?php _e( 'Upload Image', 'inoventis' ); ?>
				</button>
				<button type="button" class="button button-secondary" id="remove_category_image_button" style="<?php echo $image_id ? '' : 'display:none;'; ?>">
					<?php _e( 'Remove Image', 'inoventis' ); ?>
				</button>
			</div>
			<p class="description"><?php _e( 'Upload a featured image for this category.', 'inoventis' ); ?></p>
		</td>
	</tr>

	<tr class="form-field term-parent-category-wrap">
		<th scope="row">
			<label for="parent_product_category"><?php _e( 'Parent Category (for subcategories)', 'inoventis' ); ?></label>
		</th>
		<td>
			<?php
			$parent_category_id = get_term_meta( $term->term_id, 'parent_product_category', true );
			$categories = get_terms( array(
				'taxonomy'   => 'product_category',
				'hide_empty' => false,
			) );
			?>
			<select name="parent_product_category" id="parent_product_category" style="width: 100%;">
				<option value=""><?php _e( '— None —', 'inoventis' ); ?></option>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $parent_category_id, $category->term_id ); ?>>
						<?php echo esc_html( $category->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php _e( 'Select a parent category if this is a subcategory.', 'inoventis' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_subcategory_add_form_fields', 'inoventis_add_category_featured_image_field' );
add_action( 'product_subcategory_edit_form_fields', 'inoventis_add_category_featured_image_field' );

/**
 * Add featured image field to product category (parent) taxonomy
 */
function inoventis_add_parent_category_featured_image_field( $term ) {
	$image_id = get_term_meta( $term->term_id, 'category_featured_image', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	?>
	<tr class="form-field term-image-wrap">
		<th scope="row">
			<label for="category_featured_image"><?php _e( 'Featured Image', 'inoventis' ); ?></label>
		</th>
		<td>
			<div class="category-image-wrapper">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />
				<?php endif; ?>
				<input type="hidden" name="category_featured_image" id="category_featured_image" value="<?php echo esc_attr( $image_id ); ?>" />
				<button type="button" class="button button-secondary" id="upload_category_image_button">
					<?php _e( 'Upload Image', 'inoventis' ); ?>
				</button>
				<button type="button" class="button button-secondary" id="remove_category_image_button" style="<?php echo $image_id ? '' : 'display:none;'; ?>">
					<?php _e( 'Remove Image', 'inoventis' ); ?>
				</button>
			</div>
			<p class="description"><?php _e( 'Upload a featured image for this category.', 'inoventis' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_category_add_form_fields', 'inoventis_add_parent_category_featured_image_field' );
add_action( 'product_category_edit_form_fields', 'inoventis_add_parent_category_featured_image_field' );

/**
 * Save category featured image
 */
function inoventis_save_category_featured_image( $term_id ) {
	if ( isset( $_POST['category_featured_image'] ) ) {
		$image_id = intval( $_POST['category_featured_image'] );
		update_term_meta( $term_id, 'category_featured_image', $image_id );
	}

	if ( isset( $_POST['parent_product_category'] ) ) {
		$parent_id = intval( $_POST['parent_product_category'] );
		update_term_meta( $term_id, 'parent_product_category', $parent_id );
	}
}
add_action( 'created_product_category', 'inoventis_save_category_featured_image' );
add_action( 'edited_product_category', 'inoventis_save_category_featured_image' );
add_action( 'created_product_subcategory', 'inoventis_save_category_featured_image' );
add_action( 'edited_product_subcategory', 'inoventis_save_category_featured_image' );

/**
 * AJAX handler to get subcategories for a category
 */
function inoventis_get_subcategories_ajax() {
	check_ajax_referer( 'inoventis_product_taxonomies', 'nonce' );

	$category_id = isset( $_POST['category_id'] ) ? intval( $_POST['category_id'] ) : 0;

	if ( ! $category_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid category ID', 'inoventis' ) ) );
	}

	// Get all subcategories and filter by parent category
	$all_subcategories = get_terms( array(
		'taxonomy'   => 'product_subcategory',
		'hide_empty' => false,
	) );
	
	$subcategories = array();
	if ( ! is_wp_error( $all_subcategories ) ) {
		foreach ( $all_subcategories as $subcat ) {
			$parent_cat_id = get_term_meta( $subcat->term_id, 'parent_product_category', true );
			if ( $parent_cat_id == $category_id ) {
				$subcategories[] = $subcat;
			}
		}
	}

	$subcats_data = array();
	if ( ! is_wp_error( $subcategories ) && ! empty( $subcategories ) ) {
		foreach ( $subcategories as $subcat ) {
			$subcats_data[] = array(
				'id'   => $subcat->term_id,
				'name' => $subcat->name,
			);
		}
	}

	wp_send_json_success( array( 'subcategories' => $subcats_data ) );
}
add_action( 'wp_ajax_get_product_subcategories', 'inoventis_get_subcategories_ajax' );

/**
 * Enqueue scripts and styles for category featured image and product taxonomies
 */
function inoventis_enqueue_category_image_scripts( $hook ) {
	$screen = get_current_screen();
	
	// Handle taxonomy pages (category/subcategory edit pages)
	if ( ( $hook === 'edit-tags.php' || $hook === 'term.php' ) && $screen ) {
		if ( $screen->taxonomy === 'product_category' || $screen->taxonomy === 'product_subcategory' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'jquery' );
			
			// Add inline script for category image upload
			wp_add_inline_script( 'jquery', '
				jQuery(document).ready(function($) {
					var fileFrame;
					var button = $("#upload_category_image_button");
					var removeButton = $("#remove_category_image_button");
					var imageIdInput = $("#category_featured_image");
					var imageWrapper = $(".category-image-wrapper");

					button.on("click", function(e) {
						e.preventDefault();

						if (fileFrame) {
							fileFrame.open();
							return;
						}

						fileFrame = wp.media({
							title: "' . esc_js( __( 'Select Category Image', 'inoventis' ) ) . '",
							button: {
								text: "' . esc_js( __( 'Use this image', 'inoventis' ) ) . '"
							},
							multiple: false
						});

						fileFrame.on("select", function() {
							var attachment = fileFrame.state().get("selection").first().toJSON();
							imageIdInput.val(attachment.id);
							
							var img = imageWrapper.find("img");
							if (img.length) {
								img.attr("src", attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
							} else {
								imageWrapper.prepend("<img src=\"" + (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + "\" style=\"max-width: 150px; height: auto; display: block; margin-bottom: 10px;\" />");
							}
							
							removeButton.show();
						});

						fileFrame.open();
					});

					removeButton.on("click", function(e) {
						e.preventDefault();
						imageIdInput.val("");
						imageWrapper.find("img").remove();
						$(this).hide();
					});
				});
			' );
		}
	}

	// Handle product edit pages
	if ( ( $hook === 'post.php' || $hook === 'post-new.php' ) && $screen && $screen->post_type === 'product' ) {
		wp_enqueue_script( 'jquery' );
		
		// Add inline script for subcategory filtering
		wp_add_inline_script( 'jquery', '
			jQuery(document).ready(function($) {
				var categorySelect = $("#product_category");
				var subcategorySelect = $("#product_subcategory");

				categorySelect.on("change", function() {
					var selectedCategoryId = $(this).val();
					subcategorySelect.empty();
					subcategorySelect.append("<option value=\"\">' . esc_js( __( '— Select —', 'inoventis' ) ) . '</option>");

					if (selectedCategoryId) {
						subcategorySelect.prop("disabled", true);
						subcategorySelect.append("<option value=\"\">' . esc_js( __( 'Loading...', 'inoventis' ) ) . '</option>");

						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: {
								action: "get_product_subcategories",
								category_id: selectedCategoryId,
								nonce: "' . wp_create_nonce( 'inoventis_product_taxonomies' ) . '"
							},
							success: function(response) {
								subcategorySelect.empty();
								subcategorySelect.append("<option value=\"\">' . esc_js( __( '— Select —', 'inoventis' ) ) . '</option>");
								
								if (response.success && response.data.subcategories.length > 0) {
									$.each(response.data.subcategories, function(index, subcat) {
										subcategorySelect.append(
											$("<option></option>")
												.attr("value", subcat.id)
												.text(subcat.name)
										);
									});
								} else {
									subcategorySelect.append("<option value=\"\">' . esc_js( __( 'No subcategories available', 'inoventis' ) ) . '</option>");
								}
								
								subcategorySelect.prop("disabled", false);
							},
							error: function() {
								subcategorySelect.empty();
								subcategorySelect.append("<option value=\"\">' . esc_js( __( 'Error loading subcategories', 'inoventis' ) ) . '</option>");
								subcategorySelect.prop("disabled", false);
							}
						});
					} else {
						subcategorySelect.append("<option value=\"\">' . esc_js( __( '— Select Category First —', 'inoventis' ) ) . '</option>");
					}
				});
			});
		' );
	}
}
add_action( 'admin_enqueue_scripts', 'inoventis_enqueue_category_image_scripts' );

/**
 * Render callbacks for product blocks are defined in their respective render.php files
 * No need to define them here as block.json points to those files
 */

/**
 * Add custom meta boxes for Products
 * Hide most meta boxes in block editor (replaced by blocks), but keep gallery in sidebar
 */
function inoventis_add_product_meta_boxes() {
	// Check if we're in block editor
	$is_block_editor = false;
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen && method_exists( $screen, 'is_block_editor' ) ) {
			$is_block_editor = $screen->is_block_editor();
		}
	}
	
	// Hide most meta boxes in block editor (they're replaced by blocks)
	// But keep the gallery meta box visible in sidebar
	if ( ! $is_block_editor ) {
		add_meta_box(
			'product_short_description',
			__( 'Short Description', 'inoventis' ),
			'inoventis_product_short_description_callback',
			'product',
			'normal',
			'high'
		);

		add_meta_box(
			'product_specifications',
			__( 'Спецификации (Specifications)', 'inoventis' ),
			'inoventis_product_specifications_callback',
			'product',
			'normal',
			'default'
		);

		add_meta_box(
			'product_characteristics',
			__( 'Карактеристики (Characteristics)', 'inoventis' ),
			'inoventis_product_characteristics_callback',
			'product',
			'normal',
			'default'
		);

		add_meta_box(
			'product_detailed_images',
			__( 'Детални слики (Detailed Images)', 'inoventis' ),
			'inoventis_product_detailed_images_callback',
			'product',
			'normal',
			'default'
		);
	}

	// Always show gallery meta box in sidebar (both block and classic editor)
	add_meta_box(
		'product_gallery',
		__( 'Product Gallery', 'inoventis' ),
		'inoventis_product_gallery_callback',
		'product',
		'side',
		'default'
	);

	// Always show related products meta box in sidebar (both block and classic editor)
	add_meta_box(
		'inoventis_product_related_products',
		__( 'Related Products', 'inoventis' ),
		'inoventis_product_related_products_callback',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'inoventis_add_product_meta_boxes' );

/**
 * Short Description Meta Box Callback
 */
function inoventis_product_short_description_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$short_description = get_post_meta( $post->ID, '_product_short_description', true );
	
	wp_editor(
		$short_description,
		'product_short_description',
		array(
			'textarea_name' => 'product_short_description',
			'textarea_rows' => 10,
			'media_buttons' => true,
			'tinymce'       => true,
			'quicktags'     => true,
		)
	);
}

/**
 * Specifications Meta Box Callback
 */
function inoventis_product_specifications_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$specifications = get_post_meta( $post->ID, '_product_specifications', true );
	$specifications = is_array( $specifications ) ? $specifications : array();
	
	?>
	<div id="product-specifications-container">
		<p class="description" style="margin-bottom: 15px; color: #646970;">
			<?php _e( 'Add specification rows that will be displayed in a table format on the frontend.', 'inoventis' ); ?>
		</p>
		<div id="specifications-list">
			<?php if ( ! empty( $specifications ) ) : ?>
				<?php foreach ( $specifications as $index => $spec ) : ?>
					<div class="specification-row" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="spec-field-group">
							<p>
								<label><?php _e( 'Title:', 'inoventis' ); ?></label>
								<input type="text" name="product_specifications[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $spec['title'] ); ?>" placeholder="<?php esc_attr_e( 'Enter specification title', 'inoventis' ); ?>" />
							</p>
							<p>
								<label><?php _e( 'Value:', 'inoventis' ); ?></label>
								<input type="text" name="product_specifications[<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $spec['value'] ); ?>" placeholder="<?php esc_attr_e( 'Enter specification value', 'inoventis' ); ?>" />
							</p>
						</div>
						<div class="spec-actions">
							<button type="button" class="button remove-specification-row"><?php _e( 'Remove Row', 'inoventis' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="add-button-container">
			<button type="button" class="button button-primary" id="add-specification-row">
				<span class="dashicons dashicons-plus-alt" style="vertical-align: middle; margin-right: 5px;"></span>
				<?php _e( 'Add Specification Row', 'inoventis' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Characteristics Meta Box Callback
 */
function inoventis_product_characteristics_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$characteristics = get_post_meta( $post->ID, '_product_characteristics', true );
	$characteristics = is_array( $characteristics ) ? $characteristics : array();
	
	?>
	<div id="product-characteristics-container">
		<p class="description" style="margin-bottom: 15px; color: #646970;">
			<?php _e( 'Add characteristics that will be displayed as an accordion on the frontend. Each item can be expanded to show details.', 'inoventis' ); ?>
		</p>
		<div id="characteristics-list">
			<?php if ( ! empty( $characteristics ) ) : ?>
				<?php foreach ( $characteristics as $index => $char ) : ?>
					<div class="characteristic-row" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="char-field-group">
							<p>
								<label><strong><?php _e( 'Title:', 'inoventis' ); ?></strong></label>
								<input type="text" name="product_characteristics[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $char['title'] ); ?>" placeholder="<?php esc_attr_e( 'Enter characteristic title', 'inoventis' ); ?>" />
							</p>
							<p>
								<label><strong><?php _e( 'Description:', 'inoventis' ); ?></strong></label>
								<textarea name="product_characteristics[<?php echo esc_attr( $index ); ?>][text]" rows="4" placeholder="<?php esc_attr_e( 'Enter characteristic description', 'inoventis' ); ?>"><?php echo esc_textarea( $char['text'] ); ?></textarea>
							</p>
						</div>
						<div class="char-actions">
							<button type="button" class="button remove-characteristic-row"><?php _e( 'Remove Characteristic', 'inoventis' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="add-button-container">
			<button type="button" class="button button-primary" id="add-characteristic-row">
				<span class="dashicons dashicons-plus-alt" style="vertical-align: middle; margin-right: 5px;"></span>
				<?php _e( 'Add Characteristic', 'inoventis' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Detailed Images Meta Box Callback
 */
function inoventis_product_detailed_images_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$detailed_images = get_post_meta( $post->ID, '_product_detailed_images', true );
	$detailed_images = is_array( $detailed_images ) ? $detailed_images : array();
	
	?>
	<div id="product-detailed-images-container">
		<p class="description" style="margin-bottom: 15px; color: #646970;">
			<?php _e( 'Add detailed images with captions that will be displayed in a grid on the frontend.', 'inoventis' ); ?>
		</p>
		<div id="detailed-images-list">
			<?php if ( ! empty( $detailed_images ) ) : ?>
				<?php foreach ( $detailed_images as $index => $item ) : ?>
					<div class="detailed-image-row" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="detailed-image-field-group">
							<p>
								<label><strong><?php _e( 'Image:', 'inoventis' ); ?></strong></label>
								<div class="detailed-image-wrapper">
									<?php if ( ! empty( $item['image_id'] ) ) : ?>
										<?php $image_url = wp_get_attachment_image_url( $item['image_id'], 'thumbnail' ); ?>
										<img src="<?php echo esc_url( $image_url ); ?>" />
									<?php endif; ?>
									<input type="hidden" name="product_detailed_images[<?php echo esc_attr( $index ); ?>][image_id]" class="detailed-image-id" value="<?php echo esc_attr( $item['image_id'] ); ?>" />
									<div class="image-buttons">
										<button type="button" class="button upload-detailed-image">
											<span class="dashicons dashicons-upload" style="vertical-align: middle; margin-right: 5px;"></span>
											<?php _e( 'Upload Image', 'inoventis' ); ?>
										</button>
										<button type="button" class="button remove-detailed-image" style="<?php echo empty( $item['image_id'] ) ? 'display:none;' : ''; ?>">
											<span class="dashicons dashicons-dismiss" style="vertical-align: middle; margin-right: 5px;"></span>
											<?php _e( 'Remove Image', 'inoventis' ); ?>
										</button>
									</div>
								</div>
							</p>
							<p>
								<label><strong><?php _e( 'Caption:', 'inoventis' ); ?></strong></label>
								<input type="text" name="product_detailed_images[<?php echo esc_attr( $index ); ?>][text]" value="<?php echo esc_attr( $item['text'] ); ?>" placeholder="<?php esc_attr_e( 'Enter image caption', 'inoventis' ); ?>" />
							</p>
						</div>
						<div class="detailed-image-actions">
							<button type="button" class="button remove-detailed-image-row"><?php _e( 'Remove Item', 'inoventis' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="add-button-container">
			<button type="button" class="button button-primary" id="add-detailed-image-row">
				<span class="dashicons dashicons-plus-alt" style="vertical-align: middle; margin-right: 5px;"></span>
				<?php _e( 'Add Detailed Image', 'inoventis' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Product Gallery Meta Box Callback
 */
function inoventis_product_gallery_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$gallery_ids = get_post_meta( $post->ID, '_product_gallery', true );
	$gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : array();
	
	?>
	<div id="product-gallery-container">
		<p class="description" style="margin-bottom: 15px; color: #646970;">
			<?php _e( 'Add multiple images to create a product gallery. These images will be displayed on the product page.', 'inoventis' ); ?>
		</p>
		<div id="gallery-images-list">
			<?php if ( ! empty( $gallery_ids ) ) : ?>
				<?php foreach ( $gallery_ids as $image_id ) : ?>
					<?php if ( $image_id ) : ?>
						<?php $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' ); ?>
						<div class="gallery-image-item" data-image-id="<?php echo esc_attr( $image_id ); ?>">
							<img src="<?php echo esc_url( $image_url ); ?>" />
							<input type="hidden" name="product_gallery[]" value="<?php echo esc_attr( $image_id ); ?>" />
							<button type="button" class="button remove-gallery-image">
								<span class="dashicons dashicons-trash" style="vertical-align: middle; margin-right: 3px; font-size: 16px;"></span>
								<?php _e( 'Remove', 'inoventis' ); ?>
							</button>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<button type="button" class="button button-primary" id="add-gallery-images">
			<span class="dashicons dashicons-format-gallery" style="vertical-align: middle; margin-right: 5px;"></span>
			<?php _e( 'Add Gallery Images', 'inoventis' ); ?>
		</button>
	</div>
	<?php
}

/**
 * Related Products Meta Box Callback
 */
function inoventis_product_related_products_callback( $post ) {
	wp_nonce_field( 'inoventis_product_fields', 'inoventis_product_fields_nonce' );
	
	$selected_ids = get_post_meta( $post->ID, '_product_related_products_ids', true );
	$selected_ids = is_array( $selected_ids ) ? $selected_ids : array();
	
	// Get current product's subcategory and category
	$subcategories = wp_get_post_terms( $post->ID, 'product_subcategory', array( 'fields' => 'ids' ) );
	$categories = wp_get_post_terms( $post->ID, 'product_category', array( 'fields' => 'ids' ) );
	
	// Fetch available products from subcategory first, then category
	$tax_query = array( 'relation' => 'OR' );
	
	if ( ! empty( $subcategories ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_subcategory',
			'field' => 'term_id',
			'terms' => $subcategories,
		);
	}
	
	if ( ! empty( $categories ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_category',
			'field' => 'term_id',
			'terms' => $categories,
		);
	}
	
	$args = array(
		'post_type' => 'product',
		'post__not_in' => array( $post->ID ),
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'post_status' => 'publish',
	);
	
	if ( ! empty( $tax_query ) && count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	} elseif ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query[0];
	}
	
	$available_products = get_posts( $args );
	
	?>
	<div id="product-related-products-container">
		<p class="description" style="margin-bottom: 15px; color: #646970;">
			<?php _e( 'Select and reorder related products. Products are automatically filtered from the same subcategory or category.', 'inoventis' ); ?>
		</p>
		
		<div id="related-products-list" class="sortable-products-list">
			<?php if ( ! empty( $selected_ids ) ) : ?>
				<?php foreach ( $selected_ids as $product_id ) : 
					$product = get_post( $product_id );
					if ( ! $product ) continue;
					?>
					<div class="related-product-item-admin" data-product-id="<?php echo esc_attr( $product_id ); ?>">
						<span class="drag-handle dashicons dashicons-menu-alt" style="cursor: move; vertical-align: middle; margin-right: 8px;"></span>
						<span class="product-title"><?php echo esc_html( $product->post_title ); ?></span>
						<input type="hidden" name="product_related_products_ids[]" value="<?php echo esc_attr( $product_id ); ?>" />
						<button type="button" class="button-link remove-related-product" style="color: #b32d2e; float: right;">
							<span class="dashicons dashicons-dismiss"></span>
						</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		
		<div id="available-products-select" style="margin-top: 15px;">
			<label for="select-related-product" style="display: block; margin-bottom: 8px; font-weight: 600;">
				<?php _e( 'Add Product:', 'inoventis' ); ?>
			</label>
			<select id="select-related-product" style="width: 100%;">
				<option value=""><?php _e( '-- Select a product to add --', 'inoventis' ); ?></option>
				<?php foreach ( $available_products as $product ) : 
					$is_selected = in_array( $product->ID, $selected_ids );
					if ( $is_selected ) continue; // Skip already selected products
					?>
					<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="button" class="button button-primary" id="add-related-product" style="margin-top: 10px; width: 100%;">
				<span class="dashicons dashicons-plus-alt" style="vertical-align: middle; margin-right: 5px;"></span>
				<?php _e( 'Add Product', 'inoventis' ); ?>
			</button>
		</div>
		
		<?php if ( empty( $available_products ) ) : ?>
			<p style="color: #d63638; margin-top: 15px;">
				<?php _e( 'No products available in the same subcategory or category.', 'inoventis' ); ?>
			</p>
		<?php endif; ?>
	</div>
	
	<style>
	.sortable-products-list {
		min-height: 50px;
		max-height: 400px;
		overflow-y: auto;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 10px;
		background: #f9f9f9;
		margin-bottom: 15px;
	}
	
	.related-product-item-admin {
		padding: 10px;
		margin-bottom: 8px;
		background: #ffffff;
		border: 1px solid #ddd;
		border-radius: 4px;
		display: flex;
		align-items: center;
		cursor: move;
		position: relative;
	}
	
	.related-product-item-admin:hover {
		border-color: #0B3C53;
	}
	
	.related-product-item-admin.ui-sortable-helper {
		opacity: 0.8;
		box-shadow: 0 2px 8px rgba(0,0,0,0.2);
	}
	
	.related-product-item-admin .product-title {
		flex: 1;
		font-size: 13px;
		color: #1d2327;
	}
	
	.related-product-item-admin .remove-related-product {
		opacity: 0;
		transition: opacity 0.2s;
	}
	
	.related-product-item-admin:hover .remove-related-product {
		opacity: 1;
	}
	
	.related-product-item-admin:last-child {
		margin-bottom: 0;
	}
	</style>
	
	<script>
	jQuery(document).ready(function($) {
		// Make list sortable
		$('#related-products-list').sortable({
			handle: '.drag-handle',
			placeholder: 'related-product-placeholder',
			axis: 'y',
			tolerance: 'pointer',
			opacity: 0.8,
			cursor: 'move',
			start: function(e, ui) {
				ui.placeholder.height(ui.item.height());
				ui.placeholder.css('background', '#e5e5e5');
				ui.placeholder.css('border', '1px dashed #999');
				ui.placeholder.css('border-radius', '4px');
			}
		});
		
		// Add product
		$('#add-related-product').on('click', function() {
			const select = $('#select-related-product');
			const productId = select.val();
			const productTitle = select.find('option:selected').text();
			
			if (!productId) {
				alert('<?php echo esc_js( __( 'Please select a product first.', 'inoventis' ) ); ?>');
				return;
			}
			
			// Check if already added
			if ($('#related-products-list').find('[data-product-id="' + productId + '"]').length > 0) {
				alert('<?php echo esc_js( __( 'This product is already added.', 'inoventis' ) ); ?>');
				return;
			}
			
			const itemHtml = '<div class="related-product-item-admin" data-product-id="' + productId + '">' +
				'<span class="drag-handle dashicons dashicons-menu-alt" style="cursor: move; vertical-align: middle; margin-right: 8px;"></span>' +
				'<span class="product-title">' + productTitle + '</span>' +
				'<input type="hidden" name="product_related_products_ids[]" value="' + productId + '" />' +
				'<button type="button" class="button-link remove-related-product" style="color: #b32d2e; float: right;">' +
				'<span class="dashicons dashicons-dismiss"></span></button>' +
				'</div>';
			
			$('#related-products-list').append(itemHtml);
			select.find('option[value="' + productId + '"]').remove();
			select.val('');
			
			// Reinitialize sortable
			$('#related-products-list').sortable('refresh');
		});
		
		// Remove product
		$(document).on('click', '.remove-related-product', function() {
			const item = $(this).closest('.related-product-item-admin');
			const productId = item.data('product-id');
			const productTitle = item.find('.product-title').text();
			
			if (confirm('<?php echo esc_js( __( 'Remove this product from related products?', 'inoventis' ) ); ?>')) {
				item.remove();
				// Add back to select
				$('#select-related-product').append('<option value="' + productId + '">' + productTitle + '</option>');
				$('#select-related-product').val('');
			}
		});
	});
	</script>
	<?php
}

/**
 * Save Product Custom Fields
 */
function inoventis_save_product_custom_fields( $post_id ) {
	// Skip REST API saves - meta is handled by REST API when using block editor
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	// Check nonce
	if ( ! isset( $_POST['inoventis_product_fields_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['inoventis_product_fields_nonce'], 'inoventis_product_fields' ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Check post type
	if ( get_post_type( $post_id ) !== 'product' ) {
		return;
	}

	// Save Short Description (only for classic editor)
	if ( isset( $_POST['product_short_description'] ) ) {
		update_post_meta( $post_id, '_product_short_description', wp_kses_post( $_POST['product_short_description'] ) );
	}

	// Save Specifications (only for classic editor)
	if ( isset( $_POST['product_specifications'] ) && is_array( $_POST['product_specifications'] ) ) {
		$specifications = array();
		foreach ( $_POST['product_specifications'] as $spec ) {
			if ( ! empty( $spec['title'] ) || ! empty( $spec['value'] ) ) {
				$specifications[] = array(
					'title' => sanitize_text_field( $spec['title'] ),
					'value' => sanitize_text_field( $spec['value'] ),
				);
			}
		}
		update_post_meta( $post_id, '_product_specifications', $specifications );
	} else if ( isset( $_POST['product_specifications'] ) ) {
		// Only delete if explicitly set to empty in classic editor
		delete_post_meta( $post_id, '_product_specifications' );
	}

	// Save Characteristics (only for classic editor)
	if ( isset( $_POST['product_characteristics'] ) && is_array( $_POST['product_characteristics'] ) ) {
		$characteristics = array();
		foreach ( $_POST['product_characteristics'] as $char ) {
			if ( ! empty( $char['title'] ) || ! empty( $char['text'] ) ) {
				$characteristics[] = array(
					'title' => sanitize_text_field( $char['title'] ),
					'text'  => sanitize_textarea_field( $char['text'] ),
				);
			}
		}
		update_post_meta( $post_id, '_product_characteristics', $characteristics );
	} else if ( isset( $_POST['product_characteristics'] ) ) {
		// Only delete if explicitly set to empty in classic editor
		delete_post_meta( $post_id, '_product_characteristics' );
	}

	// Save Detailed Images (only for classic editor)
	if ( isset( $_POST['product_detailed_images'] ) && is_array( $_POST['product_detailed_images'] ) ) {
		$detailed_images = array();
		foreach ( $_POST['product_detailed_images'] as $item ) {
			if ( ! empty( $item['image_id'] ) || ! empty( $item['text'] ) ) {
				$detailed_images[] = array(
					'image_id' => intval( $item['image_id'] ),
					'text'     => sanitize_text_field( $item['text'] ),
				);
			}
		}
		update_post_meta( $post_id, '_product_detailed_images', $detailed_images );
	} else if ( isset( $_POST['product_detailed_images'] ) ) {
		// Only delete if explicitly set to empty in classic editor
		delete_post_meta( $post_id, '_product_detailed_images' );
	}

	// Save Product Gallery (only for classic editor)
	if ( isset( $_POST['product_gallery'] ) && is_array( $_POST['product_gallery'] ) ) {
		$gallery_ids = array_map( 'intval', $_POST['product_gallery'] );
		$gallery_ids = array_filter( $gallery_ids );
		update_post_meta( $post_id, '_product_gallery', $gallery_ids );
	} else if ( isset( $_POST['product_gallery'] ) ) {
		// Only delete if explicitly set to empty in classic editor
		delete_post_meta( $post_id, '_product_gallery' );
	}

	// Save Related Products IDs
	if ( isset( $_POST['product_related_products_ids'] ) && is_array( $_POST['product_related_products_ids'] ) ) {
		$related_ids = array_map( 'intval', $_POST['product_related_products_ids'] );
		$related_ids = array_filter( $related_ids );
		update_post_meta( $post_id, '_product_related_products_ids', $related_ids );
	} else {
		// If not set, clear it (allows auto-fetching in render)
		delete_post_meta( $post_id, '_product_related_products_ids' );
	}
}
add_action( 'save_post_product', 'inoventis_save_product_custom_fields' );

/**
 * Enqueue scripts for product custom fields
 */
function inoventis_enqueue_product_fields_scripts( $hook ) {
	global $post_type;

	if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
		return;
	}

	if ( $post_type !== 'product' ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-sortable' );

	// Calculate indices for repeatable fields
	global $post;
	$specifications = get_post_meta( $post->ID, '_product_specifications', true );
	$characteristics = get_post_meta( $post->ID, '_product_characteristics', true );
	$detailed_images = get_post_meta( $post->ID, '_product_detailed_images', true );
	
	$spec_index = is_array( $specifications ) ? count( $specifications ) : 0;
	$char_index = is_array( $characteristics ) ? count( $characteristics ) : 0;
	$image_index = is_array( $detailed_images ) ? count( $detailed_images ) : 0;

	// Add admin styles
	wp_add_inline_style( 'wp-admin', '
		/* Container Styles */
		#product-specifications-container,
		#product-characteristics-container,
		#product-detailed-images-container {
			margin-top: 15px;
		}
		
		/* List Containers */
		#specifications-list,
		#characteristics-list,
		#detailed-images-list {
			margin-bottom: 20px;
		}
		
		/* Row Styles - Common */
		.specification-row,
		.characteristic-row,
		.detailed-image-row {
			margin-bottom: 20px;
			padding: 20px;
			background: #fff;
			border: 2px solid #e5e5e5;
			border-radius: 8px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.05);
			position: relative;
			transition: all 0.2s ease;
		}
		
		.specification-row:hover,
		.characteristic-row:hover,
		.detailed-image-row:hover {
			border-color: #2271b1;
			box-shadow: 0 2px 6px rgba(0,0,0,0.1);
		}
		
		/* Row Headers */
		.specification-row::before,
		.characteristic-row::before,
		.detailed-image-row::before {
			content: "📋";
			position: absolute;
			top: 15px;
			right: 15px;
			font-size: 18px;
			opacity: 0.3;
		}
		
		.characteristic-row::before {
			content: "📝";
		}
		
		.detailed-image-row::before {
			content: "🖼️";
		}
		
		/* Field Labels */
		.specification-row label,
		.characteristic-row label,
		.detailed-image-row label {
			display: block;
			font-weight: 600;
			margin-bottom: 8px;
			color: #1d2327;
			font-size: 13px;
		}
		
		/* Input Fields */
		.specification-row input[type="text"],
		.characteristic-row input[type="text"],
		.detailed-image-row input[type="text"] {
			padding: 8px 12px;
			border: 1px solid #8c8f94;
			border-radius: 4px;
			font-size: 14px;
			transition: border-color 0.2s;
		}
		
		.specification-row input[type="text"]:focus,
		.characteristic-row input[type="text"]:focus,
		.detailed-image-row input[type="text"]:focus {
			border-color: #2271b1;
			box-shadow: 0 0 0 1px #2271b1;
			outline: none;
		}
		
		/* Textareas */
		.characteristic-row textarea {
			padding: 8px 12px;
			border: 1px solid #8c8f94;
			border-radius: 4px;
			font-size: 14px;
			font-family: inherit;
			resize: vertical;
			transition: border-color 0.2s;
			width: 100%;
			min-height: 80px;
		}
		
		.characteristic-row textarea:focus {
			border-color: #2271b1;
			box-shadow: 0 0 0 1px #2271b1;
			outline: none;
		}
		
		/* Input width fix */
		.specification-row input[type="text"],
		.characteristic-row input[type="text"],
		.detailed-image-row input[type="text"] {
			width: 100%;
		}
		
		/* Field Groups */
		.spec-field-group,
		.char-field-group,
		.detailed-image-field-group {
			margin-bottom: 15px;
		}
		
		.specification-row p,
		.characteristic-row p,
		.detailed-image-row p {
			margin-bottom: 15px;
		}
		
		.specification-row p:last-child,
		.characteristic-row p:last-child,
		.detailed-image-row p:last-child {
			margin-bottom: 0;
		}
		
		/* Action Containers */
		.spec-actions,
		.char-actions,
		.detailed-image-actions {
			margin-top: 15px;
			padding-top: 15px;
			border-top: 1px solid #e5e5e5;
		}
		
		.add-button-container {
			margin-top: 10px;
		}
		
		.image-buttons {
			display: flex;
			gap: 8px;
			flex-wrap: wrap;
		}
		
		/* Remove Buttons */
		.remove-specification-row,
		.remove-characteristic-row,
		.remove-detailed-image-row {
			background: #dc3232;
			color: #fff;
			border-color: #dc3232;
			margin-top: 10px;
		}
		
		.remove-specification-row:hover,
		.remove-characteristic-row:hover,
		.remove-detailed-image-row:hover {
			background: #b52727;
			border-color: #b52727;
			color: #fff;
		}
		
		/* HR Separators */
		.specification-row hr,
		.characteristic-row hr,
		.detailed-image-row hr {
			margin: 15px 0 0 0;
			border: none;
			border-top: 1px solid #e5e5e5;
		}
		
		/* Add Buttons */
		#add-specification-row,
		#add-characteristic-row,
		#add-detailed-image-row {
			padding: 10px 20px;
			font-size: 14px;
			height: auto;
			line-height: 1.5;
		}
		
		/* Detailed Images Specific */
		.detailed-image-wrapper {
			margin-bottom: 15px;
			padding: 15px;
			background: #f6f7f7;
			border: 1px dashed #c3c4c7;
			border-radius: 4px;
		}
		
		.detailed-image-wrapper img {
			display: block;
			margin-bottom: 12px;
			max-width: 200px;
			height: auto;
			border: 2px solid #fff;
			border-radius: 4px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		
		.upload-detailed-image,
		.remove-detailed-image {
			margin-right: 8px;
		}
		
		/* Product Gallery */
		#product-gallery-container {
			margin-top: 10px;
		}
		
		#gallery-images-list {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
			margin-bottom: 20px;
			padding: 15px;
			background: #f6f7f7;
			border: 1px dashed #c3c4c7;
			border-radius: 4px;
			min-height: 80px;
		}
		
		#gallery-images-list:empty::before {
			content: "No images in gallery. Click \'Add Gallery Images\' to add images.";
			display: block;
			color: #646970;
			font-style: italic;
			width: 100%;
			padding: 20px;
			text-align: center;
		}
		
		.gallery-image-item {
			position: relative;
			background: #fff;
			border: 2px solid #e5e5e5;
			border-radius: 6px;
			padding: 10px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.05);
			transition: all 0.2s ease;
		}
		
		.gallery-image-item:hover {
			border-color: #2271b1;
			box-shadow: 0 2px 6px rgba(0,0,0,0.1);
		}
		
		.gallery-image-item img {
			display: block;
			width: 120px;
			height: 120px;
			object-fit: cover;
			border-radius: 4px;
			margin-bottom: 8px;
		}
		
		.gallery-image-item .remove-gallery-image {
			width: 100%;
			background: #dc3232;
			color: #fff;
			border-color: #dc3232;
			font-size: 12px;
			padding: 6px 12px;
		}
		
		.gallery-image-item .remove-gallery-image:hover {
			background: #b52727;
			border-color: #b52727;
			color: #fff;
		}
		
		#add-gallery-images {
			width: 100%;
			padding: 12px 20px;
			font-size: 14px;
			height: auto;
		}
		
		/* Empty State Messages */
		#specifications-list:empty::after,
		#characteristics-list:empty::after,
		#detailed-images-list:empty::after {
			content: "No items added yet. Click the button below to add your first item.";
			display: block;
			padding: 30px;
			text-align: center;
			color: #646970;
			font-style: italic;
			background: #f6f7f7;
			border: 1px dashed #c3c4c7;
			border-radius: 4px;
			margin-bottom: 15px;
		}
	' );

	wp_add_inline_script( 'jquery', '
		jQuery(document).ready(function($) {
			var specIndex = ' . intval( $spec_index ) . ';
			var charIndex = ' . intval( $char_index ) . ';
			var imageIndex = ' . intval( $image_index ) . ';

			// Specifications
			$("#add-specification-row").on("click", function() {
				var row = $("<div class=\"specification-row\" data-index=\"" + specIndex + "\">" +
					"<div class=\"spec-field-group\">" +
					"<p><label>' . esc_js( __( 'Title:', 'inoventis' ) ) . '</label>" +
					"<input type=\"text\" name=\"product_specifications[" + specIndex + "][title]\" placeholder=\"' . esc_js( __( 'Enter specification title', 'inoventis' ) ) . '\" /></p>" +
					"<p><label>' . esc_js( __( 'Value:', 'inoventis' ) ) . '</label>" +
					"<input type=\"text\" name=\"product_specifications[" + specIndex + "][value]\" placeholder=\"' . esc_js( __( 'Enter specification value', 'inoventis' ) ) . '\" /></p>" +
					"</div>" +
					"<div class=\"spec-actions\">" +
					"<button type=\"button\" class=\"button remove-specification-row\">' . esc_js( __( 'Remove Row', 'inoventis' ) ) . '</button>" +
					"</div>" +
					"</div>");
				$("#specifications-list").append(row);
				specIndex++;
			});

			$(document).on("click", ".remove-specification-row", function() {
				$(this).closest(".specification-row").remove();
			});

			// Characteristics
			$("#add-characteristic-row").on("click", function() {
				var row = $("<div class=\"characteristic-row\" data-index=\"" + charIndex + "\">" +
					"<div class=\"char-field-group\">" +
					"<p><label><strong>' . esc_js( __( 'Title:', 'inoventis' ) ) . '</strong></label>" +
					"<input type=\"text\" name=\"product_characteristics[" + charIndex + "][title]\" placeholder=\"' . esc_js( __( 'Enter characteristic title', 'inoventis' ) ) . '\" /></p>" +
					"<p><label><strong>' . esc_js( __( 'Description:', 'inoventis' ) ) . '</strong></label>" +
					"<textarea name=\"product_characteristics[" + charIndex + "][text]\" rows=\"4\" placeholder=\"' . esc_js( __( 'Enter characteristic description', 'inoventis' ) ) . '\"></textarea></p>" +
					"</div>" +
					"<div class=\"char-actions\">" +
					"<button type=\"button\" class=\"button remove-characteristic-row\">' . esc_js( __( 'Remove Characteristic', 'inoventis' ) ) . '</button>" +
					"</div>" +
					"</div>");
				$("#characteristics-list").append(row);
				charIndex++;
			});

			$(document).on("click", ".remove-characteristic-row", function() {
				$(this).closest(".characteristic-row").remove();
			});

			// Detailed Images
			$("#add-detailed-image-row").on("click", function() {
				var row = $("<div class=\"detailed-image-row\" data-index=\"" + imageIndex + "\">" +
					"<div class=\"detailed-image-field-group\">" +
					"<p><label><strong>' . esc_js( __( 'Image:', 'inoventis' ) ) . '</strong></label>" +
					"<div class=\"detailed-image-wrapper\">" +
					"<input type=\"hidden\" name=\"product_detailed_images[" + imageIndex + "][image_id]\" class=\"detailed-image-id\" value=\"\" />" +
					"<div class=\"image-buttons\">" +
					"<button type=\"button\" class=\"button upload-detailed-image\">" +
					"<span class=\"dashicons dashicons-upload\" style=\"vertical-align: middle; margin-right: 5px;\"></span>" +
					"' . esc_js( __( 'Upload Image', 'inoventis' ) ) . '" +
					"</button>" +
					"<button type=\"button\" class=\"button remove-detailed-image\" style=\"display:none;\">" +
					"<span class=\"dashicons dashicons-dismiss\" style=\"vertical-align: middle; margin-right: 5px;\"></span>" +
					"' . esc_js( __( 'Remove Image', 'inoventis' ) ) . '" +
					"</button>" +
					"</div>" +
					"</div></p>" +
					"<p><label><strong>' . esc_js( __( 'Caption:', 'inoventis' ) ) . '</strong></label>" +
					"<input type=\"text\" name=\"product_detailed_images[" + imageIndex + "][text]\" placeholder=\"' . esc_js( __( 'Enter image caption', 'inoventis' ) ) . '\" /></p>" +
					"</div>" +
					"<div class=\"detailed-image-actions\">" +
					"<button type=\"button\" class=\"button remove-detailed-image-row\">' . esc_js( __( 'Remove Item', 'inoventis' ) ) . '</button>" +
					"</div>" +
					"</div>");
				$("#detailed-images-list").append(row);
				imageIndex++;
			});

			$(document).on("click", ".upload-detailed-image", function(e) {
				e.preventDefault();
				var button = $(this);
				var wrapper = button.closest(".detailed-image-wrapper");
				var imageIdInput = wrapper.find(".detailed-image-id");
				var removeButton = wrapper.find(".remove-detailed-image");

				// Create a new media frame for each upload to ensure proper scoping
				var detailedImageFrame = wp.media({
					title: "' . esc_js( __( 'Select Image', 'inoventis' ) ) . '",
					button: { text: "' . esc_js( __( 'Use this image', 'inoventis' ) ) . '" },
					multiple: false
				});

				detailedImageFrame.on("select", function() {
					var attachment = detailedImageFrame.state().get("selection").first().toJSON();
					imageIdInput.val(attachment.id);
					
					var img = wrapper.find("img");
					var imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
					
					if (img.length) {
						img.attr("src", imageUrl);
					} else {
						wrapper.prepend("<img src=\"" + imageUrl + "\" style=\"max-width: 150px; height: auto; display: block; margin-bottom: 10px;\" />");
					}
					
					removeButton.show();
					detailedImageFrame.close();
				});

				detailedImageFrame.open();
			});

			$(document).on("click", ".remove-detailed-image", function(e) {
				e.preventDefault();
				var wrapper = $(this).closest(".detailed-image-wrapper");
				wrapper.find(".detailed-image-id").val("");
				wrapper.find("img").remove();
				$(this).hide();
			});

			$(document).on("click", ".remove-detailed-image-row", function() {
				$(this).closest(".detailed-image-row").remove();
			});

			// Product Gallery
			var galleryFrame;
			$("#add-gallery-images").on("click", function(e) {
				e.preventDefault();

				if (galleryFrame) {
					galleryFrame.open();
					return;
				}

				galleryFrame = wp.media({
					title: "' . esc_js( __( 'Select Gallery Images', 'inoventis' ) ) . '",
					button: { text: "' . esc_js( __( 'Add to Gallery', 'inoventis' ) ) . '" },
					multiple: true
				});

				galleryFrame.on("select", function() {
					var selection = galleryFrame.state().get("selection");
					selection.each(function(attachment) {
						var imageUrl = attachment.attributes.sizes && attachment.attributes.sizes.thumbnail ? attachment.attributes.sizes.thumbnail.url : attachment.attributes.url;
						var imageId = attachment.id;
						
					var item = $("<div class=\"gallery-image-item\" data-image-id=\"" + imageId + "\">" +
						"<img src=\"" + imageUrl + "\" />" +
						"<input type=\"hidden\" name=\"product_gallery[]\" value=\"" + imageId + "\" />" +
						"<button type=\"button\" class=\"button remove-gallery-image\">" +
						"<span class=\"dashicons dashicons-trash\" style=\"vertical-align: middle; margin-right: 3px; font-size: 16px;\"></span>" +
						"' . esc_js( __( 'Remove', 'inoventis' ) ) . '" +
						"</button>" +
						"</div>");
						$("#gallery-images-list").append(item);
					});
				});

				galleryFrame.open();
			});

			$(document).on("click", ".remove-gallery-image", function() {
				$(this).closest(".gallery-image-item").remove();
			});
		});
	' );
}
add_action( 'admin_enqueue_scripts', 'inoventis_enqueue_product_fields_scripts' );

/**
 * Custom Control for Page/Post/Product Links
 * (Currently not in use - kept for potential future use)
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	class Inoventis_Post_Link_Control extends WP_Customize_Control {
	public $type = 'post_link';
	public $post_types = array( 'page', 'post', 'product' );

	/**
	 * Get post options HTML for dropdown
	 */
	private function get_post_options_html() {
		$html = '<option value="0">' . esc_html__( '— Select —', 'inoventis' ) . '</option>';
		foreach ( $this->post_types as $post_type ) {
			$posts = get_posts( array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'orderby' => 'title',
				'order' => 'ASC',
			) );
			if ( ! empty( $posts ) ) {
				$post_type_obj = get_post_type_object( $post_type );
				$html .= '<optgroup label="' . esc_attr( $post_type_obj->labels->name ) . '">';
				foreach ( $posts as $post_item ) {
					$html .= '<option value="' . esc_attr( $post_item->ID ) . '" data-post-type="' . esc_attr( $post_type ) . '">' . esc_html( $post_item->post_title ) . '</option>';
				}
				$html .= '</optgroup>';
			}
		}
		return $html;
	}

	public function render_content() {
		$value = $this->value();
		$value = ! empty( $value ) ? json_decode( $value, true ) : array();
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		$post_options_html = $this->get_post_options_html();
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<div class="inoventis-post-link-control" data-post-options="<?php echo esc_attr( $post_options_html ); ?>">
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( wp_json_encode( $value ) ); ?>" class="post-link-value" />
			<div class="post-link-items">
				<?php foreach ( $value as $index => $item ) : 
					$post_id = isset( $item['post_id'] ) ? absint( $item['post_id'] ) : 0;
					$link_text = isset( $item['text'] ) ? $item['text'] : '';
					$post = $post_id ? get_post( $post_id ) : null;
					$display_text = $link_text ? $link_text : ( $post ? $post->post_title : '' );
				?>
					<div class="post-link-item" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="post-link-item-header">
							<span class="post-link-item-title"><?php echo esc_html( $display_text ? $display_text : 'New Link ' . ( $index + 1 ) ); ?></span>
							<button type="button" class="post-link-item-toggle">
								<span class="dashicons dashicons-arrow-down-alt2"></span>
							</button>
							<button type="button" class="post-link-item-remove">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</div>
						<div class="post-link-item-content" style="display: none;">
							<label>
								<span><?php _e( 'Link Text (optional)', 'inoventis' ); ?></span>
								<input type="text" class="post-link-text" data-field="text" value="<?php echo esc_attr( $link_text ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to use post title', 'inoventis' ); ?>" />
							</label>
							<label>
								<span><?php _e( 'Select Page/Post/Product', 'inoventis' ); ?></span>
								<select class="post-link-select" data-field="post_id">
									<?php
									// Use the same options HTML but add selected attribute
									$select_options = $post_options_html;
									if ( $post_id ) {
										$select_options = str_replace( 'value="' . $post_id . '"', 'value="' . $post_id . '" selected', $select_options );
									}
									echo $select_options;
									?>
								</select>
							</label>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button button-primary post-link-add-item">
				<span class="dashicons dashicons-plus-alt"></span>
				<?php _e( 'Add New Link', 'inoventis' ); ?>
			</button>
		</div>
		<?php
	}
	} // End Inoventis_Post_Link_Control
} // End if class_exists

/**
 * Custom Control for Repeater Fields (Menu Links and Social Media)
 * (Currently not in use - kept for potential future use)
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	class Inoventis_Repeater_Control extends WP_Customize_Control {
		public $type = 'repeater';
		public $fields = array();
		public $button_text = 'Add Item';

		public function enqueue() {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

		public function render_content() {
		// Get value from setting using parent's value() method
		$value = $this->value();
		
		// Handle null or empty values
		if ( $value === null || $value === false ) {
			$value = '';
		}
		
		// Decode JSON if string
		if ( is_string( $value ) && ! empty( $value ) ) {
			$decoded = json_decode( $value, true );
			if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
				$value = $decoded;
			} else {
				$value = array();
			}
		}
		
		// Ensure value is array
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
		$fields_json = wp_json_encode( array_keys( $this->fields ) );
		$field_labels_json = wp_json_encode( $this->fields );
		$value_json = wp_json_encode( $value );
		// Use htmlspecialchars with ENT_QUOTES to properly escape JSON for data attributes
		$fields_escaped = htmlspecialchars( $fields_json, ENT_QUOTES, 'UTF-8' );
		$labels_escaped = htmlspecialchars( $field_labels_json, ENT_QUOTES, 'UTF-8' );
		?>
		<div class="inoventis-repeater-control" data-type="<?php echo esc_attr( $this->type ); ?>" data-fields="<?php echo $fields_escaped; ?>" data-field-labels="<?php echo $labels_escaped; ?>">
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $value_json ); ?>" class="repeater-value" />
			<div class="repeater-items">
				<?php foreach ( $value as $index => $item ) : ?>
					<div class="repeater-item" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="repeater-item-header">
							<span class="repeater-item-title"><?php 
								$item_title = '';
								if ( isset( $item['text'] ) && ! empty( $item['text'] ) ) {
									$item_title = $item['text'];
								} elseif ( isset( $item['label'] ) && ! empty( $item['label'] ) ) {
									$item_title = $item['label'];
								} else {
									$item_title = 'Item ' . ( $index + 1 );
								}
								echo esc_html( $item_title );
							?></span>
							<button type="button" class="repeater-item-toggle">
								<span class="dashicons dashicons-arrow-down-alt2"></span>
							</button>
							<button type="button" class="repeater-item-remove">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</div>
						<div class="repeater-item-content" style="display: none;">
							<?php foreach ( $this->fields as $field_key => $field_label ) : ?>
								<label>
									<span><?php echo esc_html( $field_label ); ?></span>
									<input type="<?php echo esc_attr( $field_key === 'url' ? 'url' : 'text' ); ?>" 
										   class="repeater-field" 
										   data-field="<?php echo esc_attr( $field_key ); ?>" 
										   value="<?php echo esc_attr( isset( $item[ $field_key ] ) ? $item[ $field_key ] : '' ); ?>" />
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button button-primary repeater-add-item">
				<span class="dashicons dashicons-plus-alt"></span>
				<?php echo esc_html( $this->button_text ); ?>
			</button>
		</div>
		<?php
		}
	} // End Inoventis_Repeater_Control
} // End if class_exists

/**
 * Register Footer Customizer Settings
 */
function inoventis_customize_register( $wp_customize ) {
	// Custom control types are not currently in use
	// Add Footer Section
	$wp_customize->add_section( 'inoventis_footer', array(
		'title'    => __( 'Footer', 'inoventis' ),
		'priority' => 160,
	) );

	// About Title
	$wp_customize->add_setting( 'footer_about_title', array(
		'default'           => 'За нас',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_about_title', array(
		'label'    => __( 'About Title', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'text',
	) );

	// About Text
	$wp_customize->add_setting( 'footer_about_text', array(
		'default'           => 'Иновентис ДОЕЛ е компанија која се занимава со продажба и сервис на индустриски и градежни машини. Ние сме овластен и генерален увозник на Hangcha виљушкари на територија на Република Македонија.',
		'sanitize_callback' => 'sanitize_textarea_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_about_text', array(
		'label'    => __( 'About Text', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'textarea',
	) );

	// Social Media Links (Simple Textarea - Format: Label|URL, one per line)
	$wp_customize->add_setting( 'footer_social_media', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_textarea_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_social_media', array(
		'label'       => __( 'Social Media Links', 'inoventis' ),
		'description' => __( 'Enter one link per line. Format: Label|URL (e.g., Facebook|https://facebook.com)', 'inoventis' ),
		'section'     => 'inoventis_footer',
		'type'        => 'textarea',
	) );

	// Footer Menu Columns
	for ( $i = 1; $i <= 2; $i++ ) {
		$wp_customize->add_setting( 'footer_menu_' . $i . '_title', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'footer_menu_' . $i . '_title', array(
			'label'    => sprintf( __( 'Menu Column %d Title', 'inoventis' ), $i ),
			'section'  => 'inoventis_footer',
			'type'     => 'text',
		) );

		// Menu links - 5 text fields and 5 URL fields
		for ( $j = 1; $j <= 5; $j++ ) {
			// Link Text
			$wp_customize->add_setting( 'footer_menu_' . $i . '_link_' . $j . '_text', array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( 'footer_menu_' . $i . '_link_' . $j . '_text', array(
				'label'    => sprintf( __( 'Menu Column %d - Link %d Text', 'inoventis' ), $i, $j ),
				'section'  => 'inoventis_footer',
				'type'     => 'text',
			) );
			
			// Link URL
			$wp_customize->add_setting( 'footer_menu_' . $i . '_link_' . $j . '_url', array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( 'footer_menu_' . $i . '_link_' . $j . '_url', array(
				'label'    => sprintf( __( 'Menu Column %d - Link %d URL', 'inoventis' ), $i, $j ),
				'section'  => 'inoventis_footer',
				'type'     => 'url',
			) );
		}
	}

	// Copyright Text
	$wp_customize->add_setting( 'footer_copyright', array(
		'default'           => '© 2026 Иновентис. Сите права се задржани.',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_copyright', array(
		'label'    => __( 'Copyright Text', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'text',
	) );

	// Privacy Policy Link
	$wp_customize->add_setting( 'footer_privacy_text', array(
		'default'           => 'Политика за приватност',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_privacy_text', array(
		'label'    => __( 'Privacy Policy Link Text', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'text',
	) );

	$wp_customize->add_setting( 'footer_privacy_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_privacy_url', array(
		'label'    => __( 'Privacy Policy URL', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'url',
	) );

	// Terms Link
	$wp_customize->add_setting( 'footer_terms_text', array(
		'default'           => 'Услови за користење',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_terms_text', array(
		'label'    => __( 'Terms Link Text', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'text',
	) );

	$wp_customize->add_setting( 'footer_terms_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'footer_terms_url', array(
		'label'    => __( 'Terms URL', 'inoventis' ),
		'section'  => 'inoventis_footer',
		'type'     => 'url',
	) );
}
add_action( 'customize_register', 'inoventis_customize_register' );

/**
 * Sanitize JSON input
 */
function inoventis_sanitize_json( $input ) {
	$decoded = json_decode( $input, true );
	if ( json_last_error() === JSON_ERROR_NONE ) {
		return json_encode( $decoded );
	}
	return '[]';
}

/**
 * Enqueue Customizer Scripts
 */
function inoventis_customize_controls_enqueue() {
	wp_enqueue_script(
		'inoventis-customizer-controls',
		INOVENTIS_URI . '/assets/js/customizer-controls.js',
		array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
		INOVENTIS_VERSION,
		true
	);
	wp_enqueue_style(
		'inoventis-customizer-controls',
		INOVENTIS_URI . '/assets/css/customizer-controls.css',
		array(),
		INOVENTIS_VERSION
	);
}
add_action( 'customize_controls_enqueue_scripts', 'inoventis_customize_controls_enqueue' );

