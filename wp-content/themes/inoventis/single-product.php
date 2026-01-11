<?php
/**
 * Template for displaying single products
 *
 * @package Inoventis
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'product-single' ); ?>>
								<!-- Product Images Grid -->
				<?php
				$featured_image_id = get_post_thumbnail_id();
				$gallery_ids = get_post_meta( get_the_ID(), '_product_gallery', true );
				$gallery_ids = ! empty( $gallery_ids ) && is_array( $gallery_ids ) ? $gallery_ids : array();
				
				// Combine featured image and gallery images for modal
				$all_images = array();
				if ( $featured_image_id ) {
					$all_images[] = $featured_image_id;
				}
				if ( ! empty( $gallery_ids ) ) {
					$all_images = array_merge( $all_images, $gallery_ids );
				}
				$all_images = array_unique( $all_images );
				
				if ( ! empty( $all_images ) ) :
					$has_gallery = ! empty( $gallery_ids );
					?>
					<div class="product-images-grid <?php echo ! $has_gallery ? 'single-column' : ''; ?>">
						<!-- Featured Image Column -->
						<?php if ( $featured_image_id ) : ?>
							<div class="product-featured-image-wrapper">
								<div class="product-featured-image" data-image-id="<?php echo esc_attr( $featured_image_id ); ?>" data-image-index="<?php echo esc_attr( array_search( $featured_image_id, $all_images ) ); ?>">
									<?php echo wp_get_attachment_image( $featured_image_id, 'large' ); ?>
								</div>
							</div>
						<?php endif; ?>
						
						<!-- Gallery Grid Column -->
						<?php if ( $has_gallery ) : 
							$gallery_count = count( array_filter( $gallery_ids ) );
							// Calculate grid columns based on image count
							// 1-2 images: 2 cols, 3-4 images: 2 cols, 5-6 images: 3 cols, 7-9 images: 3 cols, 10-12 images: 4 cols, etc.
							$grid_cols = 2;
							if ( $gallery_count <= 2 ) {
								$grid_cols = 2;
							} elseif ( $gallery_count <= 4 ) {
								$grid_cols = 2;
							} elseif ( $gallery_count <= 6 ) {
								$grid_cols = 3;
							} elseif ( $gallery_count <= 9 ) {
								$grid_cols = 3;
							} elseif ( $gallery_count <= 12 ) {
								$grid_cols = 4;
							} else {
								$grid_cols = 4; // Default for more than 12 images
							}
							?>
							<div class="product-gallery-wrapper">
								<div class="product-gallery" style="grid-template-columns: repeat(<?php echo $grid_cols; ?>, 1fr);" data-gallery-count="<?php echo $gallery_count; ?>">
									<?php foreach ( $gallery_ids as $index => $image_id ) : ?>
										<?php if ( $image_id ) : 
											$image_index = array_search( $image_id, $all_images );
											if ( $image_index !== false ) :
											?>
												<div class="gallery-item" data-image-id="<?php echo esc_attr( $image_id ); ?>" data-image-index="<?php echo esc_attr( $image_index ); ?>">
													<?php echo wp_get_attachment_image( $image_id, 'medium' ); ?>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
					
					<!-- Image Modal -->
					<div class="product-image-modal" id="productImageModal">
						<span class="modal-close">&times;</span>
						<button class="modal-nav modal-prev" aria-label="Previous Image">‹</button>
						<button class="modal-nav modal-next" aria-label="Next Image">›</button>
						<div class="modal-content">
							<div class="modal-image-container">
								<img src="" alt="" id="modalImage" />
							</div>
							<div class="modal-counter">
								<span id="currentImageIndex">1</span> / <span id="totalImages"><?php echo count( $all_images ); ?></span>
							</div>
						</div>
					</div>
					
					<script>
					// Store all images data
					window.productImages = <?php 
					$images_data = array();
					foreach ( $all_images as $id ) {
						$image_url = wp_get_attachment_image_url( $id, 'full' );
						if ( $image_url ) {
							$images_data[] = array(
								'id' => $id,
								'url' => $image_url,
								'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: ''
							);
						}
					}
					echo json_encode( $images_data ); 
					?>;
					</script>
					<?php
				endif;
				?>
					
			<div class="product-container product-main-wrapper">

			
				<!-- Main Content Column -->
				<div class="product-content-wrapper">


					<!-- Product Title -->
					<header class="product-header">
						<h1 class="product-title"><?php the_title(); ?></h1>
					</header>

					<!-- Short Description -->
					<?php
					$short_description = get_post_meta( get_the_ID(), '_product_short_description', true );
					if ( ! empty( $short_description ) ) :
						?>
						<div class="product-short-description">
							<?php echo wp_kses_post( $short_description ); ?>
						</div>
						<?php
					endif;
					?>

					<!-- Specifications Table -->
					<?php
					$specifications = get_post_meta( get_the_ID(), '_product_specifications', true );
					if ( ! empty( $specifications ) && is_array( $specifications ) ) :
						// Filter out empty specifications
						$specifications = array_filter( $specifications, function( $spec ) {
							return ! empty( $spec['title'] ) || ! empty( $spec['value'] );
						} );
						
						if ( ! empty( $specifications ) ) :
							?>
							<div class="product-specifications">
								<h2 class="product-titles"><?php _e( 'Спецификации', 'inoventis' ); ?></h2>
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
						endif;
					endif;
					?>

					<!-- Characteristics Accordion -->
					<?php
					$characteristics = get_post_meta( get_the_ID(), '_product_characteristics', true );
					if ( ! empty( $characteristics ) && is_array( $characteristics ) ) :
						?>
						<div class="product-characteristics">
							<h2><?php _e( 'Карактеристики', 'inoventis' ); ?></h2>
							<div class="characteristics-accordion">
								<?php foreach ( $characteristics as $index => $char ) : ?>
									<?php if ( ! empty( $char['title'] ) || ! empty( $char['text'] ) ) : ?>
										<div class="characteristic-item">
											<button class="characteristic-toggle" type="button" aria-expanded="false">
												<span class="characteristic-title"><?php echo esc_html( $char['title'] ); ?></span>
												<span class="characteristic-icon">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M11.6636 3.74467C11.7798 3.62851 11.8862 3.59068 11.9999 3.59068C12.1134 3.59074 12.2195 3.62813 12.3355 3.74398C12.4516 3.86009 12.4895 3.96661 12.4895 4.08027V11.5104H12.9894L19.9189 11.5097C20.0326 11.5097 20.1391 11.5477 20.2552 11.6637C20.3714 11.7799 20.4092 11.8863 20.4092 12C20.4092 12.1137 20.3714 12.2201 20.2552 12.3363C20.1391 12.4523 20.0326 12.4903 19.9189 12.4903L12.9894 12.4896H12.4895V19.9197C12.4895 20.0334 12.4516 20.1399 12.3355 20.256C12.2195 20.3719 12.1134 20.4093 11.9999 20.4093C11.8862 20.4093 11.7798 20.3715 11.6636 20.2553C11.5476 20.1392 11.5096 20.0327 11.5096 19.919V12.4903H11.0097L4.08017 12.4896C3.96646 12.4896 3.86003 12.4518 3.74388 12.3356C3.62794 12.2196 3.59058 12.1136 3.59058 12C3.59058 11.8864 3.62794 11.7804 3.74388 11.6644C3.86003 11.5482 3.96646 11.5104 4.08017 11.5104L11.0097 11.5097H11.5096V4.08096C11.5096 3.9673 11.5476 3.86077 11.6636 3.74467Z" fill="#0B3C53" stroke="#0B3C53"/>
													</svg>
												</span>
											</button>
											<div class="characteristic-content">
												<p><?php echo esc_html( $char['text'] ); ?></p>
											</div>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
						<?php
					endif;
					?>

					<!-- Detailed Images -->
					<?php
					$detailed_images = get_post_meta( get_the_ID(), '_product_detailed_images', true );
					if ( ! empty( $detailed_images ) && is_array( $detailed_images ) ) :
						// Filter out completely empty items
						$detailed_images = array_filter( $detailed_images, function( $item ) {
							return ! empty( $item['image_id'] ) || ! empty( $item['text'] );
						} );
						
						if ( ! empty( $detailed_images ) ) :
							$has_slider = count( $detailed_images ) > 3;
							?>
							<div class="product-detailed-images">
								<h2><?php _e( 'Детални слики', 'inoventis' ); ?></h2>
								<div class="detailed-images-container <?php echo $has_slider ? 'detailed-images-slider' : ''; ?>" data-slider="<?php echo $has_slider ? 'true' : 'false'; ?>">
									<div class="detailed-images-grid-wrapper">
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
									<?php if ( $has_slider ) : ?>
										<button class="detailed-images-slider-btn detailed-images-prev" aria-label="Previous">‹</button>
										<button class="detailed-images-slider-btn detailed-images-next" aria-label="Next">›</button>
									<?php endif; ?>
								</div>
							</div>
							<?php
						endif;
					endif;
					?>
				</div>

				<!-- Contact Sidebar -->
				<div class="product-contact-sidebar">
					<h3>Побарај понуда</h3>
					<p>Иновентис ДОЕЛ е компанија која се занимава со продажба и сервис на индустриски и градежни машини. Ние сме овластен и генерален увозник на Hangcha виљушкари на територија на Република Македонија.</p>
					<div class="contact-divider"></div>
					<div class="contact-info-row">
						<div class="contact-image-wrapper">
							<img src="<?php echo esc_url( content_url( '/uploads/2026/01/komercijalist.png' ) ); ?>" alt="Contact Image" class="contact-image" />
						</div>
						<div class="contact-details">
							<div class="contact-person">
								<p class="contact-name">Илија Дамјанов</p>
								<p class="contact-title">Комерцијалист</p>
							</div>
							<div class="contact-buttons">
								<button type="button" class="contact-button">
									<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M10.5 1.96875C8.23737 1.96875 6.06742 2.86758 4.4675 4.4675C2.86758 6.06742 1.96875 8.23737 1.96875 10.5C1.96875 12.7626 2.86758 14.9326 4.4675 16.5325C6.06742 18.1324 8.23737 19.0312 10.5 19.0312C12.2645 19.0312 14.1176 18.4997 15.4571 17.6096C15.5289 17.5619 15.5906 17.5005 15.6387 17.429C15.6868 17.3574 15.7203 17.2771 15.7373 17.1926C15.7543 17.108 15.7546 17.021 15.7379 16.9364C15.7213 16.8518 15.6882 16.7713 15.6405 16.6995C15.5928 16.6277 15.5314 16.566 15.4598 16.5179C15.3882 16.4699 15.3079 16.4364 15.2234 16.4193C15.1389 16.4023 15.0518 16.4021 14.9672 16.4187C14.8826 16.4353 14.8022 16.4684 14.7304 16.5162C13.6172 17.2577 11.9938 17.7188 10.5 17.7188C9.07227 17.7188 7.67659 17.2954 6.48948 16.5022C5.30236 15.709 4.37711 14.5816 3.83074 13.2625C3.28437 11.9434 3.14142 10.492 3.41996 9.09169C3.69849 7.69139 4.38601 6.40513 5.39557 5.39557C6.40513 4.38601 7.69139 3.69849 9.09169 3.41996C10.492 3.14142 11.9434 3.28437 13.2625 3.83074C14.5816 4.37711 15.709 5.30236 16.5022 6.48948C17.2954 7.67659 17.7188 9.07227 17.7188 10.5C17.7188 12.6697 16.8263 13.125 16.0781 13.125C15.33 13.125 14.4375 12.6697 14.4375 10.5V7.21875C14.4375 7.0447 14.3684 6.87778 14.2453 6.75471C14.1222 6.63164 13.9553 6.5625 13.7812 6.5625C13.6072 6.5625 13.4403 6.63164 13.3172 6.75471C13.1941 6.87778 13.125 7.0447 13.125 7.21875V7.5682C12.5361 7.04019 11.8018 6.70178 11.0178 6.59707C10.2338 6.49235 9.43643 6.62618 8.72958 6.98112C8.02272 7.33605 7.43915 7.89565 7.05489 8.587C6.67062 9.27835 6.50348 10.0694 6.57523 10.8571C6.64699 11.6448 6.95431 12.3926 7.45716 13.0032C7.96002 13.6137 8.63509 14.0587 9.39444 14.28C10.1538 14.5014 10.9622 14.489 11.7144 14.2443C12.4666 13.9997 13.1276 13.5342 13.6114 12.9084C14.1036 13.8928 14.9527 14.4375 16.0781 14.4375C17.9271 14.4375 19.0312 12.9659 19.0312 10.5C19.0289 8.2381 18.1293 6.06954 16.5299 4.47013C14.9305 2.87073 12.7619 1.97114 10.5 1.96875ZM10.5 13.125C9.98082 13.125 9.47331 12.971 9.04163 12.6826C8.60995 12.3942 8.2735 11.9842 8.07482 11.5045C7.87614 11.0249 7.82415 10.4971 7.92544 9.98789C8.02672 9.47869 8.27673 9.01096 8.64384 8.64384C9.01096 8.27673 9.47869 8.02672 9.98789 7.92544C10.4971 7.82415 11.0249 7.87614 11.5045 8.07482C11.9842 8.2735 12.3942 8.60995 12.6826 9.04163C12.971 9.47331 13.125 9.98082 13.125 10.5C13.125 11.1962 12.8484 11.8639 12.3562 12.3562C11.8639 12.8484 11.1962 13.125 10.5 13.125Z" fill="black"/>
									</svg>
									<span>Емаил</span>
								</button>
								<button type="button" class="contact-button">
									<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M10.5 1.96875C8.23737 1.96875 6.06742 2.86758 4.4675 4.4675C2.86758 6.06742 1.96875 8.23737 1.96875 10.5C1.96875 12.7626 2.86758 14.9326 4.4675 16.5325C6.06742 18.1324 8.23737 19.0312 10.5 19.0312C12.2645 19.0312 14.1176 18.4997 15.4571 17.6096C15.5289 17.5619 15.5906 17.5005 15.6387 17.429C15.6868 17.3574 15.7203 17.2771 15.7373 17.1926C15.7543 17.108 15.7546 17.021 15.7379 16.9364C15.7213 16.8518 15.6882 16.7713 15.6405 16.6995C15.5928 16.6277 15.5314 16.566 15.4598 16.5179C15.3882 16.4699 15.3079 16.4364 15.2234 16.4193C15.1389 16.4023 15.0518 16.4021 14.9672 16.4187C14.8826 16.4353 14.8022 16.4684 14.7304 16.5162C13.6172 17.2577 11.9938 17.7188 10.5 17.7188C9.07227 17.7188 7.67659 17.2954 6.48948 16.5022C5.30236 15.709 4.37711 14.5816 3.83074 13.2625C3.28437 11.9434 3.14142 10.492 3.41996 9.09169C3.69849 7.69139 4.38601 6.40513 5.39557 5.39557C6.40513 4.38601 7.69139 3.69849 9.09169 3.41996C10.492 3.14142 11.9434 3.28437 13.2625 3.83074C14.5816 4.37711 15.709 5.30236 16.5022 6.48948C17.2954 7.67659 17.7188 9.07227 17.7188 10.5C17.7188 12.6697 16.8263 13.125 16.0781 13.125C15.33 13.125 14.4375 12.6697 14.4375 10.5V7.21875C14.4375 7.0447 14.3684 6.87778 14.2453 6.75471C14.1222 6.63164 13.9553 6.5625 13.7812 6.5625C13.6072 6.5625 13.4403 6.63164 13.3172 6.75471C13.1941 6.87778 13.125 7.0447 13.125 7.21875V7.5682C12.5361 7.04019 11.8018 6.70178 11.0178 6.59707C10.2338 6.49235 9.43643 6.62618 8.72958 6.98112C8.02272 7.33605 7.43915 7.89565 7.05489 8.587C6.67062 9.27835 6.50348 10.0694 6.57523 10.8571C6.64699 11.6448 6.95431 12.3926 7.45716 13.0032C7.96002 13.6137 8.63509 14.0587 9.39444 14.28C10.1538 14.5014 10.9622 14.489 11.7144 14.2443C12.4666 13.9997 13.1276 13.5342 13.6114 12.9084C14.1036 13.8928 14.9527 14.4375 16.0781 14.4375C17.9271 14.4375 19.0312 12.9659 19.0312 10.5C19.0289 8.2381 18.1293 6.06954 16.5299 4.47013C14.9305 2.87073 12.7619 1.97114 10.5 1.96875ZM10.5 13.125C9.98082 13.125 9.47331 12.971 9.04163 12.6826C8.60995 12.3942 8.2735 11.9842 8.07482 11.5045C7.87614 11.0249 7.82415 10.4971 7.92544 9.98789C8.02672 9.47869 8.27673 9.01096 8.64384 8.64384C9.01096 8.27673 9.47869 8.02672 9.98789 7.92544C10.4971 7.82415 11.0249 7.87614 11.5045 8.07482C11.9842 8.2735 12.3942 8.60995 12.6826 9.04163C12.971 9.47331 13.125 9.98082 13.125 10.5C13.125 11.1962 12.8484 11.8639 12.3562 12.3562C11.8639 12.8484 11.1962 13.125 10.5 13.125Z" fill="black"/>
									</svg>
									<span>Телефон</span>
								</button>
								<button type="button" class="contact-button">
									<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M10.5 1.96875C8.23737 1.96875 6.06742 2.86758 4.4675 4.4675C2.86758 6.06742 1.96875 8.23737 1.96875 10.5C1.96875 12.7626 2.86758 14.9326 4.4675 16.5325C6.06742 18.1324 8.23737 19.0312 10.5 19.0312C12.2645 19.0312 14.1176 18.4997 15.4571 17.6096C15.5289 17.5619 15.5906 17.5005 15.6387 17.429C15.6868 17.3574 15.7203 17.2771 15.7373 17.1926C15.7543 17.108 15.7546 17.021 15.7379 16.9364C15.7213 16.8518 15.6882 16.7713 15.6405 16.6995C15.5928 16.6277 15.5314 16.566 15.4598 16.5179C15.3882 16.4699 15.3079 16.4364 15.2234 16.4193C15.1389 16.4023 15.0518 16.4021 14.9672 16.4187C14.8826 16.4353 14.8022 16.4684 14.7304 16.5162C13.6172 17.2577 11.9938 17.7188 10.5 17.7188C9.07227 17.7188 7.67659 17.2954 6.48948 16.5022C5.30236 15.709 4.37711 14.5816 3.83074 13.2625C3.28437 11.9434 3.14142 10.492 3.41996 9.09169C3.69849 7.69139 4.38601 6.40513 5.39557 5.39557C6.40513 4.38601 7.69139 3.69849 9.09169 3.41996C10.492 3.14142 11.9434 3.28437 13.2625 3.83074C14.5816 4.37711 15.709 5.30236 16.5022 6.48948C17.2954 7.67659 17.7188 9.07227 17.7188 10.5C17.7188 12.6697 16.8263 13.125 16.0781 13.125C15.33 13.125 14.4375 12.6697 14.4375 10.5V7.21875C14.4375 7.0447 14.3684 6.87778 14.2453 6.75471C14.1222 6.63164 13.9553 6.5625 13.7812 6.5625C13.6072 6.5625 13.4403 6.63164 13.3172 6.75471C13.1941 6.87778 13.125 7.0447 13.125 7.21875V7.5682C12.5361 7.04019 11.8018 6.70178 11.0178 6.59707C10.2338 6.49235 9.43643 6.62618 8.72958 6.98112C8.02272 7.33605 7.43915 7.89565 7.05489 8.587C6.67062 9.27835 6.50348 10.0694 6.57523 10.8571C6.64699 11.6448 6.95431 12.3926 7.45716 13.0032C7.96002 13.6137 8.63509 14.0587 9.39444 14.28C10.1538 14.5014 10.9622 14.489 11.7144 14.2443C12.4666 13.9997 13.1276 13.5342 13.6114 12.9084C14.1036 13.8928 14.9527 14.4375 16.0781 14.4375C17.9271 14.4375 19.0312 12.9659 19.0312 10.5C19.0289 8.2381 18.1293 6.06954 16.5299 4.47013C14.9305 2.87073 12.7619 1.97114 10.5 1.96875ZM10.5 13.125C9.98082 13.125 9.47331 12.971 9.04163 12.6826C8.60995 12.3942 8.2735 11.9842 8.07482 11.5045C7.87614 11.0249 7.82415 10.4971 7.92544 9.98789C8.02672 9.47869 8.27673 9.01096 8.64384 8.64384C9.01096 8.27673 9.47869 8.02672 9.98789 7.92544C10.4971 7.82415 11.0249 7.87614 11.5045 8.07482C11.9842 8.2735 12.3942 8.60995 12.6826 9.04163C12.971 9.47331 13.125 9.98082 13.125 10.5C13.125 11.1962 12.8484 11.8639 12.3562 12.3562C11.8639 12.8484 11.1962 13.125 10.5 13.125Z" fill="black"/>
									</svg>
									<span>СМС</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>


			<!-- Related Products Section -->
		<?php
		// Render related products block if it exists in content, otherwise render directly
		$related_products_title = get_post_meta( get_the_ID(), '_product_related_products_title', true );
		if ( empty( $related_products_title ) ) {
			$related_products_title = 'Слични производи';
		}
		
		// Get manually selected related products
		$selected_product_ids = get_post_meta( get_the_ID(), '_product_related_products_ids', true );
		$selected_product_ids = ! empty( $selected_product_ids ) && is_array( $selected_product_ids ) ? $selected_product_ids : array();
		
		// Get current product's subcategory and category
		$subcategories = wp_get_post_terms( get_the_ID(), 'product_subcategory', array( 'fields' => 'ids' ) );
		$categories = wp_get_post_terms( get_the_ID(), 'product_category', array( 'fields' => 'ids' ) );
		
		$related_products = array();
		
		// If products are manually selected, use those
		if ( ! empty( $selected_product_ids ) ) {
			$args = array(
				'post_type' => 'product',
				'post__in' => $selected_product_ids,
				'posts_per_page' => 4,
				'orderby' => 'post__in',
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
				'post__not_in' => array( get_the_ID() ),
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

		if ( ! empty( $related_products ) ) :
		?>
		<section class="product-related-products-section" style="padding-top: 6rem;">
			<h2 class="related-products-title" style="text-align: center; margin-bottom: 2rem;"><?php echo esc_html( $related_products_title ); ?></h2>
			<div class="related-products-grid">
				<?php foreach ( $related_products as $related_product ) : 
					$product_id = $related_product->ID;
					$specifications = get_post_meta( $product_id, '_product_specifications', true );
					$specifications = ! empty( $specifications ) && is_array( $specifications ) ? array_slice( $specifications, 0, 3 ) : array();
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
				<?php endforeach; 
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php endif; ?>
		</article>

		

		<?php
	endwhile;
	?>
</main>

<style>
/* Product Single Page Styles */
.product-single {
	max-width: 1280px;
	margin: 0 auto;
	padding: 2rem;
}

