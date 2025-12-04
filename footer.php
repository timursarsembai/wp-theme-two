<?php
/**
 * Footer template
 */
?>
		</div><!-- #content -->
	</div><!-- #page -->

	<footer class="site-footer">
		<div class="container">
			<div class="footer-content" style="text-align: center;">
				<div class="site-info" style="font-family: var(--font-ui); font-size: var(--fs-sm); color: var(--color-text-light);">
					<p style="margin: 0;"><?php bloginfo( 'name' ); ?> &copy; <?php echo date( 'Y' ); ?></p>
				</div>
				
				<?php if ( is_active_sidebar( 'footer-area' ) ) : ?>
					<div class="footer-widgets" style="margin-top: var(--spacing-md);">
						<?php dynamic_sidebar( 'footer-area' ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div style="border-top: 1px solid var(--color-border); padding-top: var(--spacing-lg); margin-top: var(--spacing-lg); text-align: center; font-family: var(--font-ui); font-size: var(--fs-sm); color: var(--color-text-light);">
				<p><?php printf( __( 'Proudly powered by %s', 'islamic-scholars' ), '<a href="https://wordpress.org">WordPress</a>' ); ?></p>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
