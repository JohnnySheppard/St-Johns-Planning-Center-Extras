<?php
require_once("include/check_logged_in.php");
require_once("include/housekeeping.php");
$is_logged_in = check_logged_in();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="default" />
		<link rel="apple-touch-icon" href="images/icon.png" />
		<link rel="apple-touch-startup-image" href="images/load.png" />
		<title>
		SJ Rotas
        </title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
        <style>
            /* App custom styles */
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js">
        </script>
		<link rel="stylesheet" href="css/add2home.css">
		<link rel="stylesheet" href="css/main.css">
		<script type="application/javascript" src="js/add2home.js"></script>
		<?php
			include("include/google_analytics.inc.php");
		?>
    </head>
    <body>
        <?php
			if ($is_logged_in){
				echo home_page();
				echo not_logged_in();
			}
			else {
				echo not_logged_in();
				echo home_page();
			}
		?>
		<div data-role="page" id="About">
            <div data-theme="b" data-role="header">
                <h3>
                    St John's Rotas
                </h3>
            </div>
            <div data-role="content">
                <div>
                    <h2>
                        About
                    </h2>
                    <b>
                        Version:
                    </b>
                    2.0
                    <br />
                    <b>
                        Written By:
                    </b>
                    Johnny Sheppard
                </div>
                <a data-role="button" data-direction="reverse" data-transition="slidedown" href="index.php" data-icon="delete" data-iconpos="right">
                    Close
                </a>
            </div>
        </div>
		<div data-role="page" id="login">
            <div data-theme="b" data-role="header">
			<h3>Log In</h3>
			</div>
            <div data-role="content">
				<form method="post" action="login.php" data-ajax="false">
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput1">
                        </label>
                        <input id="textinput1" name="logon_email" placeholder="Email" value="" type="email" />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput2">
                        </label>
                        <input id="textinput2" name="logon_password" placeholder="Password" value="" type="password" />
                    </fieldset>
                </div>
                <input type="submit" data-icon="arrow-r" data-iconpos="right" value="Log In" />
				<center><a href="forgotten.php">Forgotten Password?</a></center>
			</form>
			</div>
        </div>
        <script>
            //App custom javascript
        </script>
    </body>
</html>
<?php
function home_page(){
	$data =  '
		<div data-role="page" id="Home">
            <div data-theme="b" data-role="header">
                <h3>
                    St John\'s Rotas
                </h3>
				<a data-role="button" href="logout.php" id="login_button" data-transition="fade">Log Out</a>
                <a data-role="button" data-transition="slidedown" href="#About" data-icon="info" data-iconpos="right">
                    About
                </a>
            </div>
			<form action="plans.php" method="GET">
            <div data-role="content">
				<div>
					<b>
						Pick the services you\'re interested in:
					</b>
				</div>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup" data-type="vertical">
						<legend>
							Choose:
						</legend>';
				
				$url = sprintf("https://www.planningcenteronline.com/organization.xml");
				$response = get_pco_data($url);

				$organization = simplexml_load_string($response);
				
				foreach ($organization->{'service-types'}->{'service-type'} as $service){
					$data .=  '<input name="type_' . $service->id . '" id="type_' . $service->id . '" type="checkbox" value="' . $service->name . '" />';
					$data .=  '<label for="type_' . $service->id . '">';
					$data .=  $service->name . '';
					$data .=  '</label>';
				}
				$data .=  '
					</fieldset>
				</div>
				<input type="submit" data-icon="search" data-iconpos="right" value="Go" />
            </div>
			</form>
        </div>
	';
	return $data;
}

function not_logged_in(){
	$data =  '
		<div data-role="page" id="not_logged_in">
            <div data-theme="b" data-role="header">
                <h3>
                    St John\'s Rotas
                </h3><a data-role="button"  data-rel="dialog" href="#login" id="login_button">Log In</a>
                <a data-role="button" data-transition="slidedown" href="#About" data-icon="info" data-iconpos="right">
                    About
                </a>
            </div>
			<form action="plans.php" method="GET">
            <div data-role="content">
					<div>';
	if ($_SESSION['login_failed'] == 1){
		$data .= '<h4 style="color:red;">Logon Error. Please try again!</h4>' . "\n";
		$_SESSION['login_failed'] = 0; //set to 0 so that it doesn't keep showing the error on reload.
	}
	$data .= '					<h3>
							You must Log In to use this service!
						</h3>
						<script type="application/javascript">
							if (window.navigator.standalone == true){
								document.write("<span style=\"color:red;\">IMPORTANT: If you have not connected this app to Planning Center (happens automatically the first time you log in), then PLEASE run this app from Safari rather than as an app started directly from the home screen, the first time you log in.<br><br> Otherwise the authentication won\'t work properly!</span>");
							}
						</script>
						<br><br>
						<div>
							If you have not already done so, please <a href="http://www.johnnysheppard.com/rota2/register.php">Register Here</a>
						</div>
					</div>
            </div>
			</form>
        </div>
	';
	return $data;
}