<?php
namespace SiteGround_Optimizer\Cli;

use SiteGround_Optimizer\Options\Options;
use SiteGround_Optimizer\Htaccess\Htaccess;
use SiteGround_Optimizer\Images_Optimizer\Images_Optimizer;
/**
 * WP-CLI: wp sg optimize {option} enable/disable.
 *
 * Run the `wp sg optimize {option} enable/disable` command to enable/disable specific plugin functionality.
 *
 * @since 5.0.0
 * @package Cli
 * @subpackage Cli/Cli_Optimizer
 */

/**
 * Define the {@link Cli_Optimizer} class.
 *
 * @since 5.0.0
 */
class Cli_Optimizer {
	/**
	 * Enable specific optimization for SiteGround Optimizer plugin.
	 *
	 * ## OPTIONS
	 *
	 * <optimization>
	 * : Optimization name.
	 * ---
	 * options:
	 *  - dynamic-cache
	 *  - autoflush-cache
	 *  - mobile-cache
	 *  - html
	 *  - js
	 *  - js-async
	 *  - combine-js
	 *  - css
	 *  - combine-css
	 *  - querystring
	 *  - emojis
	 *  - images
	 *  - lazyload
	 *  - gzip
	 *  - webp
	 *  - web-fonts
	 *  - browsercache
	 *  - fix_insecure_content
	 *  - database-optimization
	 *  - dns-prefetch
	 *  - heartbeat-control
	 *	- preload-combined-css
	 * ---
	 * <action>
	 * : The action: enable\disable.
	 * Whether to enable or disable the optimization.
	 *
	 * [--blog_id=<blog_id>]
	 * : Blod id for multisite optimizations
	 */
	public function __invoke( $args, $assoc_args ) {
		$this->option_service   = new Options();
		$this->htaccess_service = new Htaccess();

		$blog_id = ! empty( $assoc_args['blog_id'] ) ? $assoc_args['blog_id'] : false;

		switch ( $args[0] ) {
			case 'dynamic-cache':
			case 'autoflush-cache':
			case 'mobile-cache':
			case 'html':
			case 'js':
			case 'css':
			case 'querystring':
			case 'emojis':
			case 'js-async':
			case 'combine-js':
			case 'combine-css':
			case 'web-fonts':
			case 'webp':
			case 'images':
			case 'dns-prefetch':
			case 'heartbeat-control':
			case 'preload-combined-css':
				return $this->optimize( $args[1], $args[0], $blog_id );
			case 'lazyload':
				return $this->optimize_lazyload( $args[1], $blog_id );
			case 'gzip':
				return $this->optimize_gzip( $args[1] );
			case 'browsercache':
				return $this->optimize_browsercache( $args[1] );
			case 'database-optimization':
				return $this->optimize_database( $args[1] );
		}
	}

	public function validate_multisite( $option, $blog_id = false ) {
		if (
			! \is_multisite() &&
			false !== $blog_id
		) {
			\WP_CLI::error( 'Blog id should be passed to multisite setup only!' );
		}

		if (
			\is_multisite() &&
			false === $blog_id
		) {
			\WP_CLI::error( "Blog id is required for optimizing $option on multisite setup!" );
		}

		if ( function_exists( 'get_sites' ) ) {
			$site = \get_sites( array( 'site__in' => $blog_id ) );

			if ( empty( $site ) ) {
				\WP_CLI::error( 'There is no existing site with id: ' . $blog_id );
			}
		}
	}

	public function optimize( $action, $option, $blog_id = false ) {

		$this->validate_multisite( $option, $blog_id );

		$mapping = array(
			'dynamic-cache'        => 'siteground_optimizer_enable_cache',
			'autoflush-cache'      => 'siteground_optimizer_autoflush_cache',
			'mobile-cache'         => 'siteground_optimizer_user_agent_header',
			'html'                 => 'siteground_optimizer_optimize_html',
			'js'                   => 'siteground_optimizer_optimize_javascript',
			'js-async'             => 'siteground_optimizer_optimize_javascript_async',
			'css'                  => 'siteground_optimizer_optimize_css',
			'combine-css'          => 'siteground_optimizer_combine_css',
			'combine-js'           => 'siteground_optimizer_combine_javascript',
			'querystring'          => 'siteground_optimizer_remove_query_strings',
			'emojis'               => 'siteground_optimizer_disable_emojis',
			'images'               => 'siteground_optimizer_optimize_images',
			'webp'                 => 'siteground_optimizer_webp_support',
			'fix_insecure_content' => 'siteground_optimizer_fix_insecure_content',
			'dns-prefetch'         => 'siteground_optimizer_dns_prefetch',
			'heartbeat-control'    => 'siteground_optimizer_heartbeat_control',
			'preload-combined-css' => 'siteground_optimizer_preload_combined_css',
		);

		switch ( $action ) {
			case 'enable':
				if ( false === $blog_id ) {
					$result = $this->option_service::enable_option( $mapping[ $option ] );
				} else {
					$result = $this->option_service::enable_mu_option( $blog_id, $mapping[ $option ] );
				}
				$type = true;
				break;

			case 'disable':
				if ( false === $blog_id ) {
					$result = $this->option_service::disable_option( $mapping[ $option ] );
				} else {
					$result = $this->option_service::disable_mu_option( $blog_id, $mapping[ $option ] );
				}

				$type = false;
				break;
		}

		if ( ! isset( $result ) ) {
			\WP_CLI::error( 'Please specify action' );
		}

		$message = $this->option_service->get_response_message( $result, $mapping[ $option ], $type );

		return true === $result ? \WP_CLI::success( $message ) : \WP_CLI::error( $message );

	}

