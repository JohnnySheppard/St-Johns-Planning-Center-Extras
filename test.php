<?php
//header("Content-type: application/xml");
require_once("include/check_logged_in.php");

if (!check_logged_in()){
	echo "Not Logged In";
}

$url = sprintf("https://www.planningcenteronline.com/organization.xml");
$response = get_pco_data($url);

$organization = simplexml_load_string($response);

foreach ($organization->{'service-types'}->{'service-type'} as $service){
	echo "<label for=\"type_" . $service->id . "\">" . $service->name . "</lable><input type=\"checkbox\" name=\"type_" . $service->id . "\" id=\"type_" . $service->id . "\"><br>\n";
	/*echo "<table border=1>\n";
	$service_id = $service->id;
	$url = sprintf("https://www.planningcenteronline.com/service_types/" . $service_id. "/plans.xml");
	$response2 = get_pco_data($url);
	$plans = simplexml_load_string($response2);
	
	foreach($plans->plan as $plan){
		echo "<tr><td>" . $plan->{'plan-title'} . "&nbsp;</td><td>" . $plan->dates . "</td></tr>\n";
	}
	echo "</table>\n";*/
}

//echo $response;


?>