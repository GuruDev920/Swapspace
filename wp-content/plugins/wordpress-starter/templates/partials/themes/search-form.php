<div class="sg-central-tab-content sg-flex sg-flex--align-center sg-flex--flex-wrap-nowrap sg-settings">
	<div class="sg-grid sg-grid--gap-xx-small sg-grid--autoflow-column sg-tab-content">
		<form class="search-form sg-search-form">
			<select name="type" id="typeselector">
				<option value="search" <?php selected( 'search', $type ); ?>><?php _e( 'Keyword' ); ?></option>
				<option value="author" <?php selected( 'author', $type ); ?>><?php _e( 'Author' ); ?></option>
				<option value="tag" <?php selected( 'tag', $type ); ?>><?php _ex( 'Tag', 'Theme Installer' ); ?></option>
			</select>

			<label class="sg-field-label">
				<span class="sg-field-wrapper sg-field-wrapper--show-validation sg-field-wrapper--small">
					<input type="search" name="s" id="s" size="30" value="" autofocus="autofocus" class="sg-field">
				</span>
			</label>

			<button class="sg-central-tab-content sg-ripple-container sg-button sg-button--secondary sg-button--small sg-button-search">
				<span class="sg-button__content">
					Search
				</span>
			</button>
		</form>
	</div>
</div>
