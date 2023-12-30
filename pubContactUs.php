<?php
namespace Afsar\wtk;
use Afsar\wtk;

use Formr;

defined('ABSPATH') or die("Cannot access pages directly."); 

require_once 'Formr/class.formr.php';

function ContactUs() {

    global $app;

	?>
	<!-- Form display card w/ options -->
	<div id="contactform"></div>
	<div id="response"></div>
	<?php

	frmContactUs();

}


function frmContactUs() {
  
    $api_url        = get_rest_url(null,"wtk/v1/contactus");		// custom password reset link
	echo "<p>".$api_url."</p>";
	
	$num1 = rand(3,29);
	$num2 = rand(2,43);	
	$human_check = $num1." + ".$num2." = ?";
	?>
		<script>	

		contactform();
		
		function contactform() {  //$(function () {
			//alert("herr!");
			var api_url = '<?php echo $api_url;?>';		
			jsCallBack = 'DisplayResults';
			
			let myForm = new w2form({
				box: '#contactform',
				name: 'contactform',
				header: '<b>Contact Us</b>',	
				fields : [
					{ field: 'f_fullname', html: {label: 'Full Name',  attr: 'style="width: 250px"'}, type: 'text', required:true },
					
					<?php if (!is_user_logged_in()) { ?>
						{ field: 'f_email', html: {label: 'Email',  attr: 'style="width: 250px"'},  type: 'email', required:true },
					<?php } ?>
					
					{ field: 'f_subject', html: {label: 'Subject',  attr: 'style="width: 250px"'},  type: 'text', required:true },
					{ field: 'f_message', html: {label: 'Message',  attr: 'style="width: 250px" rows=4'},  type: 'textarea', required:true },
					
					<?php if (!is_user_logged_in()) { ?>
						{ field: 'f_banda', html: {label: '<?php echo $human_check;?>'},  type: 'int', required:true },
						{ field: 'f_jawab', hidden:true, type: 'int' },
					<?php } ?>
					
					{ field: '_wpnonce', hidden:true, type:'text'}
				],
				record: {
					f_jawab: '<?php echo $num1 + $num2;?>',
					_wpnonce: '<?php echo wp_create_nonce(wtkNonceKey());?>'
				},
				actions: {
					submit: {
						text: 'Submit',
						//class: 'w2ui-btn-green',
						//style: 'text-transform: uppercase',
						onClick(event) {
							this.lock('Wait...', true);
							if (this.validate().length == 0) {
								HandleApiResponse(api_url, JSON.stringify(this.getCleanRecord()),				
									async function(response) {
										DisplayResults(response);
									}
								);
							}
							this.unlock();
						},
						onDoubleClick(event) {
							// do nothing!
						}
					},					
					reset: {
						text: 'Reset',
						onClick(event) {
							this.clear();
						}
					}					
				}				
			});
		//});
		}


		function DisplayResults(response) {

			//alert("here!");
			//$j('#response').html(JSON.stringify(response));
			//return;
			
			if (response.status=="success") {
				// do something , eg redirect to login page?
				$j('#response').html("<div class='alert alert-success'>"+response.message+"</div>");
			} else {
				$j('#response').html("<div class='alert alert-danger'>"+response.message+"</div>");
			}									

		}

	</script>

	<?php
}
