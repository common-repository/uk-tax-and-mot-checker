<?php

add_action('admin_menu', 'uk_tax_mot_checker');
add_action('admin_head', 'uk_tax_mot_checker_admin_styles');

//admin page style
function uk_tax_mot_checker_admin_styles() {
wp_enqueue_style( 'free_tax_mot_data_admin', plugins_url( '/assets/css/admin.css', __FILE__ ) );
}

function uk_tax_mot_checker(){
    add_menu_page( 'UK Tax & Mot Checker', 'UK Tax & Mot Checker', 'manage_options', 'uk-tax-mot-checker', 'uk_tax_mot_checker_admin', 'dashicons-car');
}
 
function uk_tax_mot_checker_admin(){

register_setting( 'UTMCDisableEndPoint', 'UTMCDisableEndPoint' );
register_setting( 'UTMCCreditUs', 'UTMCCreditUs' );	

//bugfix for after upgrade of old ver to new;
	if (get_option( 'UTMCDisableEndPoint' ) == false){
	add_option( 'UTMCDisableEndPoint', 'yes' );//disable endpoint until signup (avoid errors)
	}
	
	if (get_option( 'UTMCCreditUs' ) == false){
	add_option( 'UTMCCreditUs', 'no' );//Set credit us option
	}
	
	if (get_option( 'UTMCCreditLink' ) == false){
	$selectone = array("https://www.rapidcarcheck.co.uk/", "https://www.rapidcarcheck.co.uk/developer-api/");	
	$kxx = array_rand($selectone);
	$vxx = $selectone[$kxx];
	add_option( 'UTMCCreditLink', $vxx );//Set credit us option
	}


$CreditUsStats = get_option('UTMCCreditUs');
$DisableAPIStats = get_option('UTMCDisableEndPoint');


if ($CreditUsStats == 'no'){
$CurrentSupporter = 'no';	
}else{
$CurrentSupporter = 'yes';		
}


// switching API packages
if (isset($_POST['credit'])) {
if ($_POST['credit'] == 'yes'){
$json_api_upgrade_req = json_decode(wp_remote_retrieve_body(wp_remote_get( 'https://www.rapidcarcheck.co.uk/FreeAccess/RequestsChange.php?auth=LeCc0xMsd00fnsMF345o3&package=gold&site=' . '&site=' . get_option( 'siteurl' ))), true);
echo '<div class="notice notice-success is-dismissible">
        <p><strong>UK Tax & MOT Checker:</strong> You have upgraded to Gold API access.</strong>
		</p>
    </div>';
update_option("UTMCCreditUs", "yes");
$CurrentSupporter = 'yes';
}else{

$json_api_upgrade_req = json_decode(wp_remote_retrieve_body(wp_remote_get( 'https://www.rapidcarcheck.co.uk/FreeAccess/RequestsChange.php?auth=LeCc0xMsd00fnsMF345o3&package=bronze&site=' . '&site=' . get_option( 'siteurl' ))), true);
echo '<div class="notice notice-success is-dismissible">
        <p><strong>UK Tax & MOT Checker:</strong> You have downgraded to Bronze API access.</strong>
		</p>
    </div>';
update_option("UTMCCreditUs", "no");
$CurrentSupporter = 'no';	
}	
}

// API access status check
$json_api_usage = json_decode(wp_remote_retrieve_body(wp_remote_get( 'https://www.rapidcarcheck.co.uk/FreeAccess/Requests.php?auth=LeCc0xMsd00fnsMF345o3&site=' . '&site=' . get_option( 'siteurl' ))), true);

if ($DisableAPIStats == 'yes') {
if (isset(($json_api_usage["APIAccess"]))){
update_option( 'UTMCDisableEndPoint', 'no' );
}
}


//API limit and package check/sync [Plugin Update Bug Fix], check API server package and auto sync
if (isset(($json_api_usage["Limit"]))){
if ($json_api_usage["Limit"] == '20'){

//if (file_exists($CreditUsFile)) {
if ($CreditUsStats == 'no'){	
// do nothing, API and WP backend match
}else{
// SYNC with API to downgrade to bronze [ to match both API and backend stats ].
update_option("UTMCCreditUs", "no");
$CreditUsStats = 'no';	
}
}else{
// server set to gold API [automatically upgrade in WP backend]
update_option("UTMCCreditUs", "yes");
$CreditUsStats = 'yes';
}
}



echo '<h1 class="fvduk-title">UK Tax & MOT Status Checker</h1><small><i>by RapidCarCheck.co.uk</i></small>';
	
if ($DisableAPIStats == 'yes') {
	
if (isset($_GET["first"])){
$AdminReturnURLAfterSignup12 = get_admin_url( null, null, null ) . 'admin.php?page=uk-tax-mot-checker';

	
	echo '<h3>Thanks for signing up!</h3>';
	echo '<div style="height:5px" aria-hidden="true" class="admin-spacer"></div>';
	echo '<p class="backend-font-heavy">You have signed up! Click the button below to access your admin backend</p>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';	
	echo '<div class="gen_button_sign"><a class="sign-up-button1" href="'.$AdminReturnURLAfterSignup12.'"><b>ACCESS ADMIN BACKEND NOW!</b></a></div>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
	
	
}else{
		
	
//API sign up required..
$AdminReturnURLAfterSignup1 = get_admin_url( null, null, null ) . 'admin.php?page=uk-tax-mot-checker';


	echo '<h3>Thank you for installing Free Vehicle Data UK Plugin!</h3>';
	echo '<div style="height:5px" aria-hidden="true" class="admin-spacer"></div>';
	echo '<p class="backend-font-heavy">To activate plugin/API endpoint sign-up free below (instant):</p>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
	echo '<div class="gen_button_sign"><a class="sign-up-button1" href="https://www.rapidcarcheck.co.uk/free-api-access/?site=' . get_option( 'siteurl' ) . '&return=' . $AdminReturnURLAfterSignup1 . '" target="_blank"><b>SIGN UP FREE NOW!</b></a></div>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
	echo '<p class="backend-font-heavy">Why Do i Need to Sign Up?</p>';
	echo '<p class="backend-font-light">Sign up takes less than 30 seconds and only requires an email address, we require sign up to keep things fair and avoid abuse of the free API service.</p>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
	
	}

}else{
// activated, admin backend
	echo '<div style="height:32px" aria-hidden="true" class="admin-spacer"></div>';

	if ($CurrentSupporter == 'yes'){
	echo '<p class="backend-font-heavy-big"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png"> Daily API Usage</p>';
	}else{
	echo '<p class="backend-font-heavy-big"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/bronze-star.png"> Daily API Usage</p>';	
	}
	
	//usage statistics section
	echo '<p class="backend-api-stats">Usage statistics for: <strong>' . get_option( 'siteurl' ) . '</strong></p>';
	echo '<p class="backend-api-stats">Lookups made today: <strong>' . ($json_api_usage["RequestsMade"]) . ' / ' . ($json_api_usage["Limit"]) . '</strong></p>';
	echo '<p class="backend-api-stats">Lookups remaining today: <strong>' . ($json_api_usage["RequestsLeft"]) . '</strong></p>';
	if ($CurrentSupporter == 'yes'){
	echo '<div style="height:2px" aria-hidden="true" class="admin-spacer"></div>';
	echo '<p class="backend-api-stats-small">Request bigger limit, email us: enquiries@rapidcarcheck.co.uk</p><br>';
	}
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';

	if ($CurrentSupporter == 'yes') {
	echo '<h2>Last 100 Vehicle Searches</h2>';
	
	//LOG FILE OUTPUT:
	$LogFile = plugin_dir_path(__FILE__) . 'assets/log.txt';
	
	if (file_exists($LogFile)){
	
	$fileforlog = file($LogFile);
	$xlogcount = count($fileforlog);	
	$searchfilesize = file($LogFile, FILE_IGNORE_NEW_LINES);
	$limitsearch = 100;
	
	echo '<textarea id="searchlog" class="backendsearchfvd" class="box" rows="10">';
	//return log file..
	foreach($searchfilesize as $searchfilesize){
	if ($limitsearch <=0){
	}else{
	--$limitsearch;	
	--$xlogcount;
	echo $fileforlog[$xlogcount];	
	}
	}
	echo '</textarea>';	
	echo '<br>To view all ' . count($fileforlog) . ' search records, <a href="' . plugin_dir_url(__FILE__) . 'assets/log.txt" target="_blank">View the full search log now</a>';
	echo '<div style="height:32px" aria-hidden="true" class="admin-spacer"></div>';
	}else{
	echo 'Search log will appear here once first search is made!';
	echo '<div style="height:32px" aria-hidden="true" class="admin-spacer"></div>';	
	}
	}
	
	// how to use plugin area
	echo '<p class="backend-font-heavy-big"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/question.png"> How To Use This Plugin</p>';	
	echo '<p class="clear-font-text">Simply use the shortcode below to show the tax/mot search box:</p>';
	echo '<p class="clear-font-text"><strong>[uk_taxmotstatus]</strong></p>';
	echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
//	echo '<p class="clear-font-text">How to Change Results Alignment:</p>';
//	echo '<p class="clear-font-text"><strong>[uk_taxmotstatus align=left]</strong> OR <strong>[uk_taxmotstatus align=center]</strong> OR <strong>[uk_taxmotstatus align=right]</strong></p>';
	echo '<div style="height:22px" aria-hidden="true" class="admin-spacer"></div>';

	//supporter section
//	if (file_exists($CreditUsFile)) {//is not supporter...
if ($CreditUsStats == 'no'){	
	// offer upgrade to 500 option	
echo '<p class="backend-font-heavy-big"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/bronze-star.png"> Bronze API Access</p>';
echo '<p class="clear-font-text">You have bronze API access which includes (20 free vehicle lookups a day).</p>';
echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
echo '<p class="clear-font-text"><strong>Upgrade to FREE gold access now:</strong></p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> <strike>20</strike> 500 free vehicle lookups a day.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> Backend search log of last 100 vehicle searches.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> Instant upgrade with no waiting.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> No payment or further details required.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> Priority email support.</p>';
echo '<div style="height:10px" aria-hidden="true" class="admin-spacer"></div>';
echo '<form id="creditlink" method="POST" action="">
	<input name="credit" id="credit" type="hidden" value="yes">
    <div class="gen_button_sign"><button type="submit" class="upgrade_fvd_button" id="Reg">UPGRADE TO GOLD API [FREE / INSTANT]</button></div></form>';
	
echo '<p class="backend-font-light">We offer free gold API access to users who show us their support by adding a powered by rapid car check link, this link is automatically added under the vehicle search box when you upgrade.</p>';
echo '<p class="backend-font-light">We have spent many hours making this free and easy to use plugin and would really appreciate your support.</p>';
	
	}else{
// offer downgrade to 20 option
echo '<div style="height:22px" aria-hidden="true" class="admin-spacer"></div>';
echo '<p class="backend-font-heavy-big"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png"> Gold API Access</p>';
echo '<p class="clear-font-text">You have gold API access which includes:</p>';
echo '<div style="height:12px" aria-hidden="true" class="admin-spacer"></div>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> 500 free vehicle lookups a day.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> Vehicle search logs.</p>';
echo '<p class="clear-font-text"><img src="' . plugin_dir_url(__FILE__) . '/assets/images/gold-star.png" height="16" width="16"> Priority email support (enquiries@rapidcarcheck.co.uk).</p>';
echo '<div style="height:10px" aria-hidden="true" class="admin-spacer"></div>';

echo '<form id="creditlink" method="POST" action="">
	<input name="credit" id="credit" type="hidden" value="no">
    <div class="gen_button_sign"><button type="submit" class="downgrade_fvd_button" id="Reg">DOWNGRADE TO BRONZE</button></div></form>';
echo '<p class="backend-font-light">If you downgrade API access, daily vehicle lookups will be limited to 20 a day.</p>';
		
	}
	
	// Resources area
	echo '<div style="height:22px" aria-hidden="true" class="admin-spacer"></div>';	
	echo '<p class="backend-font-heavy-big"> Resources and Help</p>';	
	echo '<p class="clear-font-text">> <a href="https://www.youtube.com/watch?v=m86PVRgzGDY" target="_blank">Video Installation Guide and Usage Demo</a></p>';
	echo '<p class="clear-font-text">> <a href="https://wordpress.org/support/plugin/uk-tax-and-mot-checker/reviews/" target="_blank">Please Rate This Plugin on WordPress.org</a></p>';
	echo '<p class="clear-font-text">> <a href="https://wordpress.org/plugins/free-vehicle-data-uk/" target="_blank">Try Our Free Vehicle Data WordPress Plugin</a></p>';
	echo '<div style="height:22px" aria-hidden="true" class="admin-spacer"></div>';	
	}
}
?>