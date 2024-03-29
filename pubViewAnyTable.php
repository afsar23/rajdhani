<?php
namespace Afsar\wtk;
use Afsar\wtk;

/*
Mohammed Afsar
Single Page Application for the Plugin Application
Supported by a customer API registration routes
Uses the w2ui library
*/

defined('ABSPATH') or die("Cannot access pages directly.");   

######################################################################################


// invoked by shortcode in pubControler script
function wtk_viewanytable() {
		
	$table  = (isset($_GET['table']) ) ? $_GET['table'] : "wp_wtk_contactus";
	$grd_url = get_rest_url(null,"wtk/v1/listdata");

	$user_login = "";
	$user_email = "";
	$sql = "SELECT TABLE_NAME 
			FROM INFORMATION_SCHEMA.TABLES
			WHERE table_schema = '". \DB_NAME. "'
			ORDER BY TABLE_NAME";
	$lst = sqlSelect($sql);	
	
	?>
		<label for="tabname">Select table to view:</label>
		<select name="tab_name" id="tab_name" value="<?php echo $table;?>" onchange="changetable(this.value);" >
			<?php
			foreach($lst as $row):
				$selected = ($row['TABLE_NAME']==$table) ? "selected" : "";
				echo '<option value="'.$row['TABLE_NAME'].'" '.$selected.'>'.$row['TABLE_NAME'].'</option>'; //close your tags!!
			endforeach;
			?>
		</select>
		<div id="grdgeneric" style="width: 100%; height: 550px;"></div>

	
		<script>
	
			$('document').ready(function(){
				init2wuiSettings();		
				$('#grdgeneric').w2grid(grdConfig());			
			});		
			
			function grdConfig() {
				
				var cfg = {  
				
					name	: 'grdgeneric', 
					//method	: 'GET',		// for load once
					method	: 'POST',		// for dynamic server side load/refresh
					//header  : '<b><?php echo $table; ?></b>',
					url     : '<?php echo $grd_url; ?>',		// comment out if loading once from server
					httpHeaders: {
						"Content-Type"		: "application/json",
						"Authorization" 	: 'Bearer ' + getCookie('jwt_token'),
						//"X-WP-Nonce"		: wpApiSettings.nonce,
					},
					postData: {table: "wp_wtk_contactus"},
					limit	: 200000000,
					autoload: true,
					recid	: 'id',
					show: {
						header         : true,  // indicates if header is visible
						toolbar        : true,  // indicates if toolbar is visible
						footer         : true,  // indicates if footer is visible
						columnHeaders  : true,   // indicates if columns is visible
						lineNumbers    : true,  // indicates if line numbers column is visible
						expandColumn   : false,  // indicates if expand column is visible
						selectColumn   : true,  // indicates if select column is visible
						emptyRecords   : true,   // indicates if empty records are visible
						toolbarReload  : true,   // indicates if toolbar reload button is visible
						toolbarColumns : true,   // indicates if toolbar columns button is visible
						toolbarSearch  : false,   // indicates if toolbar search controls are visible
						toolbarAdd     : true,   // indicates if toolbar add new button is visible
						toolbarEdit    : true,   // indicates if toolbar edit button is visible
						toolbarDelete  : true,   // indicates if toolbar delete button is visible
						toolbarSave    : false,   // indicates if toolbar save button is visible
						selectionBorder: true,   // display border around selection (for selectType = 'cell')
						recordTitles   : true,   // indicates if to define titles for records
						skipRecords    : false,    // indicates if skip records should be visible
						toolbarInput   : false,      // hides search input on the toolbar
						searchAll 	   : false       // hides 'All Fields' option in the search dropdown					
					},
					onLoad : function(event) {
						console.log(event);
						if (event.detail.data.status=="success") {
							if (event.detail.data.total == 0 ) {
								this.records = [ {data_col: 'No data'} ];
								this.columns = [ { field: 'data_col', text: 'Data' } ];
							} else {
								rec = event.detail.data.records[0];
								i=0;
								cols = []
								for(var fld in rec){		// iterate through the properties of the rec object
									if (i==0) {
										cols[i] = {"field": fld, "text": fld, 
											info: {
												render: (rec, ind, col_ind) => { return prettifyJSON(JSON.parse(JSON.stringify(rec))) }
											}};							
									} else {
										cols[i] = {"field": fld, "text": fld, sortable:true};
									} 
									i++;
								}
								this.columns = cols;
							}		
						}
					}				
				};

				return cfg
				
			}			

			function changetable(elem) {
				w2ui['grdgeneric'].postData.table = elem;
				//w2ui['grdgeneric'].header = '<b>'+elem+'</b>';
				w2ui['grdgeneric'].reload();
			}
			
		</script>	

	<?php			

}  // end 


