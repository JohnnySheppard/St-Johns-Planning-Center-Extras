<?php
//header("Content-type: application/xml");
require_once("include/check_logged_in.php");

if (!check_logged_in()){
	echo "False";
}

// 4. Set Person id for example 1 and example 2
$person_id = "1190740";

/*$url = sprintf("https://www.planningcenteronline.com/people/$person_id.xml");
$content = '<person><first-name>Johnny</first-name><last-name>Sheppard</last-name></person>';
$response = get_pco_data($url,"PUT",$content);
*/
$url = sprintf("https://www.planningcenteronline.com/organization.xml");
$response = get_pco_data($url);

$organization = simplexml_load_string($response);

foreach ($organization->{'service-types'}->{'service-type'} as $service){
	echo "<h2>" . $service->name . "</h2>\n";
	echo "<table border=1>\n";
	$service_id = $service->id;
	$url = sprintf("https://www.planningcenteronline.com/service_types/" . $service_id. "/plans.xml");
	$response2 = get_pco_data($url);
	$plans = simplexml_load_string($response2);
	
	foreach($plans->plan as $plan){
		echo "<tr><td>" . $plan->{'plan-title'} . "&nbsp;</td><td>" . $plan->dates . "</td></tr>\n";
	}
	echo "</table>\n";
}

//echo $response;


?>