<?php
namespace SiteGround_Central\Control;

// if ( ! function_exists( 'install_theme_information' ) ) {
// 	require_once( ABSPATH . 'wp-admin/includes/theme.php' );
// 	include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );
// }

/**
 * SG-Central Themes Control main class.
 */
class Themes {
	/**
	 * The singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \Themes_Control The singleton instance.
	 */
	private static $instance;

	/**
	 * The custom tabs array.
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 */
	public $tabs = array(
		'recommended' => array(
			'id'       => 'recommended',
			'template' => 'themes',
			'title'    => 'See Recommended&nbsp;',
			'count'    => '25+',
			'active'   => 1,
		),
		'default' => array(
			'id'       => 'default',
			'template' => 'themes',
			'title'    => 'Browse WordPress directory&nbsp;',
			'count'    => '9K+',
		),
		'upload' => array(
			'id'       => 'upload',
			'template' => 'upload',
			'title'    => 'Upload Theme',
		),
	);

	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$instance = $this;
	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \Themes_Control The singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			static::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load more themes when the button is pressed or search is initiated.
	 *
	 * @since  1.0.0
	 */
	public function ajax_themes() {
		// Add page param.
		if ( isset( $_GET['page_id'] ) ) {
			$args['page'] = intval( $_GET['page_id'] );
		}

		if (
			isset( $_GET['s'] ) &&
			isset( $_GET['searchType'] )
		) {
			$args[ $_GET['searchType'] ] = $_GET['s'];
		}

		if ( isset( $_GET['tag'] ) ) {
			$args['tag'] = $_GET['tag'];
		}

		$this->render_themes( $_GET['type'], $args );
		exit;
	}

	/**
	 * Populate the custom theme box. Arguments can be changed for different views.
	 *
	 * @since  1.0.0
	 *
	 * @param string $type themes type.
	 * @param array  $args API args.
	 */
	public function render_themes( $type, $args = array() ) {
		$args['per_page'] = 6;

		if ( ! empty( $args['searchType'] ) && 'search' === $args['searchType'] ) {
			$args['browse'] = 'popular';
		}

		$themes = 'recommended' === $type ? $this->get_recommended_themes( $args ) : themes_api( 'query_themes', $args );

		// Render each theme.
		foreach ( $themes->themes as $theme ) {
			include \SiteGround_Central\DIR . '/templates/partials/themes/box.php';
		}
	}

	/**
	 * Get recommended themes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Information we need for the themes.
	 *
	 * @return OBJECT     Returns the num of themes, page and array of all themes
	 */
	public function get_recommended_themes( $args ) {
		$response = wp_remote_get( 'https://wpwizardapi.siteground.com/sg-themes' );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			@$themes->themes = json_decode( wp_remote_retrieve_body( $response ) );
		}

		return $themes;
	}

	/**
	 * Check if theme is installed, if there is an update for the theme.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $theme The theme we are looping.
	 *
	 * @return string         The html content for the theme page.
	 */
	public function get_actions( $theme, $type ) {
		if ( 'recommended' === $type ) {
			return $this->get_recommended_actions( $theme );
		}

		$installed_themes = array_map( 'strtolower', array_keys( search_theme_directories() ) );
		// Define default status.
		$status = 'install';
		// Get info for theme.
		$installed_theme = wp_get_theme( $theme->slug );

		if ( empty( $theme->version ) ) {
			$theme->version = 'latest';
		}

		// Check if theme exist and its status.
		if ( $installed_theme->exists() ) {
			if ( version_compare( $installed_theme->get( 'Version' ), $theme->version, '>=' ) ) {
				$status = 'latest_installed';
			} else {
				$status = 'update_available';
			}
		}

		if ( in_array( $installed_theme->template, $installed_themes ) ) {
			$status = 'activate';
		}

		if ( $installed_theme->get_stylesheet() === wp_get_theme()->get_stylesheet() ) {
			$status = 'customize';
		}

		$customize_url = add_query_arg(
			array(
				'theme'  => urlencode( $theme->slug ),
				'return' => urlencode( admin_url( 'themes.php?page=sg-themes-install.php' ) ),
			),
			admin_url( 'customize.php' )
		);

		$activate_url = add_query_arg(
			array(
				'action'     => 'activate',
				'stylesheet' => urlencode( $theme->slug ),
			),
			self_admin_url( 'themes.php' )
		);

		// Prepare the install url.
		$install_url = add_query_arg(
			array(
				'action' => 'install-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		// Prepare the update url.
		$update_url = add_query_arg(
			array(
				'action' => 'upgrade-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		switch ( $status ) {
			case 'update_available':
				return '<a href="' . esc_url( wp_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ) . '" class="sg-theme-install" data-slug="' . $theme->slug . '" data-nonce="' . wp_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) . '">
							<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
								<span class="sg-button__content">
									Install
								</span>
							</button>
						</a>';
				break;
			case 'latest_installed':
				return '<span class="sg-label sg-label--type-default sg-label--size-small sg-with-color sg-with-color--color-light">
							<span class="sg-label__text">
								Installed 
							</span>
						</span>';
				break;
			case 'activate':
				return '<a href="' . esc_url( wp_nonce_url( $activate_url, 'switch-theme_' . $theme->slug ) ) . '" class="sg-theme-activate" data-preview="' . esc_url( $customize_url ) . '">
							<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
								<span class="sg-button__content">
									Activate
								</span>
							</button>
						</a>';
				break;
			case 'customize':
				return '<a href="' . esc_url( $customize_url ) . '" class="sg-preview sg-active-theme" data-actvate="' . esc_url( wp_nonce_url( $activate_url, 'switch-theme_' . $theme->slug ) ) . '">
							<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
								<span class="sg-button__content">
									Live Preview
								</span>
							</button>
						</a>';
				break;
			case 'install':
			default:
				return '<a href="' . esc_url( wp_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ) . '" class="sg-theme-install" data-slug="' . $theme->slug . '" data-nonce="' . wp_create_nonce( 'updates', 'install-theme_' . $theme->slug ) . '">
							<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
								<span class="sg-button__content">
									Install
								</span>
							</button>
						</a>';
				break;
		}
	}

	/**
	 * Get actions for recommended themes.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $theme The theme we are looping.
	 *
	 * @return string       The html content for the theme page.
	 */
	public function get_recommended_actions( $theme ) {
		return '<a href="#" class="sg-recommended-theme-button" data-id="' . $theme->id . '">
					<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
						<span class="sg-button__content">
							Install
						</span>
					</button>
				</a>';
	}
}
