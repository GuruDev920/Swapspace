<div class="sg-tabs-<?php echo $tab['id']; ?> sg-tabs_content <?php echo ( ! empty( $tab['active'] ) ? 'sg-tabs_content--active' : '' ) ?> " id="<?php echo $tab['id'];?>">
	<div class="sg-grid sg-grid--gap-responsive sg-grid--autoflow-row sg-grid--m-3 sg-grid--sm-2 sg-tab-content" data-tab-content="<?php echo $tab['id']; ?>">
		<?php $plugins_control->render_plugins( $tab['id'] ) ?>
	</div>

	<?php
	if ( $tab['id'] === 'default' ) {
		include \SiteGround_Central\DIR . '/templates/partials/plugins/load-more.php';
	}
	?>
</div>
