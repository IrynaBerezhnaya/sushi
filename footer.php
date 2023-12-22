<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront child
 */

?>

</div><!-- .col-full -->
</div><!-- #content -->

<?php do_action( 'storefront_before_footer' ); ?>

<footer id="colophon" class="site-footer" role="contentinfo">
	<?php
	$footer_title   = get_field('footer_title', 'option');
	$footer_images  = get_field('footer_images', 'option');
	$footer_socials = get_field('footer_socials', 'option');
	$footer_text    = get_field('footer_text', 'option');

	if ( is_front_page() || is_product_category() ) :
		if ( ! empty( $footer_images ) ) :
			if ( ! empty( $footer_title ) ) : ?>
				<h2 class="title"><?php echo $footer_title; ?></h2>
			<?php endif; ?>
			<div class="payment-method">
				<?php foreach ($footer_images as $item) {
					$text = $item['text'];
					$image = $item['image'];
					if ( $text && $image ) :?>
					<div class="payment-method__item" style="--bg-desktop: url( <?php echo esc_url($image['url']); ?> )">
						<?php if ( ! empty( $text ) ) : ?>
							<p><?php echo $text; ?></p>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				<?php } ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="footer">
		<div class="col-full footer-container">
			<?php if ( ! empty( $footer_socials ) ) : ?>
				<div class="footer-socials">
					<?php foreach( $footer_socials as $item ) : ?>
						<?php if ( ! empty( $item['link'] or $item['text'] ) ) : ?>
							<div class="social__list">
								<a class="social__list-link" href="<?php echo ! empty($item['link']) ? $item['link']['url'] : $item['text']; ?>">
									<?php if ( $item['icon']['url'] ) { ?>
									<img src="<?php echo esc_url($item['icon']['url']); ?>" alt="<?php echo $item['icon']['alt']; ?>">
									<?php } elseif ($item['link']) { ?>
									<?php echo $item['link']['title']; }?>
								</a>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $footer_text ) ) : ?>
            <div class="footer-text"><?php echo $footer_text; ?></div>
			<?php endif; ?>
		</div><!-- .col-full -->
	</div>
</footer><!-- #colophon -->

<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
