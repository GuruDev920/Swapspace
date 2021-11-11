<?php
namespace SiteGround_Central\Control;

include_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );
require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
/**
 * SG-Central Plugins Control main class.
 */
class Plugins {
	/**
	 * The singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \Importer The singleton instance.
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
			'template' => 'plugins',
			'title'    => 'Must Have Plugins&nbsp;',
			'count'    => '25+',
			'active'   => 1,
		),
		'default' => array(
			'id'       => 'default',
			'template' => 'plugins',
			'title'    => 'Browse wordpress directory&nbsp;',
			'count'    => '9K+',
		),
		'upload' => array(
			'id'       => 'upload',
			'template' => 'upload',
			'title'    => 'Upload Plugin',
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

	public function get_installed_plugins() {
		$plugins = array();

		$plugin_info = get_site_transient( 'update_plugins' );
		if ( isset( $plugin_info->no_update ) ) {
			foreach ( $plugin_info->no_update as $plugin ) {
				$plugins[ $plugin->slug ] = $plugin;
			}
		}

		if ( isset( $plugin_info->response ) ) {
			foreach ( $plugin_info->response as $plugin ) {
				$plugins[ $plugin->slug ] = $plugin;
			}
		}

		return $plugins;
	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \Importer The singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			static::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load more plugins when the button is pressed or search is initiated.
	 *
	 * @since  1.0.0
	 */
	public function ajax_plugins() {
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

		$this->render_plugins( $_GET['type'], $args );
		exit;
	}

	/**
	 * Get recommended plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Information we need for the plugins.
	 *
	 * @return OBJECT     Returns the num of plugins, page and array of all plugins
	 */
	public function get_recommended_plugins( $args ) {
		$plugins = (object) array();
		$response = wp_remote_get( 'https://wpwizardapi.siteground.com/sg-plugins' );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$plugins->plugins = json_decode( wp_remote_retrieve_body( $response ), true );
		}

		return $plugins;
	}

	/**
	 * Populate the custom plugin box. Arguments can be changed for different views.
	 *
	 * @since  1.0.0
	 *
	 * @param string $type Plugins type.
	 * @param array  $args API args.
	 */
	public function render_plugins( $type, $args = array() ) {
		$args = array_merge(
			$args,
			array(
				'per_page'          => 6,
				'installed_plugins' => array_keys( $this->get_installed_plugins() ),
			)
		);

		$plugins = 'recommended' === $type ? $this->get_recommended_plugins( $args ) : plugins_api( 'query_plugins', $args );

		// Render each plugin.
		foreach ( $plugins->plugins as $plugin ) {

			if (
				! empty( $plugin['category'] ) &&
				( 'system' === $plugin['category']  )
			) {
				continue;
			}
			include \SiteGround_Central\DIR . '/templates/partials/plugins/box.php';
		}
	}

	/**
	 * Check if plugin is installed, if there is an update for the plugin
	 *
	 * @since  1.0.0
	 *
	 * @param  array $plugin The plugin we are looping.
	 *
	 * @return string         The html content for the plugins page.
	 */
	public function maybe_installed( $plugin ) {
		if ( empty( $plugin['version'] ) ) {
			$plugin['version'] = 'latest';
		}

		$plugin_info = install_plugin_install_status( $plugin );

		$activate_url = admin_url( 'admin-ajax.php?action=siteground_wizard_activate_plugin&plugin=' . $plugin['slug'] );
		$activate_url = add_query_arg( 'nonce', wp_create_nonce( $plugin['slug'], $activate_url ), $activate_url );

		switch ( $plugin_info['status'] ) {
			case 'update_available':
				return '<a href="' . $plugin_info['url'] . '" class="sg-button sg-plugin-button sg-plugin-update" data-activate="' . $activate_url . '">
						<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium sg-button--plugin-update">
							<span class="sg-button__content">Update</span>
						</button></a>';
				break;
			case 'install':
				$install_url = admin_url( 'admin-ajax.php?action=siteground_wizard_install_plugin&plugin=' . $plugin['slug'] );
				$install_url = add_query_arg( 'nonce', wp_create_nonce( $plugin['slug'], $install_url ), $install_url );

				return '<a href="' . $install_url . '" class="sg-plugin-install sg-button" data-activate="' . $activate_url . '">
						<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium">
							<span class="sg-button__content">Install</span>
						</button></a>';
				break;
			case 'latest_installed':
			case 'newer_installed':
				// Check if the plugin is inactive.
				if ( false === is_plugin_active( $plugin_info['file'] ) ) {

					return '<a href="' . $activate_url . '" class="sg-plugin-activate">
						<button class="sg-ripple-container sg-button sg-button--primary sg-button--medium sg-button--outlined">
							<span class="sg-button__content">Activate</span>
						</button></a>';
				}
				return '<button class="sg-ripple-container sg-button sg-button--medium">
							<span class="sg-button__content">Active</span>
						</button>';
				break;
		}
	}

	/**
	 * Check if plugins is compatible with the current WP version.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $plugin Plugin info.
	 *
	 * @return bool         If plugin is compatible
	 */
	public function check_compatibility( $plugin ) {
		$requires_wp  = isset( $plugin['requires'] ) ? $plugin['requires'] : null;

		$compatible_wp  = is_wp_version_compatible( $requires_wp );
		$tested_wp      = ( empty( $plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $plugin['tested'], '<=' ) );

		if ( ! $tested_wp ) {
			return '<b class="sg-with-color sg-with-color--color-warning">Untested</b> with your version of WordPress';
		} elseif ( ! $compatible_wp ) {
			return '<b>Incompatible</b> with your version of WordPress';
		} else {
			return '<b>Compatible</b> with your version of WordPress';
		}
	}
}
