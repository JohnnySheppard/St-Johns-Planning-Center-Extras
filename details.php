<?php
require_once("include/check_logged_in.php");
$is_logged_in = check_logged_in();
$pages = "";
if (isset($_GET["plan_id"])){
	$plan_id = $_GET["plan_id"];
}
else {
	$plan_id = 0;
}
date_default_timezone_set("UTC");

        echo '<div data-role="page" id="plan_' . $plan_id . '">' . "\n";
?>
	<div data-theme="b" data-role="header">
		<h3>
			St John's Rotas
		</h3>
		<a data-role="button" data-direction="reverse" data-transition="slide" href="#Plans" data-rel="back" data-icon="arrow-l" data-iconpos="left">Back</a>
		<a data-role="button" data-direction="reverse" data-transition="slide" href="#Home" data-icon="home" data-iconpos="left">Home</a>
	</div>
	<div data-role="content">
		<?php
		$item_count = 0;
		$url = sprintf("https://www.planningcenteronline.com/plans/" . $plan_id . ".xml");
		$response = get_pco_data($url);
		$plan_details = simplexml_load_string($response);
		$old_category = "";
		$current_category = "";
		
		$pages .= '
			<div data-role="collapsible" data-collapsed="false" data-content-theme="d">
			<h3>Service Details</h3>
				<div>' . "\n";
		$pages .= "<b>Service Title: </b>" . $plan_details->{'series-title'} . " - " . $plan_details->{'plan-title'} . "<br />";
		$pages .= "<b>Service Date: </b>" . $plan_details->dates . "<br />";
		$pages .= "<b>Service Time: </b>" . date("g:i a",strtotime($plan_details->{'service-times'}->{'service-time'}->{'starts-at'}));
		$pages .='		</div>
			</div>
		';
		
		foreach ($plan_details->{'plan-people'}->{'plan-person'} as $person){
			$current_category = (string)$person->{'category-name'};
			if ($old_category != $current_category){
				if ($item_count > 0){
					$pages .= '	</ul>' . "\n";
					$pages .= '	</div>' . "\n";
					$pages .= '</div>' . "\n";
				}
				$pages .= '<div data-role="collapsible" data-collapsed="false" data-content-theme="d">' . "\n";
				$pages .= '<h3>' . $person->{'category-name'} . '</h3>' . "\n";
				$pages .= '	<div>' . "\n";
				$pages .= '	<ul data-role="listview" data-divider-theme="b" data-inset="true">' . "\n";

				$old_category = $current_category;
			}
			$pages .= '		<li data-theme="c"><a href="contact.php?contact_id=' . $person->{'person-id'} . '">' . $person->position . ': <span class="reply_' . $person->status . '">' . $person->{'person-name'} . '</span></a></li>' . "\n";
			$item_count++;
		}
		if ($item_count > 0){
			$pages .= '	</ul>' . "\n";
			$pages .= '	</div>' . "\n";
			$pages .= '</div>' . "\n";
		}
		
		echo $pages;
		
		?>
	</div>
</div>
