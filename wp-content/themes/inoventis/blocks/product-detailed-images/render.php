<?php
/**
 * Render callback for Product Detailed Images block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_detailed_images_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	$detailed_images = get_post_meta( $post_id, '_product_detailed_images', true );
	
	if ( empty( $detailed_images ) || ! is_array( $detailed_images ) ) {
		return '';
	}

	// Filter out completely empty items
	$detailed_images = array_filter( $detailed_images, function( $item ) {
		return ! empty( $item['image_id'] ) || ! empty( $item['text'] );
	} );

	if ( empty( $detailed_images ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-detailed-images-block',
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<h2><?php _e( 'Детални слики', 'inoventis' ); ?></h2>
		<div class="detailed-images-grid">
			<?php foreach ( $detailed_images as $item ) : ?>
				<div class="detailed-image-item">
					<?php if ( ! empty( $item['image_id'] ) ) : ?>
						<div class="detailed-image-wrapper">
							<?php echo wp_get_attachment_image( $item['image_id'], 'medium' ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<p class="detailed-image-caption"><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

