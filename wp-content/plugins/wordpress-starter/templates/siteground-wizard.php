<?php
namespace SiteGround_Central;

use SiteGround_Central\Helper\Helper;
use SiteGround_Central\Rest\Rest;

include_once(ABSPATH.'wp-admin/includes/plugin.php');
?>
<!doctype html>
<html>
<head>
	<!-- Defining responsive ambient. -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php esc_html_e( 'WordPress Starter', 'wordlift' ); ?></title>
</head>
<body>
	<div id="app"></div>
</body>
	<?php
		wp_enqueue_script(
			'siteground-wizard-bundle',
			\SiteGround_Central\URL . '/assets/js/wizard.bundle.js',
			array( 'jquery' ), // Dependencies.
			\SiteGround_Central\VERSION
		);

		$current_user = wp_get_current_user();
		$locale       = get_user_locale( $current_user );
		preg_match( '~([a-zA-Z]+)_?~', $locale, $matches );
		$data = array(
			'ip'                    => Helper::get_ip_address(),
			'installation_endpoint' => get_rest_url( null, Rest::REST_NAMESPACE . '/installer/' ),
			'visibility_endpoint'   => get_rest_url( null, Rest::REST_NAMESPACE . '/update-visibility/' ),
			'status_endpoint'       => add_query_arg( 'nocache', '', get_rest_url( null, Rest::REST_NAMESPACE . '/installer-progress/' ) ),
			'dashboard_url'         => get_admin_url( null, 'admin.php?page=custom-dashboard.php&hard-redirect' ),
			'site_url'              => get_site_url( '/' ),
			'update_visibility'     => get_option( 'siteground_wizard_activation_redirect', 'yes' ),
			'queue'                 => get_option( 'siteground_wizard_installation_queue', array() ),
			'locale'                => $matches[1],
			'wp_nonce'              => wp_create_nonce( 'wp_rest' ),
			'persist_key'           => md5( get_home_url( '/' ) . get_option( 'siteground_wizard_install_timestamp', false ) ),
			'config'                => array(
				'assetsPath' => \SiteGround_Central\URL . '/assets/img',
			),
			'strings'               => array(
				'start_wizard'          => __( 'Start Now', 'sg-central' ),
				'exit_wizard'           => __( 'Exit', 'sg-central' ),
				'free'                  => __( 'Free', 'sg-central' ),
				'days'                  => __( 'Days', 'sg-central' ),
				'default'               => __( 'Default', 'sg-central' ),
				'continue'              => __( 'Continue', 'sg-central' ),
				'view_site'             => __( 'View Site', 'sg-central' ),
				'plugin_by'             => __( 'plugin by', 'sg-central' ),
				'previous'              => __( 'previous', 'sg-central' ),
				'select'                => __( 'Select', 'sg-central' ),
				'close'                 => __( 'Close', 'sg-central' ),
				'confirm'               => __( 'Confirm', 'sg-central' ),
				'selected'              => __( 'Selected', 'sg-central' ),
				'search_placeholder'    => __( 'Type a keyword...', 'sg-central' ),
				'currently_selected'    => __( 'Currently selected', 'sg-central' ),
				'categories_title'      => __( 'Categories', 'sg-central' ),
				'plugin_by'             => __( 'plugin by', 'sg-central' ),
				'recommended'           => __( 'recommended', 'sg-central' ),
				'required'              => __( 'required', 'sg-central' ),
				'builders_title'        => __( 'Great design choice!', 'sg-central' ),
				'builders_subtitle'     => __( 'The design you have chosen comes with sample data that is enabled by { pluginNames }. In order to keep the sample data, including all the pages and overall look and structure of the selected design, we will install { pluginNames } on your site.', 'sg-central' ),
				'installation_title'    => __( 'Installing, please don\'t close or refresh this page.', 'sg-central' ),
				'installation_subtitle' => __( 'Please, don’t close this window, we will finish shortly!', 'sg-central' ),
				'success_title'         => __( 'Congrats! Your site is ready!', 'sg-central' ),
				'success_subtitle'      => __( 'We have successfully completed the installation of the items you selected. You may now proceed to your WordPress dashboard and start managing your site.', 'sg-central' ),
				'fail_title'            => __( 'Oops! Something went wrong!', 'sg-central' ),
				'fail_subtitle'         => __( 'The installation of the selected items could not be completed. Please restart the wizard or try again later.', 'sg-central' ),
				'dashboard_button_text' => __( 'Go to Dashboard', 'sg-central' ),
				'restart_button_text'   => __( 'Restart Installation', 'sg-central' ),
				'select_theme'          => __( 'Select theme', 'sg-central' ),
				'load_more'             => __( 'Load More', 'sg-central' ),
				'init_message'          => __( 'Preparing your WordPress installation...', 'sg-central' ),
			),
			'plugins'                => array(
				'wpforms-lite'        => __( 'Contact Form', 'sg-central' ),
				'the-events-calendar' => __( 'Calendar', 'sg-central' ),
				'wp-google-maps'      => __( 'Maps', 'sg-central' ),
				'foogallery'          => __( 'Gallery', 'sg-central' ),
				'woocommerce'         => __( 'Shop', 'sg-central' ),
				'wordpress-seo'       => __( 'Optimize for SEO', 'sg-central' ),
				'optinmonster'        => __( 'Grow Subscribers List', 'sg-central' ),
			),
			'tags'                   => array(
				'all'              => __( 'All', 'sg-central' ),
				'Business'         => __( 'Business', 'sg-central' ),
				'Travel'           => __( 'Travel', 'sg-central' ),
				'Online-store'     => __( 'Online-store', 'sg-central' ),
				'Portfolio'        => __( 'Portfolio', 'sg-central' ),
				'Wedding'          => __( 'Wedding', 'sg-central' ),
				'Magazine'         => __( 'Magazine', 'sg-central' ),
				'Fashion & Beauty' => __( 'Fashion & Beauty', 'sg-central' ),
				'Blog'             => __( 'Blog', 'sg-central' ),
				'Art & Design'     => __( 'Art & Design', 'sg-central' ),
				'Health & Fitness' => __( 'Health & Fitness', 'sg-central' ),
				'Photography'      => __( 'Photography', 'sg-central' ),
				'Restaurant'       => __( 'Restaurant', 'sg-central' ),
			),
		);

		if ( 'yes' === get_option( 'siteground_wizard_reseller' ) ) {
			$data['is_reseller'] = 'yes';
		}

		if ( Helper::is_shop() ) {
			$data['is_woo_setup'] = 1;
		}

		wp_localize_script( 'siteground-wizard-bundle', 'wizardData', $data );

		// Finally print the scripts.
		wp_print_scripts();
	?>
</html>
