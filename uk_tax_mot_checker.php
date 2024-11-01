<?php

if (!defined('ABSPATH'))exit;
    /*
    Plugin Name: UK Tax and Mot Checker
    Plugin URI:  https://www.rapidcarcheck.co.uk/
    Description: Add a free UK Road Tax and Mot Checker To your website via the free Rapid Car Check API.
    Version:     1.1.8
    Author:      Rapid Car Check
    Author URI:  https://www.rapidcarcheck.co.uk/about/
    License:     GPL2

    UK Tax and Mot Checker: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.

    UK Tax and Mot Checker WordPress Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Free Vehicle Data UK WordPress Plugin. If not, see https://www.gnu.org/licenses/gpl.txt.
    */
    if ( !defined('ABSPATH') ) {
        die("-1");   
    }
    
    require_once('uk_tax_mot_checker_shortcodes.php');
    require_once('admin_area.php');
	
	add_action( 'wp_enqueue_scripts', 'uk_tax_mot_checker_plugin_assets' );
    function uk_tax_mot_checker_plugin_assets() {
	   wp_enqueue_style( 'utmcpstyle', plugin_dir_url( __FILE__ ) . 'assets/css/stylex.css' );
    }
	
	
	function uk_mot_tax_checker_jsoncall() {
	//function for returning tax mot data
	$json_return = json_decode(wp_remote_retrieve_body(wp_remote_get( 'https://www.rapidcarcheck.co.uk/FreeAccess/' . '?vrm=' . preg_replace('/\s+/', '', $_POST['Reg']) . '&auth=ACCESSAPIENDPOINT' . '&site=' . get_option( 'siteurl' ))), true);
	
	if ($StyleAlign == null){
	$StyleAlign = 'TaxMotResultsCenter5';
	}
	
	$emptyspace10 = '<div style="height:10px" aria-hidden="true" class="mot-spacer"></div>';
	$emptyspace22 = '<div style="height:22px" aria-hidden="true" class="mot-spacer"></div>';
	
	
	// TAX TITLE:
	$RoadTaxStatus = ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["RoadTaxStatusDescription"]);
	if ($RoadTaxStatus == 'Taxed'){
	$RTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . 'assets/images/tick.png"><span style="color: #058A23;"> TAX</span></h2>';	
	}
	if ($RoadTaxStatus == 'Untaxed'){
	$RTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . 'assets/images/alert.png"><span style="color: #C75000;"> TAX</span></h2>';	
	}
	if ($RoadTaxStatus == 'SORN'){
	$RTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . 'assets/images/alert.png"><span style="color: #C75000;"> TAX (SORN)</span></h2>';	
	}
	if ($RoadTaxStatus == null){
	$RTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . 'assets/images/alert.png"><span style="color: #C75000;"> TAX</span></h2>';	
	}
	
	// MOT TITLE:
	if (($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["IsMOTDue"]) == true){
	// mot is due
	$MOTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . '/assets/images/alert.png"><span style="color: #C75000;"> MOT</span></h2>';	
	}else{
	// mot not due
	$MOTtitle = '<h2> <img src="' . plugin_dir_url(__FILE__) . '/assets/images/tick.png"><span style="color: #058A23;"> MOT</span></h2>';	
	}
		
	$part1 = '<h2>' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["YearOfManufacture"]) . ' ' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["Make"]) . ' ' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["Model"]) . ', ' . ($json_return["Results"]["InitialVehicleCheckModel"]["Vrm"]) . '</h2>';
	$part2 = $emptyspace22;
	$part3 = $RTtitle;
	$part4 = '<p>Tax Due: ' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["DateRoadTaxDue"]) . ' (' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["DaysLeftUntilRoadTaxDue"]) . ')</p>';
	$part5 = $MOTtitle;
	$part6 = '<p>Mot Due: ' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["DateMotDue"]) . ' (' . ($json_return["Results"]["InitialVehicleCheckModel"]["BasicVehicleDetailsModel"]["DaysLeftUntilMotDue"]) . ')</p>';
	$part7 = $emptyspace10 . '<h3 class="titleheading">Search Again..</h3>' . $emptyspace22;
	
	
	$ReturnedData1 = '<div id="TaxMotResultsCenter2"><div id="rapidcarchecktaxmot" class="' . $StyleAlign . '">' . $part1 . $part2 . $part3 . $part4 . $part5 . $part6 . $part7 . "</div></div>";

	//error check
	if (($json_return["HasError"]) == true){	
	$ReturnedData1 = '<p class="vehiclenotfound">Vehicle Not Found, Please try again..</p>';
	}
	
	
	//Log Request Locally
	$RegVRM = sanitize_text_field($_POST['Reg']);
	$LogFileTM = plugin_dir_path(__FILE__) . '/assets/log.txt';
	$LocalLogTM = date("d M Y") . ' @ ' . date("H:i") . ' - ' . strtoupper($RegVRM);
	file_put_contents($LogFileTM, $LocalLogTM.PHP_EOL , FILE_APPEND);
	
	echo $ReturnedData1;
	
	die();
		
	}
	
add_action( 'wp_ajax_nopriv_uk_mot_tax_checker_jsoncall', 'uk_mot_tax_checker_jsoncall' );
add_action( 'wp_ajax_uk_mot_tax_checker_jsoncall', 'uk_mot_tax_checker_jsoncall' );	


	//PLUGIN OPTIONS [ ON PLUGIN ACTIVATION ]
	register_activation_hook( __FILE__, 'add_settings_for_uk_tax_mot_checker' );
	function add_settings_for_uk_tax_mot_checker(){
	
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

	}

	
?>