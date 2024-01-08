<?php
namespace Afsar\wtk;
use Afsar\wtk;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'dashicons' );
} );	


require_once plugin_dir_path( __FILE__ ) . 'pubScriptsStyles.php';

		/* Modify page title without affecting other titles (eg menus) */

			add_filter( 'the_title', 'Afsar\wtk\wtk_title_update', 10, 2 );
			function wtk_title_update( $title, $id = null ) {
				
				$post = get_post( $id );
				if( $title=="Home" ) {
					return "";  //"My new Title!";
				}

				return $title;
			}

			// this filter fires just before the nav menu item creation process
			add_filter( 'pre_wp_nav_menu', 'Afsar\wtk\wtk_remove_title_filter_nav_menu', 10, 2 );
			function wtk_remove_title_filter_nav_menu( $nav_menu, $args ) {
				// we are working with menu, so remove the title filter
				remove_filter( 'the_title', 'Afsar\wtk\wtk_title_update', 10, 2 );
				return $nav_menu;
			}

			// this filter fires after nav menu item creation is done
			add_filter( 'wp_nav_menu_items', 'Afsar\wtk\wtk_add_title_filter_non_menu', 10, 2 );
			function wtk_add_title_filter_non_menu( $items, $args ) {
				// we are done working with menu, so add the title filter back
				add_filter( 'the_title', 'Afsar\wtk\wtk_title_update', 10, 2 );
				return $items;
			}


add_filter('the_content', 'Afsar\wtk\wtk_restrict_bios', -1 );
function wtk_restrict_bios( $content ){
	
	if (!is_user_logged_in()) {	
		$post = get_post();		
		$categories = get_the_category($post);
		foreach ($categories as $categ) {
			if ($categ->name == 'Biographical') {
				if (is_single()) {
					$content = "<b><i>SORRY! Biographical content is restricted for guest visitors.</i></b>";
				} else {
					$content = "--- Restricted Content ---";					
				}
				break;
			}
		}
	}
	
	return $content;

}

add_filter( 'the_content_more_link', 'Afsar\wtk\wtk_modify_read_more_link' );
function wtk_modify_read_more_link() {
 return '<a class="more-link" href="' . get_permalink() . '">Contrinue reading...</a>';
}




/*** function to impersonate a user WIP ***/
function ImpersonateUser() {

	if (current_user_can("manage_options")) {
		
		$user_login = (isset($_REQUEST["user_login"])) ? $_REQUEST["user_login"] : "";
	
		//die("HERE!");
		//get user's ID
		$user = get_userdatabylogin($user_login);
		$user_id = $user->ID;

		//login
		wp_set_current_user($user_id, $user_login);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $user_login);    // this should also kick off the jwt cookie setting?

		wp_redirect(home_url());
		exit;
		
		//also save some setting to say we are in impersonation mode, and allow to switch back to admin!
		///
		//
	} else {
		echo "<h3>You cannote impersonate a user!</h3>";
		
	}
	
}	
	


///////////////////////////////////////////////////




	
//add_action('wp_footer','Afsar\wtk\wtk_footer');
function wtk_footer() {

	//example of using dash-icons in front-en
	//echo '<h2><span class="dashicons dashicons-smiley"></span> A Cheerful Headline</h2>';
	//echo "<div>Is logged in: ". is_user_logged_in() . "</div>";
	//phpinfo();
	
	UserInfo();
	echo "Current URL = '". getCurrentUrl()."'<br/>";
	echo '<h3>COOKIES</h3>';
	echo wtk_printable($_COOKIE);

		// get the the role object
		//$role_object = get_role( "administrator" );

		// add $cap capability to this role object
		//$role_object->add_cap( "customize" );

		// remove $cap capability from this role object
		//$role_object->remove_cap( $capability_name );

}	


function UserInfo() {

	echo "<hr/>";

	if ( is_multisite() ) { echo '<h4>Multisite is enabled</h4>'; }
	
	$user = wp_get_current_user();
	
	if ( empty( $user ) ) {
		// User is logged out, create anonymous user object.
		$user = new \WP_User( 0 );
		$user->init( new \stdClass() );
	}
	echo "USER INFO<br/>";
	echo wtk_printable($user);
	echo "<hr/>";
	
}
	
add_action('wp_footer', 'Afsar\wtk\my_footer'); 
function my_footer() { 

	echo '<div style="background: gainsboro; color: gray;">&copy; Afsar Inc, 2023</div>'; 
	//mosquelist();
	//ListAllShortCodes();

	//echo "<h4>REST API - Registered Routes</h4>";
	
	//$r = new \WP_REST_Server;
	//echo printable($r->get_routes());
}


