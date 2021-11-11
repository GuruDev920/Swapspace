<?php
namespace SiteGround_Central\Rest;

use SiteGround_Central\Installer\Installer;
use SiteGround_Central\Importer\Importer;

/**
 * SG Central Rest class.
 */
class Rest {
	/**
	 * The Rest Namespace.
	 */
	const REST_NAMESPACE = 'siteground-wizard/v1';

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		$this->installer = new Installer();
		$this->importer = new Importer();
	}

	/**
	 * Check if a given request has admin access
	 *
	 * @since  5.0.13
	 *
	 * @param  WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function check_permissions( $request ) {
		return current_user_can( 'activate_plugins' );
	}

	/**
	 * Register rest routes.
	 *
	 * @since  1.0.0
	 */
	public function register_rest_routes() {
		// Register Helper routes.
		register_rest_route(
			self::REST_NAMESPACE, '/update-visibility/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'update_visibility' ),
				'permission_callback' => '__return_true',

			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/reset/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'reset' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		// Register instaler routes.
		register_rest_route(
			self::REST_NAMESPACE, '/prepare/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'prepare' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
		register_rest_route(
			self::REST_NAMESPACE, '/install/', array(
				'methods'  => 'POST',
				'callback' => array( $this->installer, 'install' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
		register_rest_route(
			self::REST_NAMESPACE, '/complete/', array(
				'methods'  => 'POST',
				'callback' => array( $this->installer, 'complete' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
		// Register importer routes.
		register_rest_route(
			self::REST_NAMESPACE, '/import-sample-data/', array(
				'methods'  => 'POST',
				'callback' => array( $this->importer, 'pre_import' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Update the wizard activation redirect.
	 *
	 * @since  1.0.0
	 */
	public function update_visibility() {
		update_option( 'siteground_wizard_activation_redirect', 'no' );
		wp_send_json_success();
	}

	/**
	 * Handle plugin install request.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function prepare( $request ) {
		// Get the data.
		$data = json_decode( $request->get_body(), true );

		if ( empty( $data ) ) {
			wp_send_json_error();
		}

		update_option( 'siteground_wizard_installation_queue', $data );

		// Reset the site.
		$this->reset();

		// Notify the api, that everything is ok with provided data.
		wp_send_json_success();
	}

	/**
	 * Empty the site.
	 *
	 * @since  1.0.0
	 */
	public function reset() {
		// Reset the site.
		exec( 'wp site empty --yes' );
		// Notify the api, that everything is ok with provided data.
		wp_send_json_success();
	}
}
