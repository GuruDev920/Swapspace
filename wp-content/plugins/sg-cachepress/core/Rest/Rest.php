<?php
namespace SiteGround_Optimizer\Rest;

use SiteGround_Optimizer\Helper\Factory_Trait;

/**
 * Handle PHP compatibility checks.
 */
class Rest {
	use Factory_Trait;

	const REST_NAMESPACE = 'siteground-optimizer/v1';

	/**
	 * Dependencies.
	 *
	 * @since 5.9.0
	 *
	 * @var array
	 */
	public $dependencies = array(
		'webp'        => 'rest_helper_webp',
		'options'     => 'rest_helper_options',
		'cache'       => 'rest_helper_cache',
		'multisite'   => 'rest_helper_multisite',
		'misc'        => 'rest_helper_misc',
		'images'      => 'rest_helper_images',
		'environment' => 'rest_helper_environment',
		'cloudflare'  => 'rest_helper_cloudflare',
	);

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load the main plugin dependencies.
	 *
	 * @since  5.9.0
	 */
	public function load_dependencies() {
		foreach ( $this->dependencies as $dependency => $classes ) {
			$this->factory( 'rest', $classes );
		}
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
	 * @since  5.0.0
	 */
	public function register_rest_routes() {
		foreach ( $this->dependencies as $dependency => $classes) {
			call_user_func( array( $this, 'register_' . $dependency . '_rest_routes' ) );
		}
	}

	/**
	 * Register php and ssl rest routes.
	 *
	 * @since  5.4.0
	 */
	public function register_environment_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/enable-ssl/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_environment, 'enable_ssl' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/disable-ssl/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_environment, 'disable_ssl' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/enable-database-optimization/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_environment, 'enable_database_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
		register_rest_route(
			self::REST_NAMESPACE, '/disable-database-optimization/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_environment, 'disable_database_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register options rest routes.
	 *
	 * @since  5.4.0
	 */
	public function register_options_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/enable-option/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_options, 'enable_option_from_rest' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/disable-option/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_options, 'disable_option_from_rest' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/fetch-options/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_options, 'fetch_options' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/change-option/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_options, 'change_option_from_rest' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register cache rest routes.
	 *
	 * @since  5.4.0
	 */
	public function register_cache_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/update-excluded-urls/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_cache, 'update_excluded_urls' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/test-url-cache/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_cache, 'test_cache' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/purge-cache/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_cache, 'purge_cache_from_rest' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/enable-memcache/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_cache, 'enable_memcache' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/disable-memcache/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_cache, 'disable_memcache' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

	}

	/**
	 * Register the rest routes for images optimization.
	 *
	 * @since  5.4.0
	 */
	public function register_images_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/optimize-images/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_images, 'optimize_images' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/stop-images-optimization/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_images, 'stop_images_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/check-image-optimizing-status/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_images, 'check_image_optimizing_status' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/reset-images-optimization/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_images, 'reset_images_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register the rest routes for webp conversion.
	 *
	 * @since  5.4.0
	 */
	public function register_webp_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/delete-webp-files/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_webp, 'delete_webp_files' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/generate-webp-files/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_webp, 'generate_webp_files' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/stop-webp-conversion/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_webp, 'stop_webp_conversion' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/check-webp-conversion-status/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_webp, 'check_webp_conversion_status' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register multisite rest routes.
	 *
	 * @since  5.4.0
	 */
	public function register_multisite_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/enable-multisite-optimization/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_multisite, 'enable_multisite_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/disable-multisite-optimization/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_multisite, 'disable_multisite_optimization' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register Cloudflare routes.
	 *
	 * @since  5.7
	 */
	public function register_cloudflare_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/authenticate-cloudflare/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_cloudflare, 'authenticate' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/purge-cloudflare-cache/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_cloudflare, 'purge_cloudflare_cache_from_rest' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/deauthenticate-cloudflare/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_cloudflare, 'deauthenticate' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Register misc rest routes.
	 *
	 * @since  5.4.0
	 */
	public function register_misc_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE, '/hide-rating/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_misc, 'handle_hide_rating' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/get-assets/', array(
				'methods'  => 'GET',
				'callback' => array( $this->rest_helper_misc, 'get_assets' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/update-exclude-list/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_misc, 'update_exclude_list' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/run-analysis/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_misc, 'run_analysis' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE, '/get-test-results/', array(
				'methods'  => 'POST',
				'callback' => array( $this->rest_helper_misc, 'get_test_results' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

	}
}
