<?php
/**
 * Render callback for Product Characteristics block
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the block content.
 */
function inoventis_render_product_characteristics_block( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	
	if ( ! $post_id ) {
		return '';
	}

	$characteristics = get_post_meta( $post_id, '_product_characteristics', true );
	
	if ( empty( $characteristics ) || ! is_array( $characteristics ) ) {
		return '';
	}

	// Filter out empty characteristics
	$characteristics = array_filter( $characteristics, function( $char ) {
		return ! empty( $char['title'] ) || ! empty( $char['text'] );
	} );

	if ( empty( $characteristics ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'product-characteristics-block',
	) );

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<h2><?php _e( 'Карактеристики', 'inoventis' ); ?></h2>
		<div class="characteristics-accordion">
			<?php foreach ( $characteristics as $index => $char ) : ?>
				<div class="characteristic-item">
					<button class="characteristic-toggle" type="button" aria-expanded="false">
						<span class="characteristic-title"><?php echo esc_html( $char['title'] ?? '' ); ?></span>
						<span class="characteristic-icon">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M11.6636 3.74467C11.7798 3.62851 11.8862 3.59068 11.9999 3.59068C12.1134 3.59074 12.2195 3.62813 12.3355 3.74398C12.4516 3.86009 12.4895 3.96661 12.4895 4.08027V11.5104H12.9894L19.9189 11.5097C20.0326 11.5097 20.1391 11.5477 20.2552 11.6637C20.3714 11.7799 20.4092 11.8863 20.4092 12C20.4092 12.1137 20.3714 12.2201 20.2552 12.3363C20.1391 12.4523 20.0326 12.4903 19.9189 12.4903L12.9894 12.4896H12.4895V19.9197C12.4895 20.0334 12.4516 20.1399 12.3355 20.256C12.2195 20.3719 12.1134 20.4093 11.9999 20.4093C11.8862 20.4093 11.7798 20.3715 11.6636 20.2553C11.5476 20.1392 11.5096 20.0327 11.5096 19.919V12.4903H11.0097L4.08017 12.4896C3.96646 12.4896 3.86003 12.4518 3.74388 12.3356C3.62794 12.2196 3.59058 12.1136 3.59058 12C3.59058 11.8864 3.62794 11.7804 3.74388 11.6644C3.86003 11.5482 3.96646 11.5104 4.08017 11.5104L11.0097 11.5097H11.5096V4.08096C11.5096 3.9673 11.5476 3.86077 11.6636 3.74467Z" fill="#0B3C53" stroke="#0B3C53"/>
							</svg>
						</span>
					</button>
					<div class="characteristic-content">
						<p><?php echo esc_html( $char['text'] ?? '' ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