.product-header {
	margin-bottom: 0;
}

.product-title {
	font-size: 2.5rem;
	margin-bottom: 1rem;
}

/* Product Images Grid */
.product-images-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 1.5rem;
	margin-bottom: 2rem;
}

.product-images-grid.single-column {
	grid-template-columns: 1fr;
}

.product-featured-image-wrapper {
	display: flex;
}

.product-featured-image {
	cursor: pointer;
	width: 100%;
	overflow: hidden;
	border-radius: 8px;
	transition: transform 0.3s ease;
}

.product-featured-image:hover {
	transform: scale(1.02);
}

.product-featured-image img {
	width: 100%;
	height: auto;
	display: block;
	object-fit: cover;
}

.product-gallery-wrapper {
	display: flex;
}

.product-gallery {
	display: grid;
	gap: 1rem;
	width: 100%;
	grid-auto-rows: minmax(150px, auto);
}

.gallery-item {
	cursor: pointer;
	overflow: hidden;
	border-radius: 8px;
	aspect-ratio: 1;
	transition: transform 0.3s ease;
}

.gallery-item:hover {
	transform: scale(1.05);
}

.gallery-item img {
	width: 100%;
	height: 100%;
	display: block;
	object-fit: cover;
}

/* Image Modal */
.product-image-modal {
	display: none;
	position: fixed;
	z-index: 10000;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.95);
	overflow: hidden;
}

