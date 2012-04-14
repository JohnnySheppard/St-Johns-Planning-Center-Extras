<?php
$sent = 0;
$error = "";
if ((isset($_GET["email_input"])) && ($_GET["email_input"] != "")){
	require_once("include/phpmailer.inc.php");
	require_once("include/db.inc.php");
	$id = 0;
	$rand_str = "";
	$email = $_GET["email_input"];
	//find the mail in the database.
	$query = "SELECT `id` FROM `pico_users` WHERE `email` = '" . $email . "'";
	$result = mysql_query($query);
	if ($result){
		if (mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$id = $row["id"];
		}
	}
	if ($id > 0){
		$rand_str = genRandomString();
		$query = "INSERT INTO `pico_password_reset`(`rand_str`,`user_id`,`date_time`) VALUES('" . $rand_str . "'," . $id . "," . time() . ")";
		$result = mysql_query($query);
		if ($result){
			//send an email.
			$mail = new phpmailer;
			$mail->From = "no_reply@johnnysheppard.com";
			$mail->FromName = "St John's Rotas 2";

			$mail->AddAddress($email);   // name is optional
			$mail->WordWrap = 50;    // set word wrap

			$mail->IsHTML(true);    // set email format to HTML
			$mail->Subject = "Password Reset";
			$body = "Hi<br><br>To reset your password, please click the link: http://www.johnnysheppard.com/rota2/reset.php?rand_str=" . $rand_str;
			$body .= "<br><br>This link will only stay active for 1 week.";
			$body .= "<br><br>Thanks.";
			$mail->Body = $body;
			$mail->Send(); // send message
			$sent = 1;
		}
	}
	else {
		$error = "Could not find User in database!";
	}
}
if ($sent == 1){
	echo '<div data-role="page" id="mail_sent">
		<div data-theme="b" data-role="header">
				<h3>
                    St John\'s Rotas
                </h3>
                <a data-role="button" data-transition="slidedown" href="#not_logged_in" data-icon="home" data-iconpos="left">
                   Home
                </a>
		</div>
		<div data-role="content">
				<div>
					An email has been sent with instructions on what to do next.
				</div>
		</div>
	</div>';
}
else {
	echo '<div data-role="page" id="forgotten">
            <div data-theme="b" data-role="header">
                <h3>
                    St John\'s Rotas
                </h3>
                <a data-role="button" data-transition="slidedown" href="#not_logged_in" data-icon="home" data-iconpos="left">
                   Home
                </a>
            </div>
			<form action="forgotten.php" method="GET">
            <div data-role="content">';
	if ($error != ""){
		echo "<h4 style=\"color:red;\">" . $error . "</h4>\n";
	}
	echo '				<div>
						<label for="email_input">Enter your email:</label>
						<input type="email" name="email_input" id="email_input" value=""  />
						<input type="submit" data-icon="search" data-iconpos="right" value="Go" />
					</div>
            </div>
			</form>
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