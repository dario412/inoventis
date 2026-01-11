<?php
/**
 * Render callback for Product Short Description block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_short_description_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	$short_description = get_post_meta( $post_id, '_product_short_description', true );
	
	if ( empty( $short_description ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-short-description-block',
	) );

	return sprintf(
		'<div %s><div class="product-short-description-content">%s</div></div>',
		$wrapper_attributes,
		wp_kses_post( $short_description )
	);
}