.product-image-modal.active {
	display: flex;
	align-items: center;
	justify-content: center;
}

.modal-close {
	position: absolute;
	top: 2rem;
	right: 2rem;
	color: #ffffff;
	font-size: 3rem;
	font-weight: bold;
	cursor: pointer;
	z-index: 10001;
	line-height: 1;
	transition: opacity 0.3s;
}

.modal-close:hover {
	opacity: 0.7;
}

.modal-content {
	position: relative;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 4rem 6rem;
	box-sizing: border-box;
}

.modal-image-container {
	max-width: 90%;
	max-height: 85%;
	display: flex;
	align-items: center;
	justify-content: center;
}

.modal-image-container img {
	max-width: 100%;
	max-height: 85vh;
	width: auto;
	height: auto;
	object-fit: contain;
	border-radius: 8px;
}

.modal-nav {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	background: rgba(255, 255, 255, 0.2);
	color: #ffffff;
	border: 2px solid rgba(255, 255, 255, 0.5);
	border-radius: 50%;
	width: 50px;
	height: 50px;
	font-size: 2rem;
	line-height: 1;
	cursor: pointer;
	z-index: 10001;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.3s;
	backdrop-filter: blur(10px);
}

.modal-nav:hover:not(:disabled) {
	background: rgba(255, 255, 255, 0.3);
	border-color: rgba(255, 255, 255, 0.8);
	transform: translateY(-50%) scale(1.1);
}

