<?php
	//if (!ini_get('display_errors')) {
		ini_set('display_errors', 1);
	//}
	error_reporting(E_ALL);
	
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
		SJ Rotas
        </title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js">
        </script>
		<?php
			include("include/google_analytics.inc.php");
		?>
    </head>
    <body>
<?php

require_once("include/db.inc.php");
require_once("include/phpmailer.inc.php");
$saved = $user_id = 0;
$fname = $lname = $email = $password = $error = "";

if ((isset($_POST["email_address"])) && ($_POST["email_address"] != "")){
	//register the user.
	if (isset($_POST["fname"])){
		$fname = $_POST["fname"];
	}
	if (isset($_POST["lname"])){
		$lname = $_POST["lname"];
	}
	if ((isset($_POST["password"])) && (isset($_POST["confirm"]) && ($_POST["password"] == $_POST["confirm"]))){
		$password = crypt($_POST["password"]);
	}
	//we already know $_POST["email"] is set.
	$email = $_POST["email_address"];

	$query = "INSERT INTO `pico_users`(`firstname`,`surname`,`email`,`password`) VALUES('" . $fname . "','" . $lname . "','" . $email . "','" . $password . "')";
	$result = mysql_query($query);
	if ($result){
		$user_id = mysql_insert_id();
	}
	else {
		$error = "Registration Failed. Are you already registered?";
	}
	if ($user_id > 0){
		$rand_str = genRandomString();
		$query = "INSERT INTO `pico_activate`(`id`,`rand_str`,`date_time`) VALUES(" . $user_id . ",'" . $rand_str . "'," . time() . ")";
		$result = mysql_query($query);
		if ($result){
			$saved = 1;
			//Mail Administrator
			$mail = new phpmailer;
			$mail->From = "no_reply@johnnysheppard.com";
			$mail->FromName = "St John's Rotas 2";

			$mail->AddAddress($admin_email);   // email set in include/db.inc.php
			$mail->WordWrap = 50;    // set word wrap

			$mail->IsHTML(true);    // set email format to HTML
			$mail->Subject = "New User for St John's Rota's 2";
			$body = "Hi<br><br>New User ($fname $lname - $email)<br><br> Activate: http://www.johnnysheppard.com/rota2/activate_user.php?rand_str=" . $rand_str . "&delete=no";
			$body .= "<br><br>Delete User: http://www.johnnysheppard.com/rota2/activate_user.php?rand_str=" . $rand_str . "&delete=yes";
			$body .= "<br><br>Thanks.";
			$mail->Body = $body;
			$mail->Send(); // send message
		}
		else {
			$error = "Registration Failed";
		}
	}
}
if ($saved == 0){
	echo '
        <div data-role="page" id="register_page">
            <div data-theme="b" data-role="header">
                <h3>
                     St John\'s Rotas
                </h3>
            </div>
            <div data-role="content">
                <div>';
	if ($error != ""){
		echo "<h4 style=\"color:red;\">" . $error . "</h4>\n";
	}	
    echo '                Please enter your details and click <b>Save</b>.
                </div>
				<form action="register.php" method="POST" data-ajax="false">
					<div data-role="fieldcontain">
						<fieldset data-role="controlgroup">
							<label for="textinput1">
								Firstname:
							</label>
							<input id="textinput1" name="fname" placeholder="" value="" type="text" />
						</fieldset>
						<fieldset data-role="controlgroup">
							<label for="textinput2">
								Surname:
							</label>
							<input id="textinput2" name="lname" placeholder="" value="" type="text" />
						</fieldset>
						<fieldset data-role="controlgroup">
							<label for="textinput3">
								Email Address:
							</label>
							<input id="textinput3" name="email_address" placeholder="" value="" type="email" />
						</fieldset>
						<fieldset data-role="controlgroup">
							<label for="textinput4">
								Password:
							</label>
							<input id="textinput4" name="password" placeholder="" value="" type="password" />
						</fieldset>
						<fieldset data-role="controlgroup">
							<label for="textinput5">
								Confirm Password:
							</label>
							<input id="textinput5" name="confirm" placeholder="" value="" type="password" />
						</fieldset>
					</div>
					<input type="submit" data-icon="check" data-iconpos="right" value="Save" />
				</form>
				</div>
        </div>';
}
else {
	echo '
        <div data-role="page" id="new_register">
            <div data-theme="b" data-role="header">
                <h3>
                     St John\'s Rotas
                </h3>
            </div>
            <div data-role="content">
                <div>
                    Thank you, an email has been sent to the Administrator. Once he\'s activated you, you will recieve an email, and you can log in.
                </div>
				<div>
					<a data-role="button" href="index.php" data-icon="home" data-iconpos="right">Home</a>
				</div>
			</div>
        </div>';
}

function genRandomString() {
    $length = 20;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

?>
    </body>
</html>