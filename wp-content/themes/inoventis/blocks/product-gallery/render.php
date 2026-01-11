<?php
/**
 * Render callback for Product Gallery block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_gallery_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	$gallery_ids = get_post_meta( $post_id, '_product_gallery', true );
	
	if ( empty( $gallery_ids ) || ! is_array( $gallery_ids ) ) {
		return '';
	}

	// Filter out empty IDs
	$gallery_ids = array_filter( array_map( 'intval', $gallery_ids ) );

	if ( empty( $gallery_ids ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-gallery-block',
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<div class="product-gallery">
			<?php foreach ( $gallery_ids as $image_id ) : ?>
				<?php if ( $image_id ) : ?>
					<div class="gallery-item">
						<?php echo wp_get_attachment_image( $image_id, 'large' ); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