.modal-nav:disabled {
	opacity: 0.3;
	cursor: not-allowed;
}

.modal-prev {
	left: 2rem;
}

.modal-next {
	right: 2rem;
}

.modal-counter {
	position: absolute;
	bottom: 2rem;
	left: 50%;
	transform: translateX(-50%);
	color: #ffffff;
	font-size: 1rem;
	font-weight: 500;
	background: rgba(0, 0, 0, 0.5);
	padding: 0.5rem 1rem;
	border-radius: 20px;
	backdrop-filter: blur(10px);
}

@media (max-width: 768px) {
	.product-images-grid {
		grid-template-columns: 1fr;
		gap: 1rem;
	}

	.modal-content {
		padding: 4rem 3rem;
	}

	.modal-nav {
		width: 40px;
		height: 40px;
		font-size: 1.5rem;
	}

	.modal-prev {
		left: 1rem;
	}

	.modal-next {
		right: 1rem;
	}

	.modal-close {
		top: 1rem;
		right: 1rem;
		font-size: 2rem;
	}
}

.product-short-description {
	margin-bottom: 0;
	font-size: 1.1rem;
	line-height: 1.6;
}

.product-content {
	margin-bottom: 0;
}

/* Specifications Table */
.specifications-table {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 1rem;
	border: none !important;
}

