<?php
namespace SiteGround_Optimizer\Images_Optimizer;

use SiteGround_Optimizer\Supercacher\Supercacher;
use SiteGround_Optimizer\Options\Options;

/**
 * SG Images_Optimizer main plugin class
 */
class Images_Optimizer extends Abstract_Images_Optimizer {

	/**
	 * Array containing options used for status updates.
	 *
	 * @var array
	 */
	public $options_map = array(
		'completed' => 'siteground_optimizer_image_optimization_completed',
		'status'    => 'siteground_optimizer_image_optimization_status',
		'stopped'   => 'siteground_optimizer_image_optimization_stopped',
	);

	/**
	 * The type of image optimization.
	 *
	 * @var string
	 */
	public $type = 'image';

	/**
	 * The total non-optimized images option.
	 *
	 * @var string
	 */
	public $non_optimized = 'siteground_optimizer_total_unoptimized_images';

	/**
	 * The batch name.
	 *
	 * @var string
	 */
	public $batch_skipped = 'siteground_optimizer_is_optimized';

	/**
	 * The ajax action we are using.
	 *
	 * @var string
	 */
	public $action = 'siteground_optimizer_start_image_optimization';

	/**
	 * Array containing all process
	 *
	 * @var array
	 */
	public $process_map = array(
		'filter'   => 'siteground_optimizer_image_optimization_timeout',
		'attempts' => 'siteground_optimizer_optimization_attempts',
		'failed'   => 'siteground_optimizer_optimization_failed',
	);

	/**
	 * The type of cron we want to fire.
	 *
	 * @var string
	 */
	public $cron_type = 'siteground_optimizer_start_image_optimization_cron';

	/**
	 * The process lock we are using.
	 *
	 * @var string
	 */
	public $process_lock = 'siteground_optimizer_image_optimization_lock';

	/**
	 * Optimize the image
	 *
	 * @since  5.0.0
	 *
	 * @param  int   $id       The image id.
	 * @param  array $metadata The image metadata.
	 *
	 * @return bool     True on success, false on failure.
	 */
	public function optimize( $id, $metadata ) {
		// Load the uploads dir.
		$upload_dir = wp_get_upload_dir();
		// Get path to main image.
		$main_image = get_attached_file( $id );
		// Get the basename.
		$basename = basename( $main_image );

		// Get the command placeholder. It will be used by main image and to optimize the different image sizes.
		$status = $this->execute_optimization_command( $main_image );

		// Optimization failed.
		if ( true === boolval( $status ) ) {
			update_post_meta( $id, 'siteground_optimizer_optimization_failed', 1 );
			return false;
		}

		// Check if there are any sizes.
		if ( ! empty( $metadata['sizes'] ) ) {
			// Loop through all image sizes and optimize them as well.
			foreach ( $metadata['sizes'] as $size ) {
				// Replace main image with the cropped image and run the optimization command.
				$status = $this->execute_optimization_command( str_replace( $basename, $size['file'], $main_image ) );

				// Optimization failed.
				if ( true === boolval( $status ) ) {
					update_post_meta( $id, 'siteground_optimizer_optimization_failed', 1 );
					return false;
				}
			}
		}

		// Everything ran smoothly.
		update_post_meta( $id, 'siteground_optimizer_is_optimized', 1 );
		return true;
	}

	/**
	 * Check if image exists and perform optimiation.
	 *
	 * @since  5.0.0
	 *
	 * @param  string $filepath The path to the file.
	 *
	 * @return bool             False on success, true on failure.
	 */
	private function execute_optimization_command( $filepath ) {
		// Bail if the file doens't exists.
		if ( ! file_exists( $filepath ) ) {
			return true;
		}

		// Get image type.
		$type = exif_imagetype( $filepath );

		switch ( $type ) {
			case IMAGETYPE_GIF:
				$placeholder = 'gifsicle -O3 --careful -o %1$s %1$s 2>&1';
				break;

			case IMAGETYPE_JPEG:
				$placeholder = 'jpegoptim -m85 %s 2>&1';
				break;

			case IMAGETYPE_PNG:
				// Bail if the image is bigger than 500k.
				// PNG usage is not recommended and images bigger than 500kb
				// hit the limits.
				if ( filesize( $filepath ) > self::PNGS_SIZE_LIMIT ) {
					return true;
				}
				$placeholder = 'optipng -o2 %s 2>&1';
				break;

			default:
				// Bail if the image type is not supported.
				return true;
		}

		// Optimize the image.
		exec(
			sprintf(
				$placeholder, // The command.
				$filepath // Image path.
			),
			$output,
			$status
		);

		// Create webp copy of the webp is enabled.
		if ( Options::is_enabled( 'siteground_optimizer_webp_support' ) ) {
			Images_Optimizer_Webp::generate_webp_file( $filepath );
		}

		return $status;
	}
}
