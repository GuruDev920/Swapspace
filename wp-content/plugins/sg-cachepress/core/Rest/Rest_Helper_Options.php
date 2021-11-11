<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Options\Options;
use SiteGround_Optimizer\Multisite\Multisite;
use SiteGround_Optimizer\Front_End_Optimization\Front_End_Optimization;
use SiteGround_Optimizer\Helper\Helper;
use SiteGround_Optimizer\Htaccess\Htaccess;
use SiteGround_Optimizer\Analysis\Analysis;

/**
 * Rest Helper class that manages all of the front end optimisation.
 */
class Rest_Helper_Options extends Rest_Helper {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->options          = new Options();
		$this->multisite        = new Multisite();
		$this->htaccess_service = new Htaccess();
		$this->analysis         = new Analysis();
	}
	/**
	 * Checks if the option key exists.
	 *
	 * @since  5.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function enable_option_from_rest( $request ) {
		// Get the option key.
		$key        = $this->validate_and_get_option_value( $request, 'option_key' );
		$is_network = $this->validate_and_get_option_value( $request, 'is_multisite', false );
		$result     = $this->options->enable_option( $key, $is_network );

		$this->maybe_change_htaccess_rules( $key, 1 );

		// Enable the option.
		wp_send_json(
			array(
				'success' => $result,
				'data'    => array(
					'message' => $this->options->get_response_message( $result, $key, true ),
				),
			)
		);
	}

	/**
	 * Checks if the option key exists.
	 *
	 * @since  5.0.0
	 *
	 * @param  object $request Request data.
	 *
	 * @return string The option key.
	 */
	public function disable_option_from_rest( $request ) {
		// Get the option key.
		$key        = $this->validate_and_get_option_value( $request, 'option_key' );
		$is_network = $this->validate_and_get_option_value( $request, 'is_multisite', false );
		$result     = $this->options->disable_option( $key, $is_network );

		$this->maybe_change_htaccess_rules( $key, 0 );

		// Disable the option.
		return wp_send_json(
			array(
				'success' => $result,
				'data'    => array(
					'message' => $this->options->get_response_message( $result, $key, false ),
				),
			)
		);
	}

	/**
	 * Checks if the option key exists.
	 *
	 * @since  5.5.0
	 *
	 * @param  object $request Request data.
	 *
	 * @return string The option key.
	 */
	public function change_option_from_rest( $request ) {
		$allowed_options = array(
			'siteground_optimizer_quality_webp',
			'siteground_optimizer_quality_type',
			'siteground_optimizer_quality_images',
			'siteground_optimizer_heartbeat_dashboard_interval',
			'siteground_optimizer_heartbeat_post_interval',
			'siteground_optimizer_heartbeat_frontend_interval',
		);

		// Get the option key.
		$key = $this->validate_and_get_option_value( $request, 'option_key' );

		// Bail if the option is now allowed.
		if ( ! in_array( $key, $allowed_options ) ) {
			wp_send_json_error();
		}

		$value      = $this->validate_and_get_option_value( $request, 'value' );
		$is_network = $this->validate_and_get_option_value( $request, 'is_multisite', false );
		$result     = $this->options->change_option( $key, $value, $is_network );

		// Chnage the option.
		return wp_send_json(
			array(
				'success' => $result,
			)
		);
	}

	/**
	 * Provide all plugin options.
	 *
	 * @since  5.0.0
	 */
	public function fetch_options() {
		// Fetch the options.
		$options = $this->options->fetch_options();

		if ( is_multisite() ) {
			$options['sites_data'] = $this->multisite->get_sites_info();
		}
		$options['has_images']                  = $this->options->check_for_images();
		$options['has_images_for_optimization'] = $this->options->check_for_unoptimized_images( 'image' );
		$options['assets']                      = Front_End_Optimization::get_instance()->get_assets();
		$options['quality_type']                = get_option( 'siteground_optimizer_quality_type', '' );
		$options['post_types']                  = $this->options->get_post_types();
		$options['previous_tests']              = $this->analysis->rest_get_test_results();

		// Check for non converted images when we are on avalon server.
		if ( Helper::is_siteground() ) {
			$options['has_images_for_conversion'] = $this->options->check_for_unoptimized_images( 'webp' );
		}

		// Send the options to react app.
		wp_send_json_success( $options );
	}

	/**
	 * Check if we should add additional rules to the htaccess file.
	 *
	 * @since  5.7.14
	 *
	 * @param  string $type  The optimization type.
	 * @param  int    $value The optimization value.
	 */
	public function maybe_change_htaccess_rules( $type, $value ) {
		// Options mapping with the htaccess rules and methods.
		$htaccess_options = array(
			'siteground_optimizer_enable_gzip_compression' => array(
				0      => 'disable',
				1      => 'enable',
				'rule' => 'gzip',
			),
			'siteground_optimizer_enable_browser_caching'  => array(
				0      => 'disable',
				1      => 'enable',
				'rule' => 'browser-caching',
			),
			'siteground_optimizer_user_agent_header'       => array(
				0      => 'enable',
				1      => 'disable',
				'rule' => 'user-agent-vary',
			),
		);

		// Bail if the option doesn't require additional htaccess rules to be added.
		if ( ! array_key_exists( $type, $htaccess_options ) ) {
			return;
		}

		// Call the htaccess method to add/remove the rules.
		call_user_func_array(
			array( $this->htaccess_service, $htaccess_options[ $type ][ $value ] ),
			array( $htaccess_options[ $type ]['rule'] )
		);
	}
}
