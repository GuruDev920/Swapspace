<div class="sg-tabs_content sg-container sg-container--elevation-1 sg-with-padding sg-with-padding--padding-top-none sg-with-padding--padding-right-none sg-with-padding--padding-bottom-none sg-with-padding--padding-left-none" id="upload">
	<form method="post" enctype="multipart/form-data" id="wp-upload-form" class="wp-upload-form" action="<?php echo self_admin_url( 'update.php?action=upload-plugin' ); ?>">
		<div class="sg-drop-area sg-grid sg-grid--gap-medium sg-grid--autoflow-row sg-grid--sm-12 sg-with-padding sg-with-padding--padding-top-responsive sg-with-padding--padding-right-responsive sg-with-padding--padding-bottom-responsive sg-with-padding--padding-left-responsive presentation-box">
			<div class="sg-grid-column--sm-span-2 sg-grid-column--flex sg-grid-column--align-center sg-grid-column--justify-center">
					<span class="sg-icon sg-icon--fill-lighter sg-icon--use-css-colors sg-with-color sg-with-color--color-mint sg-icon--color-mint" style="width: 144px; height: 144px;">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144 144"><rect opacity="0" width="144" height="144" fill="#fff"/><polygon points="74.324 118.385 108.309 118.385 116.806 106.49 116.806 80.151 113.407 76.753 106.61 76.753 106.61 86.099 106.61 76.753 103.212 73.354 96.414 73.354 96.414 81.851 96.414 73.354 93.016 69.956 86.219 69.956 86.219 82.7 86.219 51.264 82.82 47.865 79.422 47.865 76.023 51.264 76.023 97.994 76.023 97.994 76.023 82.7 72.625 79.302 69.226 79.302 65.828 82.7 65.828 109.888 74.324 118.385" class="sgmaincolor"/><path d="M122.478,61.971,116.9,48.652a1,1,0,0,0-.79-.6,1.012,1.012,0,0,0-.921.378l-8.856,11.4a1,1,0,0,0,.658,1.6l14.433,1.914a.974.974,0,0,0,.132.008,1,1,0,0,0,.923-1.385Zm-13.5-2.291,6.737-8.675,4.243,10.131Z"/><path d="M30.129,79.575A6.185,6.185,0,1,0,25.488,90.02h.158a6.185,6.185,0,0,0,4.483-10.446Zm-4.59,8.445a4.185,4.185,0,0,1,.1-8.369l.109,0a4.184,4.184,0,0,1,4.077,4.289A4.208,4.208,0,0,1,25.539,88.02Z"/><path d="M39.554,114.583a.989.989,0,0,0,.469-.118l13.715-7.323a1,1,0,0,0-.941-1.764L39.082,112.7a1,1,0,0,0,.472,1.882Z"/><path d="M58.21,111.56,32.146,125.652a1,1,0,0,0,.952,1.76l26.063-14.093a1,1,0,1,0-.951-1.759Z"/><path d="M58.954,92.931H39.138V18.464H74.521V38.705a1,1,0,0,0,1,1H96.612V62.1a1,1,0,1,0,2,0V38.748c0-.007,0-.014,0-.021s0-.014,0-.022a.98.98,0,0,0-.444-.809L76.231,16.759l-.006,0-.01-.01a.9.9,0,0,0-.128-.083.96.96,0,0,0-.185-.12.99.99,0,0,0-.381-.077H38.138a1,1,0,0,0-1,1V93.931a1,1,0,0,0,1,1H58.954a1,1,0,0,0,0-2ZM76.521,19.816,95.089,37.705H76.521Z"/><path d="M114.114,76.046a1,1,0,0,0-.707-.293h-6.383l-3.105-3.106a1,1,0,0,0-.707-.293H96.828l-3.105-3.105a1,1,0,0,0-.707-.293h-5.8V51.264a1,1,0,0,0-.293-.707l-3.4-3.4a1,1,0,0,0-.707-.293h-3.4a1,1,0,0,0-.707.293l-3.4,3.4a1,1,0,0,0-.293.707V80.286L73.332,78.6a1,1,0,0,0-.707-.293h-3.4a1,1,0,0,0-.707.293l-3.4,3.4a1,1,0,0,0-.293.707v27.189a1,1,0,0,0,.293.707l8.5,8.5a1,1,0,0,0,.707.293H108.31a1,1,0,0,0,.813-.419l8.5-11.895a1,1,0,0,0,.187-.581V80.151a1,1,0,0,0-.293-.707Zm1.692,30.124L107.8,117.385H74.738l-7.91-7.91V83.114L69.641,80.3h2.57l2.812,2.812V97.993a1,1,0,0,0,2,0V51.678l2.813-2.813h2.57l2.813,2.813V82.7a1,1,0,0,0,2,0V70.956H92.6l2.812,2.813v8.082a1,1,0,0,0,2,0v-7.5H102.8l2.812,2.813V86.1a1,1,0,0,0,2,0V77.753h5.383l2.813,2.812Z"/></svg>
					</span>
			</div>
			<div class="sg-grid-column--sm-span-10">
				<h3 class="sg-title sg-title--density-cozy sg-title--level-3 sg-with-color sg-with-color--color-darkest sg-typography sg-typography--weight-bold">
					Upload Your Plugin
				</h3>
				<p class="sg-text sg-text--size-medium sg-with-color sg-with-color--color-dark sg-typography sg-typography--weight-regular sg-upload-label">
					If you have a plugin in a .zip format, you may install it by uploading it here.
				</p>

				<p class="sg-text sg-text--size-medium sg-with-color sg-with-color--color-dark sg-typography sg-typography--weight-regular sg-upload-result">
				</p>
			</div>
		</div>
		<div class="sg-toolbar sg-toolbar--background-light sg-toolbar--density-cozy sg-toolbar--align-center sg-toolbar--justify-flex-end sg-toolbar--direction-row">
			<div class="sg-grid--sm-4">
                <?php wp_nonce_field( 'plugin-upload' ); ?>
				<label for="pluginzip" class="sg-ripple-container sg-button sg-button--neutral sg-button--medium">
					<span class="sg-button__content">Choose file</span>
				</label>
                <button class="sg-ripple-container sg-button sg-button--primary sg-button--medium sg-button--disabled" id="install-button-submit" name="install-button-submit">
                    <span class="sg-button__content">
                        Install Now
                    </span>
                </button>
				<input type="file" id="pluginzip" name="pluginzip" accept=".zip"  style="display: none;" />
			</div>
		</div>
	</form>
</div>
