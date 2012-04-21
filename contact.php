<?php
require_once("include/check_logged_in.php");
$is_logged_in = check_logged_in();
$pages = "";
if (isset($_GET["contact_id"])){
	$contact_id = $_GET["contact_id"];
}
else {
	$contact_id = 0;
}
    echo '<div data-role="page" id="contact_' . $contact_id . '">' . "\n";
?>
	<div data-theme="b" data-role="header">
		<h3>
			St John's Rotas
		</h3>
		<a data-role="button" data-direction="reverse" data-transition="slidefade" href="#Plans" data-rel="back" data-icon="arrow-l" data-iconpos="left">Back</a>
		<a data-role="button" data-direction="reverse" data-transition="slidefade" href="#Home" data-icon="home" data-iconpos="left">Home</a>
	</div>
	<div data-role="content">
		<?php
		$url = sprintf("https://www.planningcenteronline.com/people/" . $contact_id . ".xml");
		$response = get_pco_data($url);
		$contact_details = simplexml_load_string($response);
		$old_category = "";
		$current_category = "";
		
		$pages .= '<ul data-role="listview" data-divider-theme="b" data-inset="true">';
		$pages .= '		<li data-dividertheme="b" data-role="list-divider">Email</li>' . "\n";
		foreach ($contact_details->{'contact-data'}->{'email-addresses'}->{'email-address'} as $email){
			$pages .= '		<li data-theme="c"><a href="mailto:' . $email->address . '">' . $email->location . ': ' . $email->address . '</a></li>' . "\n";
		}
		$pages .= "</ul>";
		
		$pages .= '<ul data-role="listview" data-divider-theme="b" data-inset="true">';
		$pages .= '		<li data-dividertheme="b" data-role="list-divider">Phone Number</li>' . "\n";
		foreach ($contact_details->{'contact-data'}->{'phone-numbers'}->{'phone-number'} as $phone){
			$pages .= '		<li data-theme="c"><a href="tel:' . $phone->number . '">' . $phone->location . ': ' . $phone->number . '</a></li>' . "\n";
		}

		$pages .= "</ul>";			

		echo $pages;

		?>
	</div>
</div>
