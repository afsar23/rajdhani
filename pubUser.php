<?php
namespace Afsar\wtk;
use Afsar\wtk;

/*
user interface related to manage users
*/

defined('ABSPATH') or die("Cannot access pages directly.");   

######################################################################################



function wtk_myaccount($pg_atts) {
	
	if ( is_user_logged_in() ) {
		$default_fnc = 'profile';
	} else {
		$default_fnc = 'login';
	}
	$fnc = (isset($_REQUEST["fnc"])) ? $_REQUEST["fnc"] : $default_fnc;
	$fnc = 'Afsar\\wtk\\wtk_' . $fnc;	
	$fnc([ $pg_atts ]);	
}	



function wtk_login($pg_atts) {

	$login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
	if ( $login === "failed" ) {
		echo '<p class="login-msg"><strong>ERROR:</strong> Invalid username and/or password.</p>';
	} elseif ( $login === "empty" ) {
		echo '<p class="login-msg"><strong>ERROR:</strong> Username and/or Password is empty.</p>';
	} elseif ( $login === "false" ) {
		echo '<p class="login-msg"><strong>ERROR:</strong> You are logged out.</p>';
	}
	
    $args = array(
        'redirect' => home_url(), // redirect to home page.
        'form_id' => 'wtk_loginform',
        'label_username' => __( 'Username:' ),
        'label_password' => __( 'Password:' ),
        'label_remember' => __( 'Remember Me' ),
		'redirect'		 => get_permalink( get_the_ID()),
        'label_log_in' => __( 'Log in' ),
         'remember' => true
    );
	
	if (!is_user_logged_in()) {
		?>
			<div class="card shadow">
				<div class="card-body">
					<div>
					<?php
					wp_login_form( $args );
					?>
					</div>
				</div>
				<div class="card-footer">
					<div><a href="<?php echo home_url().'/my-account?fnc=forgot_password';?>">Forgot Password?</a></div>
					<div>Don't have an account? <a href="<?php echo home_url().'/my-account?fnc=register';?>">Register</a></div>
				</div>
			</div>
		<?php
	} else {
		echo "<h3>You are already logged in!</h4>";
	}
}


function wtk_login_out($pg_atts) {
	if (!is_user_logged_in()) {
		login($pg_atts);
	} else {
		logout($pg_atts);
	}
}



######################################################################################

	
function wtk_register($pg_atts) {

	$curr_user = wp_get_current_user();
	
	if ($curr_user->ID = 0) {
		
		echo "<h3>You are already registered and logged in!</h3>";
		
	} else {

		$api_email_code = get_rest_url(null,"wtk/v1/email_verification_code");		// custom user registration end point
		$api_url        = get_rest_url(null,"wtk/v1/register/?arg1=Y&arg2=No");		// custom user registration end point
		$jsCallBack     = "postFormProcessing";

		//echo "<pre>".$api_url."</pre>";	

		$user_login = "";
		$user_email = "";
		//$lst = SelectList("SELECT id, usergroup FROM ".prefix("usergroups")." ORDER BY seqno;");
				
		?>
		<div>
			<div id="response"></diV>
		
			<form id='reg_form' action='javascript:;' onsubmit="submitForm(this,'<?=$api_url?>',<?=$jsCallBack?>);"> 
				<input autocomplete="false" name="hidden" type="text" style="display:none;">
				
				<div class="form-group">
					<label for="user_name" autocomplete="off">User Name</label>
					<input type="text" class="form-control" name="user_name" id="user_name" oplaceholder="login name" />
				</div>

				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" class="form-control" name="email" id="email" value=" " required />
					
					<p>Click [<a href="javascript:;" id="email_verification_code" name="email_verification_code" onclick="EmailVerificationCode('<?=$api_email_code?>');">here</a>] to receive Verfication Code to input below.<br/> 
					<label for="token_raw">Verification code</label>
					<input type="text" id="token_raw" name="token_raw" value="" required />
						
					<input type="hidden" id="token_hash" name="token_hash" value="" />
				
				</div>

				<div class="form-group">
					<label for="user_pass">Choose Password</label>
					<input type="password" class="form-control" name="user_pass" id="user_pass" required placeholder="password" autocomplete="off" />
				</div>
				
					<div class="form-group" style="display:none" >
						<label for="user_group">User Group</label>
						<select class="form-control" name="user_group" id="user_group" value="1" >
							<?php
							//foreach($lst as $key => $value):
							//	echo '<option value="'.$key.'">'.$value.'</option>'; //close your tags!!
							//endforeach;
							?>
						</select>
					</div>					


				<div class="form-group">
					<label for="referral_code">Referral Code</label>
					<input type="text" class="form-control" name="referral_code" id="referral_code" value="" required />
				</div>

				<?php wp_nonce_field(wtkNonceKey(), '_wpnonce');?>

				<button type='submit' class='btn btn-primary'>Register</button>

				</form>
			</div>
		<?php
	}

}

