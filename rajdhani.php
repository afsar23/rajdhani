<?php
namespace Afsar\wtk;
use Afsar\wtk;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              wptoolkit.com
 * @since             0.0.1
 * @package           Wptoolkit
 *
 * @wordpress-plugin
 * Plugin Name:       [Rajdjhani]
 * Plugin URI:        wptoolkit.com
 * Description:       Bespoke plugin for Rajdhani Connections
 * Version:           0.1.0
 * Author:            Mohammed Afsar
 * Author URI:        wptoolkit.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptoolkit
 * Domain Path:       /languages
 */

require_once plugin_dir_path( __FILE__ ) . 'clsWptoolkit.php'; 
require_once plugin_dir_path( __FILE__ ) . 'wtkWidgets.php';
require_once plugin_dir_path( __FILE__ ) . 'db_setup.php';


require_once plugin_dir_path( __FILE__ ) . 'wtkFunctions.php';
require_once plugin_dir_path( __FILE__ ) . 'clsDatabase.php';

require_once plugin_dir_path( __FILE__ ) . 'CoreShortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'CustomShortcodes.php';

global $wtk;
global $pdb;
global $db;
$wtk = new Wptoolkit(__FILE__);
$pdb = new Database();
$db = $pdb->getConnection();



CreateOrUpdateDbSchema();
//register_activation_hook( __FILE__, 'Afsar\wtk\CreateOrUpdateDbSchema' );




wtk_create_plugin_menus();


			// If this file is called directly, abort.
			if ( ! defined( 'WPINC' ) ) {
				die;
			}

			if( ! class_exists( 'Plugin_Updater' ) ){
				include_once( plugin_dir_path( __FILE__ ) . 'plugin_updater.php' );
			}

			$ght = "ghp_mN5d3ZJm4hq0b8"."ZE29PU2OLh8dXTdy4G6mhC";
			//$ght = "ghp_CxOERbcLox80u8A6"."QG3gN2zcSkwlsw1fPk8U";
			
			$wtk_updater = new Plugin_Updater( __FILE__ );
			$wtk_updater->set_username( 'afsar23' );
			$wtk_updater->set_repository( 'rajdhani' );
			$wtk_updater->authorize( $ght ); 				// Your auth code goes here for private repos
			$wtk_updater->initialize();


// common stuff regardless of 
// what context I'm running in (backe-end / front-end / api)

global $wtkContext;
$wtkContext = "No context, man!";


add_action('init', 'Afsar\wtk\wtkInit',0);				// the 0 param makes the widgets_init hook fire!
function wtkInit() {
	
	global $wtkContext;
	$wtkContext = wtkContext();

		####################
			// patch for ensuring user is fully logged in and is_user_logged_in() will return true
			// needed after updating password via front-end where password hash cookies become invalid
			// and is_user_logged_in() returns false 
			if (!is_user_logged_in() AND $wtkContext<>"wprest") {
				try {    
					$jwt = (isset($_COOKIE["jwt_token"])) ? htmlspecialchars($_COOKIE["jwt_token"]) : "";
					$user = GetUserFromToken($jwt);
					$uid = $user["ID"];
					wp_set_current_user($uid);
					wp_set_auth_cookie($uid,true);
					wp_redirect(getCurrentUrl());
					exit;
				}
				catch (\Throwable $e) {
					// do nothing
				}				
			}


	
	switch (wtkContext()) {
		case "admin":
			//require_once plugin_dir_path( __FILE__ ) . 'pubController.php';
			require_once plugin_dir_path( __FILE__ ) . 'admController.php';
			$wtk_settings = new admSettings( __FILE__ );
			break;

		case "wprest":
			require_once plugin_dir_path( __FILE__ ) . 'apiController.php';
			break;

		case "public":				
			require_once plugin_dir_path( __FILE__ ) . 'pubController.php';
			break;		
		
		default:
			die("No context!");
	}

	InitSitePages();
	
}

/**
* returns the context under which the plugin is running
*	'admin'		-- we are in the wordpress backed-end / admin area
* 	'public'	-- we are in the wordpress public facing front-end
*	'wprest'	-- we are running in wp rest api mode
*/
function wtkContext() {

	global $wtkContext;
	if (is_admin()) return 'admin';					// I'm in the backed-end
	if ( is_rest() ) return 'wprest';				// It's wp rest request
	return 'public';
}


	/**
	 * Checks if the current request is a WP REST API request.
	 *
	 * Case #1: After WP_REST_Request initialisation
	 * Case #2: Support "plain" permalink settings and check if `rest_route` starts with `/`
	 * Case #3: It can happen that WP_Rewrite is not yet initialized,
	 *          so do this (wp-settings.php)
	 * Case #4: URL Path begins with wp-json/ (your REST prefix)
	 *          Also supports WP installations in subfolders
	 *
	 * @returns boolean
	 * @author matzeeable
	 */
	function is_rest() {
		if (defined('REST_REQUEST') && \REST_REQUEST // (#1)
				|| isset($_GET['rest_route']) // (#2)
						&& strpos( $_GET['rest_route'] , '/', 0 ) === 0)
				return true;

		// (#3)
		global $wp_rewrite;
		if ($wp_rewrite === null) $wp_rewrite = new WP_Rewrite();
			
		// (#4)
		$rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );
		$current_url = wp_parse_url( add_query_arg( array( ) ) );
		return strpos( $current_url['path'] ?? '/', $rest_url['path'], 0 ) === 0;
	}
	