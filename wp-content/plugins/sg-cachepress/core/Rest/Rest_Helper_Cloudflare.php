<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\DNS\Cloudflare;
use SiteGround_Optimizer\Supercacher\Supercacher;
/**
 * Rest Helper class that manages cloudflare options.
 */
class Rest_Helper_Cloudflare extends Rest_Helper {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->cloudflare = new Cloudflare();
	}

	/**
	 * Authenticate Cloudflare
	 *
	 * @param  object $request Request data..
	 *
	 * @since  5.7.0
	 */
	public function authenticate( $request ) {
		$email = $this->validate_and_get_option_value( $request, 'email' );
		$key   = $this->validate_and_get_option_value( $request, 'key' );

		update_option( 'siteground_optimizer_cloudflare_email', $email );
		update_option( 'siteground_optimizer_cloudflare_auth_key', $key );

		$result = $this->cloudflare->add_worker();

		// Purge the cache.
		Supercacher::purge_cache();

		wp_send_json_success( array(
			'data' => array(
				'cloudflare_email'               => $email,
				'cloudflare_auth_key'            => $key,
				'cloudflare_optimization_status' => 1,
			),
			'message' => 'Cloudflare optimization enabled.',
		) );
	}

	/**
	 * Purge the cloudflare cache and send json response
	 *
	 * @since  5.7.0
	 */
	public function purge_cloudflare_cache_from_rest() {
		// Purge the cache.
		Supercacher::purge_cache();
		// Disable the option.
		wp_send_json_success();
	}

	/**
	 * Deauthenticate Cloudflare.
	 *
	 * @since  5.7.0
	 */
	public function deauthenticate() {
		$result = Cloudflare::get_instance()->remove_worker();

		delete_option( 'siteground_optimizer_cloudflare_email' );
		delete_option( 'siteground_optimizer_cloudflare_auth_key' );
		delete_option( 'siteground_optimizer_cloudflare_zone_id' );

		update_option( 'siteground_optimizer_cloudflare_optimization_status', 0 );

		// Purge the cache.
		Supercacher::purge_cache();

		wp_send_json_success( array(
			'data' => array(
				'cloudflare_email' => '',
				'cloudflare_auth_key' => '',
				'cloudflare_optimization_status' => 0,
			),
			'message' => __( 'Cloudflare successfully deauthenticated', 'sg-cachepress' ),
		) );
	}

}
