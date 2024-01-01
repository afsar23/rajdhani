<?php
namespace Afsar\wtk;
use Afsar\wtk;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function ListAllShortCodes(){
	global $shortcode_tags;
	echo "<ul>";
	foreach($shortcode_tags as $code => $function) {
		echo "<li>$code</li>";
	}
	echo "</ul>";
}

add_shortcode( 'wtk_myaccount', 			'Afsar\wtk\wtk_HandleShortcode' ); 
	
add_shortcode( 'wtk_login', 				'Afsar\wtk\wtk_HandleShortcode' ); 
add_shortcode( 'wtk_register', 				'Afsar\wtk\wtk_HandleShortcode' ); 
add_shortcode( 'wtk_forgot_password', 		'Afsar\wtk\wtk_HandleShortcode' ); 
add_shortcode( 'wtk_reset_password', 		'Afsar\wtk\wtk_HandleShortcode' ); 
add_shortcode( 'wtk_settings', 				'Afsar\wtk\wtk_HandleShortcode' ); 
add_shortcode( 'wtk_profile', 				'Afsar\wtk\wtk_HandleShortcode' ); 

add_shortcode( 'wtk_contactus', 			'Afsar\wtk\wtk_HandleShortcode' );
add_shortcode( 'wtk_viewanytable', 			'Afsar\wtk\wtk_HandleShortcode' );
add_shortcode( 'wtk_apilogs', 				'Afsar\wtk\wtk_HandleShortcode' );

//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleShortcode' );
//add_shortcode( 'wtk_<short_code_name>', 	'Afsar\wtk\wtk_HandleShortcode' );


function wtk_HandleShortcode($pg_atts = [], $pg_content = null, $sc_tag = '') {

	switch ($sc_tag) {

		case "wtk_myaccount": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_login": 					require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_register": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_forgot_password": 		require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_reset_password": 			require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_settings": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_profile": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;
		case "wtk_welcome": 				require_once plugin_dir_path( __FILE__ ) . 'pubUser.php';			break;

		case "wtk_contactus": 				require_once plugin_dir_path( __FILE__ ) . 'pubContactUs.php';		break;
		case "wtk_viewanytable": 			require_once plugin_dir_path( __FILE__ ) . 'pubViewAnyTable.php';	break;
		case "wtk_viewanytable": 			require_once plugin_dir_path( __FILE__ ) . 'pubApiLogs.php';		break;

	}

	ob_start();   // start buffering content

	// execute the shortcode identified by the passed in tag
	$fnc = 'Afsar\\wtk\\' . $sc_tag;	
	$fnc([ $pg_atts ]);
	
	$content = ob_get_clean(); // store buffered output content.

    return $content; // Return the content.

}


////////////////////////////////////////////////////////////////////
			/*
				add_shortcode( 'wtk_listmaint', 'Afsar\wtk\scListMaint');
				function listmaint($pg_atts = [], $pg_content = null, $pg_tag = '') {
					
					ob_start();

					// normalize attribute keys, lowercase
					$pg_atts = array_change_key_case((array)$pg_atts, CASE_LOWER);
					
					//die("tag=".$pg_tag);
					require_once plugin_dir_path( __FILE__ ) . 'pubListMaint.php';

					if ( is_user_logged_in() ) {
						//echo '<h4>Show list of tables to maintain</h4>';
						$default_fnc = "UserGroups";
					} else {
						return '<h4>Sorry, you need to login to maaintain data lists</h4>';
					}

					$fnc = (isset($_REQUEST["fnc"])) ? $_REQUEST["fnc"] : $default_fnc;

					switch ($fnc) {
						case "UserGroups": 			$subtitle = "User Groups"; 			break;
					}
					$fnc = 'Afsar\\wtk\\' . $fnc;	
					echo '<div class="subtitle"><h3>'.$subtitle.'</h3></div>';
					$fnc([ $pg_atts ]);

					$content = ob_get_clean(); // store buffered output content.

					return $content; // Return the content.
				}
			*/
	


