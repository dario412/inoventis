	<footer id="colophon" class="site-footer">
		<div class="footer-container">
			<!-- Main Footer Grid -->
			<div class="footer-main-grid">
				<!-- Column 1: Logo and About -->
				<div class="footer-col-1">
					<div class="footer-logo-section">
						<!-- Logo -->
						<div class="footer-logo-wrapper">
							<?php
							if ( has_custom_logo() ) {
								$logo_id = get_theme_mod( 'custom_logo' );
								$logo = wp_get_attachment_image_src( $logo_id, 'full' );
								if ( $logo ) {
									?>
									<img src="<?php echo esc_url( $logo[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="footer-logo" />
									<?php
								}
							} else {
								?>
								<span class="footer-logo-text"><?php bloginfo( 'name' ); ?></span>
								<?php
							}
							?>
						</div>
						
						<!-- About Section -->
						<div class="footer-about-section">
							<div class="footer-about-content">
								<?php
								$about_title = get_theme_mod( 'footer_about_title', 'За нас' );
								$about_text = get_theme_mod( 'footer_about_text', 'Иновентис ДОЕЛ е компанија која се занимава со продажба и сервис на индустриски и градежни машини. Ние сме овластен и генерален увозник на Hangcha виљушкари на територија на Република Македонија.' );
								?>
								<p class="footer-about-title"><?php echo esc_html( $about_title ); ?></p>
								<p class="footer-about-text"><?php echo esc_html( $about_text ); ?></p>
							</div>
							
							<!-- Social Media Icons -->
							<div class="footer-social-media">
								<?php
								$social_media_text = get_theme_mod( 'footer_social_media', '' );
								$social_media_lines = array_filter( array_map( 'trim', explode( "\n", $social_media_text ) ) );
								
								foreach ( $social_media_lines as $line ) {
									if ( strpos( $line, '|' ) !== false ) {
										list( $label, $url ) = array_map( 'trim', explode( '|', $line, 2 ) );
										if ( ! empty( $url ) ) {
											$label = ! empty( $label ) ? $label : 'Social';
											$icon_key = strtolower( $label );
											?>
											<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon social-<?php echo esc_attr( sanitize_title( $icon_key ) ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
												</svg>
											</a>
											<?php
										}
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Column 2: Footer Menu Links -->
				<div class="footer-col-2">
						<?php
						// Render two menu columns
						for ( $i = 1; $i <= 2; $i++ ) {
							$menu_title = get_theme_mod( 'footer_menu_' . $i . '_title', '' );
							$menu_links_text = get_theme_mod( 'footer_menu_' . $i . '_links', '' );
							$menu_links_lines = array_filter( array_map( 'trim', explode( "\n", $menu_links_text ) ) );
							
							if ( ! empty( $menu_title ) || ! empty( $menu_links_lines ) || $i === 1 ) { // Show at least first column
								?>
								<div class="footer-menu-column">
									<?php if ( ! empty( $menu_title ) ) : ?>
										<p class="footer-menu-title"><?php echo esc_html( $menu_title ); ?></p>
									<?php endif; ?>
									<div class="footer-menu-links">
										<?php
										for ( $j = 1; $j <= 5; $j++ ) {
											$link_text = get_theme_mod( 'footer_menu_' . $i . '_link_' . $j . '_text', '' );
											$link_url = get_theme_mod( 'footer_menu_' . $i . '_link_' . $j . '_url', '' );
											
											if ( ! empty( $link_text ) && ! empty( $link_url ) ) {
												?>
												<a href="<?php echo esc_url( $link_url ); ?>" class="footer-menu-link"><?php echo esc_html( $link_text ); ?></a>
												<?php
											}
										}
										?>
									</div>
								</div>
								<?php
							}
						}
						?>
				</div>
			</div>
			
			<!-- Footer Bottom -->
			<div class="footer-bottom">
				<p class="footer-copyright"><?php echo esc_html( get_theme_mod( 'footer_copyright', '© 2026 Иновентис. Сите права се задржани.' ) ); ?></p>
				<div class="footer-bottom-links">
					<?php
					$privacy_text = get_theme_mod( 'footer_privacy_text', 'Политика за приватност' );
					$privacy_url = get_theme_mod( 'footer_privacy_url', '' );
					$terms_text = get_theme_mod( 'footer_terms_text', 'Услови за користење' );
					$terms_url = get_theme_mod( 'footer_terms_url', '' );
					
					if ( ! empty( $privacy_url ) ) {
						?>
						<a href="<?php echo esc_url( $privacy_url ); ?>" class="footer-bottom-link"><?php echo esc_html( $privacy_text ); ?></a>
						<?php
					}
					
					if ( ! empty( $terms_url ) ) {
						?>
						<a href="<?php echo esc_url( $terms_url ); ?>" class="footer-bottom-link"><?php echo esc_html( $terms_text ); ?></a>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
