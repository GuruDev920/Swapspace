<div class="sg-container sg-container--elevation-1 sg-with-padding sg-with-padding--padding-top-none sg-with-padding--padding-right-none sg-with-padding--padding-bottom-none sg-with-padding--padding-left-none listing-card sg-plugin">
	<div class="sg-grid sg-grid--gap-responsive sg-grid--autoflow-column sg-with-padding sg-with-padding--padding-top-responsive sg-with-padding--padding-right-responsive sg-with-padding--padding-bottom-responsive sg-with-padding--padding-left-responsive sg-plugin__grid">
		<img src="<?php echo ! empty( $plugin['icons']['1x'] ) ? $plugin['icons']['1x'] : $plugin['logo_url'];?>" alt="<?php echo $plugin['slug'];?>" class="sg-plugin__icon"/>
		<div class="sg-grid sg-grid--gap-x-small sg-grid--autoflow-row">
			<h4 class="sg-title sg-title--level-4 sg-with-color sg-with-color--color-darkest sg-typography sg-typography--weight-bold">
				<?php echo $plugin['name']; ?>
			</h4>

			<p class="sg-text sg-text--size-small sg-with-color sg-with-color--color-dark sg-typography sg-typography--weight-regular">
				<?php echo $this->check_compatibility( $plugin );?>
			</p>

			<p class="sg-text sg-text--size-small sg-with-color sg-with-color--color-dark sg-typography sg-typography--weight-regular">
				<?php echo ! empty( $plugin['description'][0]['description'] ) ? $plugin['description'][0]['description'] : $plugin['short_description']; ?>
			</p>
		</div>
	</div>
	<div class="sg-toolbar sg-toolbar--background-light sg-toolbar--density-cozy sg-toolbar--align-center sg-toolbar--justify-flex-end sg-toolbar--direction-row">
        <a href="<?php echo self_admin_url( 'plugin-install.php?tab=plugin-information&amp;sg-central-preview=1&amp;plugin=' . $plugin['slug'] . '&amp;TB_iframe=true&amp;width=780&amp;height=680' ) ?>" class="sg-button thickbox open-plugin-details-modal">
            <button class="sg-ripple-container sg-button sg-button--neutral sg-button--medium">
                <span class="sg-button__content">
                    Learn More
                </span>
            </button>
        </a>
		<?php echo $this->maybe_installed( $plugin ); ?>
	</div>
</div>
