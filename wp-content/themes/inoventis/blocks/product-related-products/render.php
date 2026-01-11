<?php
/**
 * Render callback for Product Related Products block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_related_products_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	// Get custom title or use default
	$title = get_post_meta( $post_id, '_product_related_products_title', true );
	if ( empty( $title ) ) {
		$title = 'Слични производи';
	}

	// Get manually selected related products
	$selected_product_ids = get_post_meta( $post_id, '_product_related_products_ids', true );
	$selected_product_ids = ! empty( $selected_product_ids ) && is_array( $selected_product_ids ) ? $selected_product_ids : array();
	
	// Get current product's subcategory and category
	$subcategories = wp_get_post_terms( $post_id, 'product_subcategory', array( 'fields' => 'ids' ) );
	$categories = wp_get_post_terms( $post_id, 'product_category', array( 'fields' => 'ids' ) );
	
	$related_products = array();
	
	// If products are manually selected, use those
	if ( ! empty( $selected_product_ids ) ) {
		$args = array(
			'post_type' => 'product',
			'post__in' => $selected_product_ids,
			'posts_per_page' => 4,
			'orderby' => 'post__in', // Maintain the order of selected products
			'post_status' => 'publish',
		);
		$related_products = get_posts( $args );
	} else {
		// Auto-fetch related products from subcategory first, then category
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
			'post__not_in' => array( $post_id ), // Exclude current product
			'posts_per_page' => 4,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_status' => 'publish',
		);
		
		if ( ! empty( $tax_query ) && count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		} elseif ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query[0];
		}
		
		$related_products = get_posts( $args );
	}

	if ( empty( $related_products ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-related-products-block',
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<h2 class="related-products-title"><?php echo esc_html( $title ); ?></h2>
		<div class="related-products-grid">
			<?php foreach ( $related_products as $related_product ) : 
				$product_id = $related_product->ID;
				$specifications = get_post_meta( $product_id, '_product_specifications', true );
				$specifications = ! empty( $specifications ) && is_array( $specifications ) ? array_slice( $specifications, 0, 3 ) : array(); // Limit to 3 specs
				?>
				<div class="related-product-item">
					<?php if ( has_post_thumbnail( $product_id ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="related-product-image">
							<?php echo get_the_post_thumbnail( $product_id, 'medium_large' ); ?>
						</a>
					<?php endif; ?>
					
					<p class="related-product-title">
						<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
							<?php echo esc_html( get_the_title( $product_id ) ); ?>
						</a>
					</p>
					
					<?php if ( ! empty( $specifications ) ) : ?>
						<div class="related-product-specs">
							<div class="related-product-divider"></div>
							<?php foreach ( $specifications as $spec ) : ?>
								<?php if ( ! empty( $spec['title'] ) || ! empty( $spec['value'] ) ) : ?>
									<div class="related-product-spec-item">
										<span class="spec-title"><?php echo esc_html( $spec['title'] ?? '' ); ?></span>
										<span class="spec-value"><?php echo esc_html( $spec['value'] ?? '' ); ?></span>
									</div>
									<div class="related-product-divider"></div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="related-product-button">
						<?php _e( 'Прочитај повеќе', 'inoventis' ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}

