<?php
namespace SiteGround_Central;

use SiteGround_Central\Control\Themes;
$themes_control = Themes::get_instance();
$type = isset( $_GET['type'] ) ? $_GET['type'] : '';
?>
<div class="sg-section sg-section--density-cozy">
	<div class="sg-section__content">
		<div class="sg-grid sg-grid--gap-responsive">
			<div class="sg-flex sg-flex--align-center sg-flex--gutter-medium sg-flex--direction-row sg-flex--justify-space-between sg-with-padding sg-with-padding--padding-top-responsive sg-with-padding--padding-right-none sg-with-padding--padding-bottom-responsive sg-with-padding--padding-left-none">
				<h1 class="sg-title sg-title--density-none sg-title--level-1 sg-with-color sg-with-color--color-darkest sg-typography sg-typography--weight-bold">
					Add Themes
				</h1>
			</div>
		</div>
		<div class="sg-grid sg-grid--gap-responsive">
			<div class="sg-grid sg-grid--autoflow-row sg-grid--m-3 sg-settings--grid">
				<div class="sg-tabs-wrapper sg-tabs-wrapper--border-light sg-flex--align-center sg-grid-column--m-span-2">
					<div class="sg-tabs_container">
						<ul class="sg-tabs sg-tabs--background-transparent sg-tabs--medium sg-tabs--active-color-sky">
							<?php
							foreach ( $themes_control->tabs as $tab ) {
								include( \SiteGround_Central\DIR . '/templates/partials/themes/tabs.php' );
							}
							?>
						</ul>
					</div>
				</div>
				<div class="sg-tabs-wrapper sg-tabs-wrapper--border-light sg-flex--align-center sg-settings--wrapper">
					<?php include \SiteGround_Central\DIR . '/templates/partials/themes/search-form.php'; ?>
				</div>
			</div>
			<div class="sg-central-tab-content">
				<?php
				foreach ( $themes_control->tabs as $tab ) {
					include( \SiteGround_Central\DIR . '/templates/partials/themes/' . $tab['template'] . '.php' );
				}
				?>
			</div>
		</div>
		<div class="sg-modals">
			<?php include( \SiteGround_Central\DIR . '/templates/partials/themes/popup.php' ); ?>
			<?php include( \SiteGround_Central\DIR . '/templates/partials/themes/dialog.php' ); ?>
			<?php include( \SiteGround_Central\DIR . '/templates/partials/themes/confirmation-dialog.php' ); ?>
		</div>
	</div>
</div>
