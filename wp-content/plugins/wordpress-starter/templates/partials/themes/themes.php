<div class="sg-grid sg-grid--gap-responsive sg-grid--autoflow-row sg-tabs-<?php echo $tab['id']; ?> sg-tab-<?php echo $tab['id']; ?> sg-tabs_content <?php echo ( ! empty( $tab['active'] ) ? 'sg-tabs_content--active' : '' ) ?>">
	<div class="filter-drawer">
		<div class="buttons">
			<button type="button" class="apply-filters button"><?php _e( 'Apply Filters' ); ?><span></span></button>
			<button type="button" class="clear-filters button" aria-label="<?php esc_attr_e( 'Clear current filters' ); ?>"><?php _e( 'Clear' ); ?></button>
		</div>
	</div>

	<div class="sg-grid sg-grid--gap-responsive sg-grid--autoflow-row sg-grid--m-3 sg-grid--sm-2 sg-tab-content" data-tab-content="<?php echo $tab['id']; ?>">
		<?php $themes_control->render_themes( $tab['id'] ); ?>
	</div>

	<?php
	if ( 'recommended' !== $tab['id'] ) {
		include \SiteGround_Central\DIR . '/templates/partials/themes/load-more.php';
	}
	?>
</div>

