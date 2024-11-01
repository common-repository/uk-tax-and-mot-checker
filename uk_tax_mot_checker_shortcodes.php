<?php
    if (!defined('ABSPATH')) exit;
    // Script for handling short codes.

    // Shortcodes available
	add_shortcode("uk_taxmotstatus",'uk_taxmotstatus');
	
	function uk_taxmotstatus($atts) {
		
	wp_enqueue_script( 'uktaxmotcheckscript', plugin_dir_url( __FILE__ ) . 'assets/js/TaxMotChecker.js', array('jquery'), '1.0' );

	
	$scriptData = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'loadinghtm' => '<h3 style="text-align:center;"> <img src="'.plugin_dir_url( __FILE__ ) . 'assets/images/car-loader.gif'.'" alt="Loading" class="centerloadingimg1" width="246" height="150"><div style="height:20px" aria-hidden="true" class="mot-spacer"></div> </h3>',
    );	
	wp_localize_script('uktaxmotcheckscript', 'my_options', $scriptData);
		
	$VehicleSearchBox = '<div class="RapidCarCheck"><input id="RegSearchBox" class="RegSearchBoxT" maxlength="9" name="Reg" placeholder="ENTER REG" type="text" value=""></div>
    <div class="RapidCarCheck"><button class="vehicle-btn-searchT" onclick="checkthestatus()" id="Reg">Check Vehicle Now >></button></div>';	
	
	$VehicleSearchBox = '<div id="content123"><h2 class="has-text-align-center">Check Tax & MOT Status</h2></div>'. $VehicleSearchBox;
	
	// check if gold API member or not
	if (get_option( 'UTMCCreditUs' ) == 'yes'){
	if (get_option( 'UTMCCreditLink' ) == false){
	$VehicleSearchBox = $VehicleSearchBox . '<p class="vehiclecredit"><a href="https://www.rapidcarcheck.co.uk/developer-api/" target="_blank">Powered By Rapid Car Check</a></p>';		
	}else{
	$CreditLink = get_option( 'UTMCCreditLink' );
	$VehicleSearchBox = $VehicleSearchBox . '<p class="vehiclecredit"><a href="'.$CreditLink.'" target="_blank">Powered By Rapid Car Check</a></p>';		
	}
	}
	
	//periodically check API access level and SYNC locally
	if (get_option( 'RandomAPIAccLevel' ) == false){
	add_option( 'RandomAPIAccLevel', 1 );//set option / not already set
	}
	if (get_option( 'RandomAPIAccLevel' ) >= 10){
	//check API level and sync // Periodic API access status check
	$json_api_usage1 = json_decode(wp_remote_retrieve_body(wp_remote_get( 'https://www.rapidcarcheck.co.uk/FreeAccess/Requests.php?auth=LeCc0xMsd00fnsMF345o3&site=' . '&site=' . get_option( 'siteurl' ))), true);
	if (isset(($json_api_usage1["Limit"]))){
	if ($json_api_usage1["Limit"] == '20'){
	$CreditUsStats1 = get_option('UTMCCreditUs');
	if ($CreditUsStats1 == 'no'){	
	// do nothing, API and WP backend match
	}else{
	// SYNC with API to downgrade to bronze [ to match both API and backend stats ].
	update_option("UTMCCreditUs", "no");
	}
	}else{
	// server set to gold API [automatically upgrade in WP backend]
	update_option("UTMCCreditUs", "yes");
	}
	}		
	//reset periodic check counter
	update_option("RandomAPIAccLevel", 1);	
	}else{
	//increment periodic check counter
	$CounterSet = get_option( 'RandomAPIAccLevel' );
	update_option("RandomAPIAccLevel", $CounterSet+1);
	}
			
	return $VehicleSearchBox;
	}
	
?>