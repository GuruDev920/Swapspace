<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Supercacher\Supercacher;
use SiteGround_Optimizer\Options\Options;
use SiteGround_Optimizer\Memcache\Memcache;
/**
 * Rest Helper class that manages caching options.
 */
class Rest_Helper_Cache extends Rest_Helper {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->memcache = new Memcache();
	}

	/**
	 * Enable memcached.
	 *
	 * @since  5.0.0
	 */
	public function enable_memcache() {
		$port = $this->memcache->get_memcached_port();

		if ( empty( $port ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'SiteGround Optimizer was unable to connect to the Memcached server and it was disabled. Please, check your SiteGround control panel and turn it on if disabled.', 'sg-cachepress' ),
				)
			);
		}

		// First enable the option.
		$result = Options::enable_option( 'siteground_optimizer_enable_memcached' );

		// Remove notices.
		Options::disable_option( 'siteground_optimizer_memcache_notice' );
		Options::disable_option( 'siteground_optimizer_memcache_crashed' );
		Options::disable_option( 'siteground_optimizer_memcache_dropin_crashed' );

		// Send success if the dropin has been created.
		if ( $result && $this->memcache->create_memcached_dropin() ) {
			wp_send_json_success(
				array(
					'message' => __( 'Memcached Enabled', 'sg-cachepress' ),
				)
			);
		} else {
			if ( 11211 === $port ) {
				wp_send_json_error(
					array(
						'message' => __( 'SiteGround Optimizer was unable to connect to the Memcached server and it was disabled. Please, check your SiteGround control panel and turn it on if disabled.', 'sg-cachepress' ),
					)
				);
			}
		}

		// Dropin cannot be created.
		wp_send_json_error(
			array(
				'message' => __( 'Could Not Enable Memcache!', 'sg-cachepress' ),
			)
		);
	}

	/**
	 * Disable memcached.
	 *
	 * @since  5.0.0
	 */
	public function disable_memcache() {
		// First disable the option.
		$result = Options::disable_option( 'siteground_optimizer_enable_memcached' );

		// Send success if the option has been disabled and the dropin doesn't exist.
		if ( ! $this->memcache->dropin_exists() ) {
			wp_send_json_success(
				array(
					'message' => __( 'Memcached Disabled!', 'sg-cachepress' ),
				)
			);
		}

		// Try to remove the dropin.
		$is_dropin_removed = $this->memcache->remove_memcached_dropin();

		// Remove notices.
		Options::disable_option( 'siteground_optimizer_memcache_notice' );
		Options::disable_option( 'siteground_optimizer_memcache_crashed' );
		Options::disable_option( 'siteground_optimizer_memcache_dropin_crashed' );

		// Send success if the droping has been removed.
		if ( $is_dropin_removed ) {
			wp_send_json_success(
				array(
					'message' => __( 'Memcached Disabled!', 'sg-cachepress' ),
				)
			);
		}

		// The dropin cannot be removed.
		wp_send_json_error(
			array(
				'message' => __( 'Could Not Disable Memcache!', 'sg-cachepress' ),
			)
		);
	}

	/**
	 * Update excluded urls.
	 *
	 * @since  5.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function update_excluded_urls( $request ) {
		$data = $this->validate_and_get_option_value( $request, 'excluded_urls' );

		// Convert the json urls to array.
		$urls = json_decode( $data, true );

		// Update the option.
		$result = update_option( 'siteground_optimizer_excluded_urls', $urls );

		// Purge the cache.
		Supercacher::purge_cache();

		wp_send_json(
			array(
				'success' => $result,
				'data'    => $urls,
			)
		);
	}

	/**
	 * Update excluded post types.
	 *
	 * @since  5.7.0
	 *
	 * @param  object $request Request data.
	 */
	public function update_excluded_post_types( $request ) {
		$data = $this->validate_and_get_option_value( $request, 'excluded_post_types' );

		// Convert the json urls to array.
		$post_types = json_decode( $data, true );

		// Update the option.
		$result = update_option( 'siteground_optimizer_excluded_post_types', $post_types );

		wp_send_json(
			array(
				'success' => $result,
				'data'    => $post_types,
			)
		);
	}

	/**
	 * Test if url is cached.
	 *
	 * @since  5.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function test_cache( $request ) {
		// Get the url.
		$url           = $this->validate_and_get_option_value( $request, 'url' );
		$is_cloudflare = $this->validate_and_get_option_value( $request, 'isCloudflare', false );
		// Check if the url is cached.
		$is_cached = Supercacher::test_cache( $url, true, (bool) $is_cloudflare );
		// Send response to the app.
		wp_send_json_success( array( 'cached' => $is_cached ) );
	}

	/**
	 * Purge the cache and send json response
	 *
	 * @since  5.0.0
	 */
	public function purge_cache_from_rest() {
		Supercacher::purge_cache();
		// Disable the option.
		wp_send_json_success();
	}
}
