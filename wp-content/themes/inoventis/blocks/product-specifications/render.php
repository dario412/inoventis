<?php
/**
 * Render callback for Product Specifications block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_specifications_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	$specifications = get_post_meta( $post_id, '_product_specifications', true );
	
	if ( empty( $specifications ) || ! is_array( $specifications ) ) {
		return '';
	}

	// Filter out empty specifications
	$specifications = array_filter( $specifications, function( $spec ) {
		return ! empty( $spec['title'] ) || ! empty( $spec['value'] );
	} );

	if ( empty( $specifications ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-specifications-block',
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<h2><?php _e( 'Спецификации', 'inoventis' ); ?></h2>
		<table class="specifications-table">
			<tbody>
				<?php foreach ( $specifications as $index => $spec ) : ?>
					<tr class="<?php echo ( $index % 2 === 0 ) ? 'spec-row-even' : 'spec-row-odd'; ?>">
						<td class="spec-title"><?php echo esc_html( $spec['title'] ?? '' ); ?></td>
						<td class="spec-value"><?php echo esc_html( $spec['value'] ?? '' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}

