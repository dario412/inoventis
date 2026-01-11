<?php
/**
 * The main template file
 *
 * @package Inoventis
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	?>
</main>

<?php
get_footer();

