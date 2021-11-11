<li class="sg-ripple-container sg-tabs__tab <?php echo ( ! empty( $tab['active'] ) ? 'sg-tabs__tab--active' : '' ) ?>" data-tab="<?php echo $tab['id']; ?>">
	<?php echo $tab['title']; ?>

	<?php if ( ! empty( $tab['count'] ) ) : ?>
		<span class="sg-label sg-label--type-default sg-label--size-small sg-with-color sg-with-color--color-light">
			<span class="sg-label__text">
				<?php echo $tab['count']; ?>
			</span>
		</span>
	<?php endif; ?>
</li>
