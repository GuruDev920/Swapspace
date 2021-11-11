<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Images_Optimizer\Images_Optimizer;
use SiteGround_Optimizer\Options\Options;
/**
 * Rest Helper class that manages image optimisation  settings.
 */
class Rest_Helper_Images {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->options          = new Options();
		$this->images_optimizer = new Images_Optimizer();
	}

	/**
	 * Initialize images optimization
	 *
	 * @since  5.0.0
	 */
	public function optimize_images() {
		$this->images_optimizer->initialize();

		wp_send_json_success(
			array(
				'image_optimization_status'   => 0,
				'image_optimization_stopped'  => 0,
				'has_images_for_optimization' => get_option( 'siteground_optimizer_total_unoptimized_images', 0 ),
				'total_unoptimized_images'    => get_option( 'siteground_optimizer_total_unoptimized_images', 0 ),
			)
		);
	}

	/**
	 * Stops images optimization
	 *
	 * @since  5.0.8
	 */
	public function stop_images_optimization() {
		// Clear the scheduled cron after the optimization is completed.
		wp_clear_scheduled_hook( 'siteground_optimizer_start_image_optimization_cron' );

		// Update the status to finished.
		update_option( 'siteground_optimizer_image_optimization_completed', 1, false );
		update_option( 'siteground_optimizer_image_optimization_status', 1, false );
		update_option( 'siteground_optimizer_image_optimization_stopped', 1, false );

		// Delete the lock.
		delete_option( 'siteground_optimizer_image_optimization_lock' );

		wp_send_json_success(
			array(
				'image_optimization_status'   => 1,
				'image_optimization_stopped'  => 1,
				'has_images_for_optimization' => $this->options->check_for_unoptimized_images( 'image' ),
			)
		);
	}

	/**
	 * Return the status of current compatibility check.
	 *
	 * @since  5.0.0
	 */
	public function check_image_optimizing_status() {
		$unoptimized_images = $this->options->check_for_unoptimized_images( 'image' );

		if ( 0 === $unoptimized_images ) {
			$this->images_optimizer->complete();
		}

		$status = (int) get_option( 'siteground_optimizer_image_optimization_completed', 0 );

		wp_send_json_success(
			array(
				'image_optimization_status'   => $status,
				'has_images_for_optimization' => $unoptimized_images,
				'total_unoptimized_images'    => (int) get_option( 'siteground_optimizer_total_unoptimized_images' ),
			)
		);
	}

	/**
	 * Deletes images meta_key flag to allow reoptimization.
	 *
	 * @since  5.0.0
	 */
	public function reset_images_optimization() {
		$this->images_optimizer->reset_image_optimization_status();

		wp_send_json_success();
	}
}