.specifications-table td {
	padding: 1rem;
	border: none !important;
}

.specifications-table .spec-title {
	font-weight: bold;
	width: 40%;
	text-align: left !important;
	background-color: transparent !important;
}

.specifications-table .spec-value {
	width: 60%;
	text-align: right !important;
}

.specifications-table .spec-row-even,
.specifications-table tr:nth-child(even) {
	background-color: #ffffff !important;
}

.specifications-table .spec-row-odd,
.specifications-table tr:nth-child(odd) {
	background-color: rgba(11, 60, 83, 0.2) !important;
}

/* Characteristics Accordion */
.product-characteristics {
	margin-bottom: 0;
}

.product-characteristics h2 {
	font-size: 2rem;
	margin-bottom: 1.5rem;
}

.characteristics-accordion {
	border: 1px solid #ddd;
	border-radius: 4px;
	overflow: hidden;
}

.characteristic-item {
	border-bottom: 1px solid #ddd;
}

.characteristic-item:last-child {
	border-bottom: none;
}

.characteristic-toggle {
	width: 100%;
	padding: 1.5rem;
	background: #f9f9f9;
	border: none;
	text-align: left;
	cursor: pointer;
	display: flex;
	justify-content: space-between;
	align-items: center;
	transition: background-color 0.3s;
}

