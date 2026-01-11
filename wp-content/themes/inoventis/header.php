<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'inoventis' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="header-container">
			<div class="logo-container">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>
					<?php
				}
				?>
			</div>

			<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'inoventis' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="menu-toggle-text"><?php esc_html_e( 'Menu', 'inoventis' ); ?></span>
					<span class="menu-toggle-icon">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</button>
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'primary',
						'menu_id'         => 'primary-menu',
						'menu_class'      => 'nav-menu',
						'container'       => false,
						'fallback_cb'     => false,
						'depth'           => 3,
					)
				);
				?>
			</nav>

			<div class="header-actions">
				<a href="<?php echo esc_url( home_url( '/akcija' ) ); ?>" class="button-outline">Акција</a>
				<a href="<?php echo esc_url( home_url( '/kontakt' ) ); ?>" class="button">Контакт</a>
			</div>
		</div>
	</header>

