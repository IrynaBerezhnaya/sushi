<?php
/**
 * The template for displaying 404 pages (not found).
 *
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found">

				<div class="page-content">

					<header class="page-header">
						<h1><?php esc_html_e( '404', 'storefront' ); ?></h1>
						<h3 class="page-subtitle"><?php esc_html_e( 'Помилка', 'storefront' ); ?></h3>
					</header><!-- .page-header -->

					<p><?php esc_html_e( 'Ми загубили цю сторінку і цілу рибку :(', 'storefront' ); ?></p>
					<p><?php esc_html_e( 'Допоможете нам знайти рибку?', 'storefront' ); ?></p>


				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