.characteristic-toggle:hover {
	background: #f0f0f0;
}

.characteristic-toggle[aria-expanded="true"] {
	background: #e9e9e9;
}

.characteristic-title {
	font-weight: bold;
	font-size: 1.1rem;
}

.characteristic-icon {
	font-size: 1.5rem;
	line-height: 1;
	transition: transform 0.3s;
}

.characteristic-toggle[aria-expanded="true"] .characteristic-icon {
	transform: rotate(45deg);
}

.characteristic-content {
	padding: 0 1.5rem;
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.3s ease-out, padding 0.3s ease-out;
}

.characteristic-toggle[aria-expanded="true"] + .characteristic-content {
	max-height: 500px;
	padding: 1.5rem;
}

.characteristic-content p {
	margin: 0;
	line-height: 1.6;
}

/* Detailed Images */
.product-detailed-images {
	margin-bottom: 0;
}

.product-detailed-images h2 {
	font-size: 2rem;
	margin-bottom: 1.5rem;
}

.detailed-images-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 2rem;
}

.detailed-image-item {
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
}

.detailed-image-item .detailed-image-wrapper {
	width: 100%;
	margin-bottom: 1rem;
}

.detailed-image-item img {
	width: 100%;
	height: auto;
	display: block;
	border-radius: 4px;
}

