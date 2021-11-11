<?php
namespace SiteGround_Central\Pages;

use SiteGround_Central\Activator\Activator;
use SiteGround_Central\Helper\Helper;
/**
 * SG Central Wizard main class.
 */
class Wizard {
	/**
	 * The Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Display the wizard page.
		add_action( 'wp_loaded', array( $this, 'display_wizard_page' ), 10 );
		// Add the styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		// Try to redirect to wizard page.
		add_action( 'admin_init', array( $this, 'admin_init' ), 1 );
		add_action( 'wp_ajax_restart_wizard', array( $this, 'restart_wizard' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		// Get the current screen.
		$current_screen = \get_current_screen();

		// Check if we meet the page requirements.
		if (
			'dashboard_page_custom-dashboard' !== $current_screen->id &&
			'dashboard_page_custom-dashboard-network' !== $current_screen->id
		) {
			return false;
		}

		wp_enqueue_style(
			'siteground-wizard-style',
			\SiteGround_Central\URL . '/assets/css/style.css',
			array(),
			\SiteGround_Central\VERSION,
			'all'
		);
	}

	/**
	 * Restart the Wizard.
	 *
	 * @since  1.0.0
	 */
	public function restart_wizard() {
		if (
			isset( $_GET['restart_wizard'] ) &&
			wp_verify_nonce( $_GET['restart_wizard'], 'restart_wizard_nonce' )
		) {
			Helper::send_statistics( 'clicked_banner' );

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Hook to `admin_init` and redirect to Siteground Wizard if the `_sg_activation_redirect` transient flag is set.
	 *
	 * @since 1.0.0
	 */
	public function admin_init() {
		// If the `_sg_activation_redirect` is set, then redirect to the setup page.
		if ( 'no' === get_option( Activator::SHOW_WIZARD ) ) {
			return;
		}

		// If we're already on the page or the user doesn't have permissions, return.
		if (
			( ! empty( $_GET['page'] ) && 'siteground-wizard' === $_GET['page'] ) ||
			is_network_admin() ||
			isset( $_GET['activate-multi'] ) ||
			! current_user_can( 'manage_options' )
		) {
			return;
		}

		// Finally redirect to the setup page.
		wp_safe_redirect( admin_url( 'index.php?page=siteground-wizard' ) );

		exit;

	}

	/**
	 * Display wizard page.
	 *
	 * @since  1.0.0
	 */
	public function display_wizard_page() {
		if ( ! is_user_logged_in() && ! current_user_can( 'administrator' ) ) {
			return;
		}

		$status = get_option( 'siteground_wizard_installation_status' );

		// First check if we are in the wizard page at all, if not do nothing.
		if ( ! empty( $_GET['page'] ) && 'siteground-wizard' === $_GET['page'] ) {
			// Bail if we have successful installation already.
			if (
				! empty( $status ) &&
				'completed' === $status['status']
			) {

				wp_safe_redirect( 'admin.php?page=custom-dashboard.php' );
				exit;
			}
			include \SiteGround_Central\DIR . '/templates/siteground-wizard.php';
			exit;
		}
	}

}
