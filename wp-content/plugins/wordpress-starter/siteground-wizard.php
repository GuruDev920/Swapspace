<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://www.siteground.com
 * @since             1.0.0
 * @package           SiteGround\SiteGroundCentral
 *
 * @wordpress-plugin
 * Plugin Name:       SiteGround Central
 * Plugin URI:        https://siteground.com
 * Description:       This plugin is designed to provide you with an easy start of your next WordPress project!
 * Version:           2.0.0
 * Author:            SiteGround
 * Author URI:        https://www.siteground.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       siteground-wizard
 * Domain Path:       /languages
 */

// Our namespace.
namespace SiteGround_Central;

use SiteGround_Central\Helper\Helper;
use SiteGround_Central\Activator\Activator;


// Define version constant.
if ( ! defined( __NAMESPACE__ . '\VERSION' ) ) {
	define( __NAMESPACE__ . '\VERSION', '2.0.0' );
}

// Define root directory.
if ( ! defined( __NAMESPACE__ . '\DIR' ) ) {
	define( __NAMESPACE__ . '\DIR', __DIR__ );
}

// Define root URL.
if ( ! defined( __NAMESPACE__ . '\URL' ) ) {
	$url = \trailingslashit( DIR );

	// Sanitize directory separator on Windows.
	$url = str_replace( '\\', '/', $url );

	$wp_plugin_dir = str_replace( '\\', '/', WP_PLUGIN_DIR );
	$url = str_replace( $wp_plugin_dir, \plugins_url(), $url );

	define( __NAMESPACE__ . '\URL', \untrailingslashit( $url ) );
}

function siteground_wizard_spl_autoload_register( $class ) {
	$prefix = 'SiteGround_Central';
	if ( stripos( $class, $prefix ) === false ) {
		return;
	}

	$file_path = \SiteGround_Central\DIR . '/core/' . str_ireplace( 'SiteGround_Central\\', '', $class ) . '.php';

	$file_path = str_replace( '\\', DIRECTORY_SEPARATOR, $file_path );

	if ( file_exists( $file_path ) ) {
		include_once( $file_path );
	}

}

spl_autoload_register( __NAMESPACE__ . '\siteground_wizard_spl_autoload_register' );

// Hook activator functions.
\register_activation_hook( __FILE__, array( new Activator , 'activate' ) );

// Initialize helper.
global $siteground_central_helper;

if ( ! isset( $siteground_central_helper ) ) {
	$siteground_central_helper = new Helper();
}
