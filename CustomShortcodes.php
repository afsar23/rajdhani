<?php
namespace Afsar\wtk;
use Afsar\wtk;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleCustomShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleCustomShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleCustomShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleCustomShortcode' );


function wtk_HandleCustomShortcode($pg_atts = [], $pg_content = null, $sc_tag = '') {

	switch ($sc_tag) {

		case "wtk_login": 					require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_register": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_forgot_password": 		require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_profile": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_settings": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_reset_password": 			require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_welcome": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;

	}

	ob_start();   // start buffering content

	// execute the shortcode identified by the passed in tag
	$fnc = 'Afsar\\wtk\\' . $sc_tag;	
	$fnc([ $pg_atts ]);
	
	$content = ob_get_clean(); // store buffered output content.

    return $content; // Return the content.

}
