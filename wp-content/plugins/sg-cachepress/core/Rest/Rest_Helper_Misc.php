<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Supercacher\Supercacher;
use SiteGround_Optimizer\Analysis\Analysis;
/**
 * Rest Helper class that manages misc rest routes  settings.
 */
class Rest_Helper_Misc extends Rest_Helper {

	/**
	 * Hide the rating box
	 *
	 * @since  5.0.12
	 */
	public function handle_hide_rating() {
		// Hide the rating box.
		update_option( 'siteground_optimizer_hide_rating', 1 );
		update_site_option( 'siteground_optimizer_hide_rating', 1 );

		// Send the response.
		wp_send_json_success();
	}

	/**
	 * Update exclude list.
	 *
	 * @since  5.2.0
	 *
	 * @param  object $request Request data.
	 */
	public function update_exclude_list( $request ) {
		// List of predefined exclude lists.
		$exclude_lists = array(
			'minify_javascript_exclude',
			'async_javascript_exclude',
			'minify_css_exclude',
			'minify_html_exclude',
			'excluded_lazy_load_classes',
			'combine_css_exclude',
			'dns_prefetch_urls',
			'combine_javascript_exclude',
			'fonts_preload_urls',
			'post_types_exclude',
		);

		// Get the type and handles data from the request.
		$type   = $this->validate_and_get_option_value( $request, 'type' );
		$handle = $this->validate_and_get_option_value( $request, 'handle' );

		// Bail if the type is not listed in the predefined exclude list.
		if ( ! in_array( $type, $exclude_lists ) ) {
			wp_send_json_error();
		}

		$handles = get_option( 'siteground_optimizer_' . $type, array() );
		$key     = array_search( $handle, $handles );

		if ( false === $key ) {
			array_push( $handles, $handle );
		} else {
			unset( $handles[ $key ] );
		}

		$handles = array_values( $handles );

		if ( in_array( $type, array( 'minify_html_exclude', 'excluded_lazy_load_classes', 'dns_prefetch_urls', 'fonts_preload_urls' ) ) ) {
			$handles = $handle;
		}

		// Update the option.
		$result = update_option( 'siteground_optimizer_' . $type, $handles );

		// Purge the cache.
		Supercacher::purge_cache();

		// Send response to the react app.
		wp_send_json(
			array(
				'success' => $result,
				'handles' => $handles,
			)
		);
	}

	/**
	 * Disable specific optimizations for a blog.
	 *
	 * @since  5.4.0
	 *
	 * @param  object $request Request data.
	 */
	public function run_analysis( $request ) {

		// Get the required params.
		$device = $this->validate_and_get_option_value( $request, 'device' );
		$url    = $this->validate_and_get_option_value( $request, 'url', false );

		// Bail if any of the parameters is empty.
		if ( empty( $device ) ) {
			wp_send_json_error();
		}

		$analysis = new Analysis();
		$analysis->run_analysis( $url, $device );
		$result = $analysis->rest_get_test_results();

		// Send the response.
		wp_send_json_success( $result );
	}
}
