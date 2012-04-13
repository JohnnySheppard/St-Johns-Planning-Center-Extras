<?php
require_once("include/db.inc.php");
require_once("include/phpmailer.inc.php");
if (isset($_GET["rand_str"]) && isset($_GET["delete"])){

	$user_id = 0;
	$query = "SELECT * FROM `pico_activate` WHERE `rand_str` = '" . $_GET["rand_str"] . "'";
	$result = mysql_query($query);
	if ($result){
		if (mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$id = $row["id"];
		}
	}
	if ($id > 0){
		$query = "SELECT * FROM `pico_users` WHERE `id`=" . $id;
		$result = mysql_query($query);
		if ($result){
			if (mysql_num_rows($result) == 1){
				$row = mysql_fetch_array($result);
				$name = $row["firstname"] . " " . $row["surname"];
				$email = $row["email"];
			}
		}
		
		if ($_GET["delete"] == "no"){
			$query = "UPDATE `pico_users` SET `activated` = 1 WHERE `id`=" . $id;
			$result = mysql_query($query);
			echo "Activated: " . $name;
			//Email user.
			$mail = new phpmailer;
			$mail->From = "no_reply@johnnysheppard.com";
			$mail->FromName = "St John's Rotas 2";

			$mail->AddAddress($email);   // name is optional
			$mail->WordWrap = 50;    // set word wrap

			$mail->IsHTML(true);    // set email format to HTML
			$mail->Subject = "St John's Rota's 2 Activation";
			$body = "Hi<br><br>Your Account has now been activated with St John's Rotas 2";
			$body .= "<br><br>Thanks.";
			$mail->Body = $body;
			$mail->Send(); // send message
			
		}
		elseif($_GET["delete"] == "yes"){
			$query = "DELETE FROM `pico_users` WHERE `id`=" . $id;
			$result = mysql_query($query);
			echo "Deleted: " . $name;
		}
		
		$query = "DELETE FROM `pico_activate` WHERE `rand_str` = '" . $_GET["rand_str"] . "'";
		$result = mysql_query($query);
	}
}
?>