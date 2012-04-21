<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
		SJ Rotas
        </title>
        <link rel="stylesheet" href="css/jquery.mobile-1.1.0.min.css" />
        <script src="js/jquery.min.js">
        </script>
        <script src="js/jquery.mobile-1.1.0.min.js">
        </script>
	</head>
    <body>
<?php
require_once("include/db.inc.php");

$saved = $id = 0;
$rand_str = $password = $error = "";

if ((isset($_GET["rand_str"])) && ($_GET["rand_str"] !="")){
	$rand_str = $_GET["rand_str"];
}

if ((isset($_POST["password"])) && (isset($_POST["confirm"])) && (isset($_POST["rand_str"]))){
	if ((isset($_POST["rand_str"])) && ($_POST["rand_str"] != "")){
		$rand_str = $_POST["rand_str"];
	}

	if (($_POST["password"] != "") && ($_POST["password"] == $_POST["confirm"])){
		$password = crypt($_POST["password"]);
	}
	else {
		$error = "Passwords do NOT match!";
	}
	if ($password != ""){
		$query = "SELECT `user_id` FROM `pico_password_reset` WHERE `rand_str` = '" . $rand_str . "'";
		$result = mysql_query($query);
		if ($result){
			if (mysql_num_rows($result) == 1){
				$row = mysql_fetch_array($result);
				$id = $row["user_id"];
			}
		}
		if ($id > 0){
			$query = "UPDATE `pico_users` SET `password` = '" . $password . "' WHERE `id`=" . $id;
			$result = mysql_query($query);
			if ($result){
				$saved = 1;
			}
			else {
				$error = "Password Not Updated!";
			}
			$query = "DELETE FROM `pico_password_reset` WHERE `rand_str` = '" . $rand_str . "'";
			$result = mysql_query($query);
		}
		else {
			$error = "Could not find User in database!";
		}
	}
}

if ($saved == 0){
	echo '
        <div data-role="page" id="new_password">
            <div data-theme="b" data-role="header">
                <h3>
                     St John\'s Rotas
                </h3>
            </div>
            <div data-role="content">
                <div>';
	if ($error != ""){
		echo "<h4 style=\"color:red;\">" . $error . "</h4>";
	}	
    echo '                Please enter your new password and click <b>Save</b>.
                </div>
				<form action="reset.php" method="POST" data-transition="flip">
					<div data-role="fieldcontain">
						<fieldset data-role="controlgroup">
							<label for="textinput1">
								New Password:
							</label>
							<input id="textinput1" name="password" placeholder="" value="" type="password" />
						</fieldset>
						<fieldset data-role="controlgroup">
							<label for="textinput2">
								Confirm Password:
							</label>
							<input id="textinput2" name="confirm" placeholder="" value="" type="password" />
						</fieldset>
					</div>
					<input type="hidden" name="rand_str" value="' .  $rand_str . '" />
					<input type="submit" data-icon="check" data-iconpos="right" value="Save" />
				</form>
				</div>
        </div>';
}
else {
	echo '
        <div data-role="page" id="new_password">
            <div data-theme="b" data-role="header">
                <h3>
                     St John\'s Rotas
                </h3>
            </div>
            <div data-role="content">
                <div>
                    Thank you, your password has been updated.
                </div>
				<div>
					<a data-role="button" data-ajax="false" href="index.php" data-icon="home" data-iconpos="right">Home</a>
				</div>
			</div>
        </div>';
}

?>
    </body>
</html>