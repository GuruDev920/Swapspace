<?php
namespace SiteGround_Optimizer\Cli;

use SiteGround_Optimizer\Options\Options;
/**
 * WP-CLI: wp sg heartbeat {setting} value.
 *
 * Run the `wp sg heartbeat {setting} {option} {frequency}` command to change the settgins of specific plugin functionality.
 *
 * @since 5.6.1
 * @package Cli
 * @subpackage Cli/Heartbeat
 */

/**
 * Define the {@link Cli_Heartbeat} class.
 *
 * @since 5.6.1
 */
class Cli_Heartbeat {
	/**
	 * Enable specific setting for SiteGround Optimizer plugin.
	 *
	 * ## OPTIONS
	 *
	 * <location>
	 * : Setting name.
	 * ---
	 * options:
	 *  - frontend
	 *  - dashboard
	 *  - post
	 * ---
	 * <action>
	 * : Setting name.
	 * [--frequency=<frequency>]
	 * : Frequency for the Heartbeat.
	 */
	public function __invoke( $args, $assoc_args ) {
		// Chek if Heartbeat optimization is enabled.
		if ( ! Options::is_enabled( 'siteground_optimizer_heartbeat_control' ) ) {
			return \WP_CLI::warning( 'Heartbeat optimization is disabled, to activate it use `wp sg optimize heartbeat-control enable`' );
		}

		// Set location based on cli command.
		$status_option = 'siteground_optimizer_heartbeat_' . $args[0] . '_status';
		$interval_option = 'siteground_optimizer_heartbeat_' . $args[0] . '_interval';

		if ( 'enable' === $args[1] ) {
			// Bail if the frequency param is missing.
			if ( empty( $assoc_args['frequency'] ) ) {
				\WP_CLI::error( 'Please, use the frequency argument with the command - wp sg heartbeat ' . $args[0] . ' enable --frequency=integer.' );
			}

			$frequency = round( intval( $assoc_args['frequency'] ) );

			// Check if frequency is within the interval.
			if ( $frequency < 15 || $frequency > 300 ) {
				\WP_CLI::error( 'Frequency ' . $frequency . ' is not supported. Please choose a number between 15 and 300' );
			}

			// Set the interval frequency.
			update_option( $interval_option, $frequency );

			// Enable the heartbeat specific optimization.
			$result = Options::enable_option( $status_option );

			$message = 'Heartbeat optimization for ' . $args[0] . ' was set successfully.';
		} elseif ( 'disable' === $args[1] ) {
			// Enable the heartbeat specific optimization.
			$result = Options::disable_option( $status_option );

			$message = 'Heartbeat optimization for ' . $args[0] . ' was disabled';
		} else {
			$message = 'Unsupported argument ' . $args[1];
		}

		return true === $result ? \WP_CLI::success( $message ) : \WP_CLI::error( $message );
	}
}
