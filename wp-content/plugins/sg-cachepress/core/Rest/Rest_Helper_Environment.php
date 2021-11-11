<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Ssl\Ssl;
use SiteGround_Optimizer\Options\Options;
/**
 * Rest Helper class that manages enviroment optimisation settings.
 */
class Rest_Helper_Environment extends Rest_Helper {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ssl     = new Ssl();
		$this->options = new Options();
	}

	/**
	 * Enable the ssl
	 *
	 * @param  object $request Request data.
	 *
	 * @since  5.0.0
	 */
	public function enable_ssl( $request ) {
		$key    = $this->validate_and_get_option_value( $request, 'option_key' );
		// Bail if the domain doens't nove ssl certificate.
		if ( ! $this->ssl->has_certificate() ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please, install an SSL certificate first!', 'sg-cachepress' ),
				)
			);
		}

		$result = $this->ssl->enable();

		wp_send_json(
			array(
				'success' => $result,
				'data' => array(
					'message' => $this->options->get_response_message( $result, $key, true ),
				),
			)
		);
	}

	/**
	 * Disable the ssl.
	 *
	 * @param  object $request Request data.
	 *
	 * @since  5.0.0
	 */
	public function disable_ssl( $request ) {
		$key    = $this->validate_and_get_option_value( $request, 'option_key' );
		$result = $this->ssl->disable();

		wp_send_json(
			array(
				'success' => $result,
				'data' => array(
					'message' => $this->options->get_response_message( $result, $key, false ),
				),
			)
		);
	}

	/**
	 * Enable the Database Optimization.
	 *
	 * @since  5.6.0
	 */
	public function enable_database_optimization() {
		$key    = 'siteground_optimizer_database_optimization';
		// Update the option in the database.
		$result = $this->options->enable_option( $key );

		// Check if the event is currently runing.
		if ( ! wp_next_scheduled( 'siteground_optimizer_database_optimization_cron' ) ) {
			wp_schedule_event( time(), 'weekly', 'siteground_optimizer_database_optimization_cron' );
		}

		wp_send_json(
			array(
				'success' => $result,
				'data' => array(
					'message' => $this->options->get_response_message( $result, $key, true ),
				),
			)
		);
	}

	/**
	 * Disable the Dabase Optimisation.
	 *
	 * @since  5.6.0
	 */
	public function disable_database_optimization() {
		$key    = 'siteground_optimizer_database_optimization';
		// Disable the option in the database.
		$result = $this->options->disable_option( $key );
		// Remove the cron job.
		wp_clear_scheduled_hook( 'siteground_optimizer_database_optimization_cron' );

		wp_send_json(
			array(
				'success' => $result,
				'data' => array(
					'message' => $this->options->get_response_message( $result, $key, false ),
				),
			)
		);
	}
}
