<?php
require_once("include/check_logged_in.php");
$is_logged_in = check_logged_in();
?>
<div data-role="page" id="Plans">
	<div data-theme="b" data-role="header">
		<h3>
			St John's Rotas
		</h3>
		<a data-role="button" data-direction="reverse" data-transition="slide" href="#Home" data-icon="arrow-l" data-iconpos="left">Back</a>
	</div>
	<div data-role="content">
		<?php
		if ($is_logged_in){
			echo "<div>\n";
			echo '<ul data-role="listview" data-divider-theme="b" data-inset="true">' . "\n";
			
			foreach($_GET as $key => $value){
				$plan_no = str_replace("type_","",$key);
				//echo $plan_no . ": " . $value . "<br>";
				$url = sprintf("https://www.planningcenteronline.com/service_types/" . $plan_no. "/plans.xml");
				$response = get_pco_data($url);
				$plans = simplexml_load_string($response);
				echo'<li data-role="list-divider" role="heading">' . $value . '</li>' . "\n";
				
				foreach($plans->plan as $plan){
					echo '<li data-theme="c"><a href="details.php?plan_id=' . $plan->id . '" data-transition="slide" data-prefetch>' . $plan->dates . '</a></li>' . "\n";
				}
			}
			echo '</ul>' . "\n";
			echo "</div>" . "\n";
		}
		else{
			echo '
			<div>
				<h3>
					You Must Log in to use this service!
				</h3>
			</div>';
		}
		?>
	</div>
</div>