function wtk_welcome() {

	$current_user = wp_get_current_user();
	if ( ($current_user instanceof \WP_User) ) {
		echo "<blockquote>Welcome <strong>".esc_html( $current_user->display_name )."</strong></br/>";
		echo "<p>Thank you for registering. Happy browsing!</p>";
		echo "</blockquote>";
	} else {
		echo "<h3>Oops -  something went wrong with registration!</h3>";
	}
}


function wtk_settings($pg_atts) {
	
	echo "<p>Change settings and preferences here</>";
}

function wtk_forgot_password($pg_atts) {
	
	$curr_user = wp_get_current_user();
		
    $api_url        = get_rest_url(null,"wtk/v1/password_reset");		// custom password reset link
	$jsCallBack     = "postFormProcessing";
	//echo "<pre>".$api_url."</pre>";

	?>
	
	<div id="response"></diV>
	
	<form id='password_reset' action='javascript:;' onsubmit="submitForm(this,'<?=$api_url?>',<?=$jsCallBack?>);"> 

		<div class="form-group">
			<label for="user_login">Login or Email</label>
			<input type="text" class="form-control" name="login_or_email" id="login_or_email" value="" required />
		</div>
		
		<?php wp_nonce_field(wtkNonceKey(), '_wpnonce'); ?>

		<button type='submit' class='btn btn-primary'>Send Password Reset Link</button>

	</form>
	<?php	
	
}

function wtk_reset_password() {

    global $wtk;

    $api_url        = get_rest_url(null,"wtk/v1/update_password");		// custom password reset link
    $jsCallBack     = "postFormProcessing";

	$JWTToken = (isset($_REQUEST["token"])) ? $_REQUEST["token"] : htmlspecialchars($_COOKIE["jwt_token"]);
	
	$tokenvalidation = JWTTokenValidation($JWTToken);
	//echo printable($tokenvalidation);

    if ( $tokenvalidation["status"] =="success" ) {

		?>
			<div id="response"></div>
			<form id='pwreset_form' action='javascript:;' onsubmit="submitForm(this,'<?=$api_url?>',<?=$jsCallBack?>);"> 
				<div class='form-group'>
					<label for='password'>New Password</label>
					<input type='password' class='form-control' id='user_pass' name='user_pass' placeholder='Enter new password' required />
					<input type='hidden' value='<?=$JWTToken?>' name='token' id='token' />
				</div>
				<?php 
					wp_nonce_field(wtkNonceKey(), '_wpnonce');
				?>
				<button type='submit' class='btn btn-primary'>Reset Password</button>
			</form>
			
			<?php if (!is_user_logged_in()) { ?>
				<div><a href="<?php echo home_url("/my-account/?fnc=login"); ?>">Login</a></div>
			<?php } ?>
			
		<?php

    } else {
        echo "Invalid password reset link!";
    }

}
	
function wtk_pwd_changed_ok() {
	
	if ( is_user_logged_in() ) {		
		echo "<blockquote>Password successfully changed</blockquote>";
	} else {
		echo "<h3>Oops - something went wrong whilst resetting password!</h3>";
	}	
}



function wtk_profile($pg_atts) {	
	echo "<p>Form to edit profile</p>";
}

#####################################################################################

// js to run when doc ready 
?>


<script>

async function EmailVerificationCode(api_email_code) {
	
	var email = $j("#email").val();
	var nonce = $j("#_wpnonce").val();
 
	var emailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	if(!email.match(emailformat)){	
		w2alert("Please input a valid email address!")
		return;
	}
	
	frmData = JSON.stringify({"email": email, "_wpnonce":nonce}); 
	CallAPI(api_email_code, frmData, PostEmailVerification);
	
}

function PostEmailVerification(response) {
	
	resp = JSON.stringify(response);
    if (response.status=="success") {
        // do something , eg redirect to login page?
        $j('#token_hash').val(response.hash_token);
		w2alert("Please check your email for Verification Code");
	} else {
        alert(response.message);
    }	
}


function postFormProcessing(response) {

	//alert("here!");
	//$j('#response').html(JSON.stringify(response));
	//$j('#response').html("<div class='alert alert-danger'>"+response.message+"</div>");
	//return;
    if (response.status=="success") {
        // do something , eg redirect to login page?
        if (response.hasOwnProperty('redirect')) {
			location.href = response.redirect;
		} else {
			$j('#response').html("<div class='alert alert-success'>"+response.message+"</div>");
		}
    } else {
        $j('#response').html("<div class='alert alert-danger'>"+response.message+"</div>");
    }
}
</script>
