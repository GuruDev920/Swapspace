<?php
$preview_url = add_query_arg(
	array(
		'theme'     => urlencode( $theme->slug ),
		'TB_iframe' => true,
		'width'     => 780,
		'height'    => 680,
	),
	admin_url( 'theme-install.php' )
);
?>
<div class="sg-container sg-container--elevation-1 sg-with-padding sg-with-padding--padding-top-none sg-with-padding--padding-right-none sg-with-padding--padding-bottom-none sg-with-padding--padding-left-none listing-card">
	<a
		class="sg-link theme-preview sg-with-color sg-typography sg-typography--break-all themes--preview theme sg-preview"
		data-slug="<?php echo $theme->slug; ?>"
		href="<?php echo ! empty( $theme->preview_url ) ? $theme->preview_url : $theme->live_demo_url; ?>"
	>
	<span class="sg-icon sg-icon--fill-lighter sg-icon--use-css-colors sg-with-color sg-with-color--color-white themes--preview-icon" style="width: 40px; height: 40px;">
		<img src="<?php echo \SiteGround_Central\URL . '/assets/img/zoom.svg'; ?>" width="40" height="40">
	</span>
		<img src="<?php echo ! empty( $theme->screenshot_url ) ? $theme->screenshot_url : $theme->thumbnail; ?>">
	</a>
	<div class="sg-flex sg-flex--align-center sg-flex--gutter-small sg-flex--justify-space-between sg-flex--flex-wrap-nowrap sg-flex--margin-none sg-with-padding sg-with-padding--padding-top-small sg-with-padding--padding-right-small sg-with-padding--padding-bottom-small sg-with-padding--padding-left-small ua-border-top sg-theme-container">
		<p class="sg-text sg-text--size-medium sg-with-color sg-with-color--color-darkest sg-with-padding sg-with-padding--padding-top-x-small sg-with-padding--padding-right-none sg-with-padding--padding-bottom-x-small sg-with-padding--padding-left-none sg-typography sg-typography--weight-regular">
			<?php
			if (
				! empty( $theme->live_demo_url ) &&
				( false === strpos( $theme->slug, 'storefront' ) && false === strpos( $theme->slug, 'twenty' ) )
			) {
				echo ucwords( $theme->slug ) . ' - ' . $theme->name;
			} else {
				echo $theme->name;
			}
			?>
			<span class="themes--footer-price">
				(Free)
			</span>
		</p>
		<?php echo $this->get_actions( $theme, $type ); ?>
	</div>
</div>