	public function optimize_lazyload( $action, $blog_id=false ) {
		$this->validate_multisite( 'lazyload', $blog_id );

		$options = array(
			'siteground_optimizer_lazyload_images',
			'siteground_optimizer_lazyload_gravatars',
			'siteground_optimizer_lazyload_thumbnails',
			'siteground_optimizer_lazyload_responsive',
			'siteground_optimizer_lazyload_textwidgets',
			'siteground_optimizer_lazyload_woocommerce',
			'siteground_optimizer_lazyload_shortcodes',
			'siteground_optimizer_lazyload_videos',
			'siteground_optimizer_lazyload_iframes',
		);

		$status = array();

		foreach ( $options as $option ) {
			if ( 'enable' === $action ) {
				if ( false === $blog_id ) {
					$status[] = Options::enable_option( $option );
				} else {
					$status[] = Options::enable_mu_option( $blog_id, $option );
				}
			} else {
				if ( false === $blog_id ) {
					$status[] = Options::disable_option( $option );
				} else {
					$status[] = Options::disable_mu_option( $blog_id, $option );
				}
			}
		}

		if ( in_array( false, $status ) ) {
			return \WP_CLI::error( 'Could not ' . ucwords( $action ) . ' Lazy Loading Images' );
		}

		return \WP_CLI::success( 'Lazy Loading Images ' . ucwords( $action ) );

	}
	
	/**
	 * Enable/disable Datbase Optimization
	 *
	 * @since  5.6.1
	 *
	 * @param  string $action Enable/disable the option
	 *
	 */
	public function optimize_database ( $action ) {
		if ( 'enable' === $action ) {
			// Check if there is a scheduled event.
			if ( ! wp_next_scheduled( 'siteground_optimizer_database_optimization_cron' ) ) {
				// Set the event if it is not running.
				$response = wp_schedule_event( time(), 'weekly', 'siteground_optimizer_database_optimization_cron' );
			}
			// Check if the event was scheduled.
			if ( false === $response ) {
				return \WP_CLI::error( 'Could not schedule the automatic optimization. Please, try again,' );
			}
			// Enable the option.
			$result = Options::enable_option( 'siteground_optimizer_database_optimization' );
			$type = true;
		} else {
			// Remove the scheduled event.
			wp_clear_scheduled_hook( 'siteground_optimizer_database_optimization_cron' );
			// Disable the option.
			$result = Options::disable_option( 'siteground_optimizer_database_optimization' );
			$type = false;
		}
		// Set the message.
		$message = $this->option_service->get_response_message( $result, 'siteground_optimizer_database_optimization', $type );

		return true === $result ? \WP_CLI::success( $message ) : \WP_CLI::error( $message );
	}

	public function optimize_gzip( $action ) {
		if ( 'enable' === $action ) {
			$result = $this->htaccess_service->enable( 'gzip' );
			true === $result ? Options::enable_option( 'siteground_optimizer_enable_gzip_compression' ) : '';
			$type = true;
		} else {
			$result = $this->htaccess_service->disable( 'gzip' );
			true === $result ? Options::disable_option( 'siteground_optimizer_enable_gzip_compression' ) : '';
			$type = false;
		}

		$message = $this->option_service->get_response_message( $result, 'siteground_optimizer_enable_gzip_compression', $type );

		return true === $result ? \WP_CLI::success( $message ) : \WP_CLI::error( $message );
	}

	public function optimize_browsercache( $action ) {
		if ( 'enable' === $action ) {
			$result = $this->htaccess_service->enable( 'browser-caching' );
			true === $result ? Options::enable_option( 'siteground_optimizer_enable_browser_caching' ) : '';
			$type = true;
		} else {
			$result = $this->htaccess_service->disable( 'browser-caching' );
			true === $result ? Options::disable_option( 'siteground_optimizer_enable_browser_caching' ) : '';
			$type = false;
		}

		$message = $this->option_service->get_response_message( $result, 'siteground_optimizer_enable_browser_caching', $type );

		return true === $result ? \WP_CLI::success( $message ) : \WP_CLI::error( $message );
	}

	public function optimize_images( $blog_id = false ) {
		$this->image_optimizer  = new Images_Optimizer();

		$this->validate_multisite( 'images', $blog_id );

		// Switch to blog for multisite setup.
		if ( false !== $blog_id ) {
			\switch_to_blog( $blog_id );
		}

		// Get all images.
		$images = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array(
					// Skip optimized images.
					array(
						'key'     => 'siteground_optimizer_is_optimized',
						'compare' => 'NOT EXISTS',
					),
					// Also skip failed optimizations.
					array(
						'key'     => 'siteground_optimizer_optimization_failed',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		\WP_CLI::log( 'Start Image Optimization');

		// Add empty line.
		\WP_CLI::log( '' );

		$progress = \WP_CLI\Utils\make_progress_bar( 'Optimizing images', count( $images ) );
		// Loop through all images and optimize them.
		foreach ( $images as $id ) {
			// Keep track of the number of times we've attempted to optimize the image.
			$count = (int) get_post_meta( $id, 'count', true );

			if ( $count > 1 ) {
				update_post_meta( $id, 'siteground_optimizer_optimization_failed', 1 );
				continue;
			}

			update_post_meta( $id, 'count', $count + 1 );

			// Get attachment metadata.
			$metadata = wp_get_attachment_metadata( $id );

			// Optimize the main image and the other image sizes.
			$status = $this->image_optimizer->optimize_image( $id, $metadata );

			// Mark image if the optimization failed.
			if ( false === $status ) {
				update_post_meta( $id, 'siteground_optimizer_optimization_failed', 1 );
			}

			// Mark the image as optimized.
			update_post_meta( $id, 'siteground_optimizer_is_optimized', 1 );

			$progress->tick();
		}

		$progress->finish();

		\WP_CLI::success( 'Images optimization completed.' );
	}
}