.detailed-image-caption {
	margin: 0;
	font-size: 0.9rem;
	color: #666;
	width: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Accordion functionality
	const accordionToggles = document.querySelectorAll('.characteristic-toggle');
	
	accordionToggles.forEach(toggle => {
		toggle.addEventListener('click', function() {
			const isExpanded = this.getAttribute('aria-expanded') === 'true';
			const content = this.nextElementSibling;
			
			// Close all other items
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

	// Detailed Images Slider functionality
	const detailedImagesContainer = document.querySelector('.detailed-images-container[data-slider="true"]');
	
	if (detailedImagesContainer) {
		const gridWrapper = detailedImagesContainer.querySelector('.detailed-images-grid-wrapper');
		const grid = detailedImagesContainer.querySelector('.detailed-images-grid');
		const items = grid.querySelectorAll('.detailed-image-item');
		const prevBtn = detailedImagesContainer.querySelector('.detailed-images-prev');
		const nextBtn = detailedImagesContainer.querySelector('.detailed-images-next');
		
		if (items.length > 3) {
			let currentIndex = 0;
			const maxVisibleItems = 3;
			const maxIndex = items.length - maxVisibleItems;
			
			function updateSlider() {
				// Calculate the width of one item with gap
				if (items.length > 0 && grid.offsetWidth > 0) {
					const firstItem = items[0];
					const itemWidth = firstItem.offsetWidth;
					const computedStyle = window.getComputedStyle(grid);
					const gap = parseFloat(computedStyle.gap) || 24; // Get actual gap from CSS (1.5rem = 24px)
					const translateX = -currentIndex * (itemWidth + gap);
					grid.style.transform = `translateX(${translateX}px)`;
				} else {
					// Fallback to percentage if items not yet rendered
					const translateX = -currentIndex * (100 / 3);
					grid.style.transform = `translateX(${translateX}%)`;
				}
				
				// Update button states
				if (prevBtn) prevBtn.disabled = currentIndex === 0;
				if (nextBtn) nextBtn.disabled = currentIndex >= maxIndex;
			}
			
			// Recalculate on window resize
			let resizeTimer;
			window.addEventListener('resize', () => {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(() => {
					currentIndex = Math.min(currentIndex, Math.max(0, items.length - maxVisibleItems));
					updateSlider();
				}, 250);
			});
			
			function nextSlide() {
				const maxIndex = items.length - maxVisibleItems;
				if (currentIndex < maxIndex) {
					currentIndex++;
					updateSlider();
				}
			}
			
			function prevSlide() {
				if (currentIndex > 0) {
					currentIndex--;
					updateSlider();
				}
			}
			
			if (nextBtn) nextBtn.addEventListener('click', nextSlide);
			if (prevBtn) prevBtn.addEventListener('click', prevSlide);
			
			// Initialize
			updateSlider();
			
			// Touch/swipe support
			let touchStartX = 0;
			let touchEndX = 0;
			
			if (gridWrapper) {
				gridWrapper.addEventListener('touchstart', (e) => {
					touchStartX = e.changedTouches[0].screenX;
				}, { passive: true });
				
				gridWrapper.addEventListener('touchend', (e) => {
					touchEndX = e.changedTouches[0].screenX;
					handleSwipe();
				}, { passive: true });
			}
			
			function handleSwipe() {
				if (touchEndX < touchStartX - 50) {
					nextSlide();
				}
				if (touchEndX > touchStartX + 50) {
					prevSlide();
				}
			}
		}
	}

	// Product Image Modal functionality
	if (typeof window.productImages !== 'undefined' && window.productImages.length > 0) {
		const modal = document.getElementById('productImageModal');
		const modalImage = document.getElementById('modalImage');
		const modalClose = modal ? modal.querySelector('.modal-close') : null;
		const modalPrev = modal ? modal.querySelector('.modal-prev') : null;
		const modalNext = modal ? modal.querySelector('.modal-next') : null;
		const currentIndexSpan = document.getElementById('currentImageIndex');
		const totalImagesSpan = document.getElementById('totalImages');
		
		let currentImageIndex = 0;
		const totalImages = window.productImages.length;
		
		if (totalImagesSpan) {
			totalImagesSpan.textContent = totalImages;
		}
		
		function openModal(index) {
			if (!modal || !modalImage || index < 0 || index >= totalImages) return;
			
			currentImageIndex = index;
			updateModalImage();
			if (modal) {
				modal.classList.add('active');
				document.body.style.overflow = 'hidden';
			}
		}
		
		function closeModal() {
			if (modal) {
				modal.classList.remove('active');
				document.body.style.overflow = '';
			}
		}
		
		function updateModalImage() {
			if (!modalImage || !window.productImages[currentImageIndex]) return;
			
			const imageData = window.productImages[currentImageIndex];
			modalImage.src = imageData.url;
			modalImage.alt = imageData.alt || 'Product Image';
			
			if (currentIndexSpan) {
				currentIndexSpan.textContent = currentImageIndex + 1;
			}
			
			// Update navigation button states
			if (modalPrev) modalPrev.disabled = currentImageIndex === 0;
			if (modalNext) modalNext.disabled = currentImageIndex >= totalImages - 1;
		}
		
		function nextImage() {
			if (currentImageIndex < totalImages - 1) {
				currentImageIndex++;
				updateModalImage();
			}
		}
		
		function prevImage() {
			if (currentImageIndex > 0) {
				currentImageIndex--;
				updateModalImage();
			}
		}
		
		// Add click handlers to featured image and gallery items
		const featuredImage = document.querySelector('.product-featured-image');
		const galleryItems = document.querySelectorAll('.gallery-item');
		
		if (featuredImage) {
			featuredImage.addEventListener('click', function() {
				const imageIndex = parseInt(this.getAttribute('data-image-index') || '0');
				openModal(imageIndex);
			});
		}
		
		galleryItems.forEach(item => {
			item.addEventListener('click', function() {
				const imageIndex = parseInt(this.getAttribute('data-image-index') || '0');
				openModal(imageIndex);
			});
		});
		
		// Modal navigation
		if (modalClose) {
			modalClose.addEventListener('click', closeModal);
		}
		
		if (modalPrev) {
			modalPrev.addEventListener('click', prevImage);
		}
		
		if (modalNext) {
			modalNext.addEventListener('click', nextImage);
		}
		
		// Close modal on background click
		if (modal) {
			modal.addEventListener('click', function(e) {
				if (e.target === modal) {
					closeModal();
				}
			});
		}
		
		// Keyboard navigation
		document.addEventListener('keydown', function(e) {
			if (!modal || !modal.classList.contains('active')) return;
			
			if (e.key === 'Escape') {
				closeModal();
			} else if (e.key === 'ArrowLeft') {
				prevImage();
			} else if (e.key === 'ArrowRight') {
				nextImage();
			}
		});
		
		// Touch/swipe support for modal
		let modalTouchStartX = 0;
		let modalTouchEndX = 0;
		
		if (modal) {
			modal.addEventListener('touchstart', function(e) {
				modalTouchStartX = e.changedTouches[0].screenX;
			}, { passive: true });
			
			modal.addEventListener('touchend', function(e) {
				modalTouchEndX = e.changedTouches[0].screenX;
				handleModalSwipe();
			}, { passive: true });
		}
		
		function handleModalSwipe() {
			const swipeThreshold = 50;
			if (modalTouchEndX < modalTouchStartX - swipeThreshold) {
				nextImage();
			}
			if (modalTouchEndX > modalTouchStartX + swipeThreshold) {
				prevImage();
			}
		}
	}
});
</script>

<?php
get_footer();
