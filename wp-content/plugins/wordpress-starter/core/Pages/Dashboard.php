<?php
namespace SiteGround_Central\Pages;

use SiteGround_Central\Helper\Helper;
/**
 * SG Central Dashboard main class
 */
class Dashboard extends Custom_Page {
	/**
	 * Parent slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $parent_slug = 'index.php';

	/**
	 * Capability.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * Menu slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $menu_slug = 'custom-dashboard.php';

	/**
	 * For checking the paths for overriding urls.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $submenu_slug = 'dashboard_page_custom-dashboard';

	/**
	 * Option which returns whether to hide or show custom page
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	public $option_name = 'siteground_wizard_hide_custom_dashboard';

	/**
	 * The page name for loading the correct scripts.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	public $page_id = 'dashboard_page_custom-dashboard';

	/**
	 * The network page name for loading the correct scripts.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	public $page_id_network = 'dashboard_page_custom-dashboard-network';

	/**
	 * The singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @var The singleton instance.
	 */
	private static $instance;

	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add independent actions.
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ), 9999 );
		add_action( 'wp_ajax_switch_dashboard', array( $this, 'switch_dashboard' ) );

		// Bail if the page should not be replaced.
		if ( false === $this->maybe_show_page() ) {
			return;
		}

		// Construct the parent.
		parent::__construct();

		// Add page specific actions.
		add_action( 'wp_ajax_hide_box', array( $this, 'hide_dashboard_box' ) );
		add_action( 'admin_menu', array( $this, 'remove_original_page' ), 999 );
		add_action( 'submenu_file', array( $this, 'highlight_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect_to_dashboard' ), 1 );
		add_action( 'wp_ajax_hide_banner', array( $this, 'hide_banner' ) );
		add_action( 'wp_ajax_hide_notifications', array( $this, 'hide_notifications' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_dashboard_admin_bar_menu_item' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'reorder_admin_bar' ) );
		add_action( 'wp_head', array( $this, 'additional_admin_bar_css' ) );
	}

	/**
	 * Prepare the necessary scripts.
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {
		// Check if we are on the correct page.
		if ( false === $this->maybe_page() ) {
			return;
		}

		// wp_enqueue_script(
		// 	'siteground-wizard-bundle',
		// 	\SiteGround_Central\URL . '/assets/js/dashboard.bundle.js',
		// 	array(), // Dependencies.
		// 	\SiteGround_Central\VERSION,
		// 	true
		// );


		wp_enqueue_script( 'dashboard' );

		wp_enqueue_style(
			'siteground-dashboard',
			\SiteGround_Central\URL . '/assets/css/dashboard.css',
			array(),
			\SiteGround_Central\VERSION,
			'all'
		);
	}

	/**
	 * Hides the dashboard box.
	 *
	 * @since  1.0.0
	 */
	public function hide_dashboard_box() {
		if ( ! isset( $_GET['box'] ) ) {
			wp_send_json_error();
		}

		update_option( $_GET['box'], 1 );

		wp_send_json_success();
	}

	/**
	 * Add option that will be used to check if the dashboard banner should be shown.
	 *
	 * @since  1.0.0
	 */
	public function switch_dashboard() {
		if (
			isset( $_GET['switch_dashboard'] ) &&
			wp_verify_nonce( $_GET['switch_dashboard'], 'switch_dashboard_nonce' )
		) {
			$value = isset( $_GET['value'] ) ? wp_unslash( $_GET['value'] ) : 'yes';
			$event = 'yes' === $value ? 'revert_dashboard' : 'dashboard_used_person';

			Helper::send_statistics( $event );

			update_option( 'siteground_wizard_hide_custom_dashboard', $value );

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'siteground_wizard_dashboard',
			__( 'Simplified Dashboard', 'siteground-wizard' ),
			array( $this, 'load_dashboard_widget' )
		);

		global $wp_meta_boxes;

		$wp_meta_boxes['dashboard']['side']['core'] = array_merge(
			array(
				'siteground_wizard_dashboard' => $wp_meta_boxes['dashboard']['normal']['core']['siteground_wizard_dashboard'],
			),
			$wp_meta_boxes['dashboard']['side']['core']
		);

		unset( $wp_meta_boxes['dashboard']['normal']['core']['siteground_wizard_dashboard'] );
	}

	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	public function load_dashboard_widget() {
		include \SiteGround_Central\DIR . '/templates/dashboard-widget.php';
	}

	/**
	 * Get the page title.
	 *
	 * @since 1.0.0
	 */
	public function get_page_title() {
		return __( 'Home', 'siteground-wizard' );
	}

	/**
	 * Get the menu title.
	 *
	 * @since 1.0.0
	 */
	public function get_menu_title() {
		return __( 'Home', 'siteground-wizard' );
	}
	/**
	 * Add additional styles to WordPress admin bar.
	 *
	 * @since  1.0.0
	 */
	public function additional_admin_bar_css() {
		if ( is_user_logged_in() && is_admin_bar_showing() ) :
		?>
			<style type="text/css">
				#wpadminbar ul li#wp-admin-bar-siteground-wizard-dashboard { padding-top: 12px; }
			</style>
		<?php
		endif;
	}

	/**
	 * Reorder admin bar menu to match the inital order.
	 *
	 * @since  1.0.0
	 */
	public function reorder_admin_bar() {
		global $wp_admin_bar;

		// The desired order of identifiers (items).
		$ids = array(
			'sg-central-dashboard',
			'themes',
			'widgets',
			'menus',
		);

		// Get an array of all the toolbar items on the current page.
		$nodes = $wp_admin_bar->get_nodes();

		// Perform recognized identifiers.
		foreach ( $ids as $id ) {
			if ( ! isset( $nodes[ $id ] ) ) {
				continue;
			}

			// This will cause the identifier to act as the last menu item.
			$wp_admin_bar->remove_menu( $id );
			$wp_admin_bar->add_node( $nodes[ $id ] );

			// Remove the identifier from the list of nodes.
			unset( $nodes[ $id ] );
		}

		// Unknown identifiers will be moved to appear after known identifiers.
		foreach ( $nodes as $id => &$obj ) {
			// There is no need to organize unknown children identifiers (sub items).
			if ( ! empty( $obj->parent ) ) {
				continue;
			}

			// This will cause the identifier to act as the last menu item.
			$wp_admin_bar->remove_menu( $id );
			$wp_admin_bar->add_node( $obj );
		}

	}

	/**
	 * Remove initial dashboard item from admin bar menu
	 * and add our custom dashboard menu item.
	 *
	 * @since 1.0.0
	 */
	public function add_dashboard_admin_bar_menu_item() {

		global $wp_admin_bar;

		// Remove the initial dashboard menu item.
		$wp_admin_bar->remove_node( 'dashboard' );

		// Add our custom dashboard item.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'sg-central-dashboard',
				'title'  => 'Dashboard',
				'href'   => get_admin_url( null, 'admin.php?page=custom-dashboard.php' ),
				'parent' => 'appearance',
			)
		);
	}

	/**
	 * Add option that will be used to check if the dashboard banner should be shown.
	 *
	 * @since  1.0.0
	 */
	public function hide_notifications() {
		$themes       = get_theme_updates();
		$plugins      = get_plugin_updates();
		$core         = get_core_updates();
		$translations = wp_get_translation_updates();
		$new_hash     = md5( serialize( $themes ) . serialize( $plugins ) . serialize( $core[0]->response ) . serialize( $translations ) );

		update_option( 'siteground_wizard_hide_notifications', 'yes' );
		update_option( 'updates_available', $new_hash );
	}

	/**
	 * Add option that will be used to check if the dashboard banner should be shown.
	 *
	 * @since  1.0.0
	 */
	public function hide_banner() {
		update_option( 'siteground_wizard_hide_main_banner', 'yes' );
	}

	/**
	 * Redirect to custom dashboard after successful installation.
	 *
	 * @since  1.0.0
	 */
	public function redirect_to_dashboard() {
		global $pagenow;

		// Bail if the current user is not admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$status = get_option( 'siteground_wizard_installation_status' );

		// Delete plugin transients on inital dashboard rendering.
		if ( isset( $_GET['hard-redirect'] ) ) {
			$this->delete_plugins_redirect_transients();
		}

		if (
			( isset( $_GET['page'] ) && 'siteground-central' === $_GET['page'] && ! empty( $status ) && 'completed' === $status['status'] ) ||
			$this->parent_slug === $pagenow && empty( $_GET )
		) {
			wp_safe_redirect( admin_url( 'admin.php?page=custom-dashboard.php' ) );
			exit;
		}
	}

	/**
	 * Delete all plugin redirect transients,
	 * to prevent redirects to their pages.
	 *
	 * @since  1.0.0
	 */
	private function delete_plugins_redirect_transients() {
		$transients = array(
			'wpforms_activation_redirect',
			'_tribe_events_activation_redirect',
		);

		foreach ( $transients as $transient ) {
			$response = delete_transient( $transient );
		}
	}

	/**
	 * Set the parent file to index.php in order to hightlight
	 * the menu item when "Dashboard" menu item is selected.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $parent_file The parent file name.
	 *
	 * @return string $parent_file The modified parent file name.
	 */
	public function highlight_menu_item( $parent_file ) {
		// Get the current screen.
		$current_screen = get_current_screen();

		// Check whether is the custom dashboard page
		// and change the `parent_file` to custom-dashboard.php.
		if ( 'dashboard_page_custom-dashboard' === $current_screen->base ) {
			$parent_file = $this->menu_slug;
		}

		// Return the `parent_file`.
		return $parent_file;
	}

	/**
	 * Remove the original "Home" page.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function remove_original_page() {
		remove_submenu_page( $this->parent_slug, $this->parent_slug );
	}

	/**
	 * Change the order of index.php submenu pages.
	 * Since our custom page has been added late, we need to reorder
	 * the submenu page, so that we can match the initial order.
	 *
	 * Example:
	 *          "SiteGround Wizard"
	 *          "Update core"
	 *
	 * @since  1.0.0
	 *
	 * @param  bool $menu_order Flag if the menu order is enabled.
	 *
	 * @return bool $menu_order Flag if the menu order is enabled.
	 */
	public function reorder_submenu_pages( $menu_order ) {
		// Load the global submenu.
		global $submenu;

		// Bail if for some reason the submenu is empty.
		if ( empty( $submenu ) ) {
			return;
		}

		// Try to get our custom page index.
		foreach ( $submenu['index.php'] as $key => $value ) {
			if ( 'custom-dashboard.php' === $value[2] ) {
				$page_index = $key;
			}
		}

		// Bail if our custom page is missing in `$submenu` for some reason.
		if ( empty( $page_index ) ) {
			return $menu_order;
		}

		// Store the custom dashboard in variable.
		$dashboard_menu_item = $submenu['index.php'][ $page_index ];

		// Remove the original custom dashboard page.
		unset( $submenu['index.php'][ $page_index ] );

		// Add the custom dashboard page in the beginning.
		array_unshift( $submenu['index.php'], $dashboard_menu_item );

		// Finally return the menu order.
		return $menu_order;
	}

	/**
	 * Render the submenu page.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function render() {
		require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );

		wp_localize_community_events();

		// Include the partial.
		include \SiteGround_Central\DIR . '/templates/custom-dashboard.php';
	}
}
