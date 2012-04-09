<?php
include("include/db.inc.php");
session_start();

$password = "";

if (isset($_POST['logon_email']) && isset($_POST['logon_password'])) {

	$query = "select password from pico_users"			//find the password "salt"
		. " where email = '" . $_POST['logon_email'] . "'";
	$result = mysql_query($query);
	if ($result) {
		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			if (($row["password"] == "") && ($_POST['password'] == "")) {
				$password = "";
			}
			else {
				$password = crypt($_POST['logon_password'],$row["password"]); //hash the new password entry using the "salt" from  the stored password, so that it's not created randomly
			}
		}
		
	}
	$query = "SELECT * FROM pico_users WHERE email='" . $_POST['logon_email'] . "' AND password='" . $password . "'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
    	$row = mysql_fetch_array($result);
    	$firstname = $row['firstname'];
		$lastname = $row['surname'];
		$userid = $row['id'];
		$active = $row['active'];
		
		$_SESSION['valid_user'] = $userid;
		$_SESSION['login_failed'] = Null;
		$rand_str = genRandomString();
		$query2 = "UPDATE pico_users SET `login_token` = '" . $rand_str . "' WHERE email='" . $_POST['logon_email'] . "' AND password='" . $password . "'";
		$result2 = mysql_query($query2);
		setcookie("login",$rand_str,(time() + 15552000));
		
    }
    else {
		$_SESSION['login_failed'] = 1;
	}

	if (isset($_SESSION['login_referal'])){
		header("Location: " . $_SESSION['login_referal']);
	}
	else {
		header("Location: index.php");
	}
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