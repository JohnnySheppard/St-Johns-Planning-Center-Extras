<?php
session_start();

$_SESSION['valid_user'] = NULL;

if (isset($_SESSION['login_referal'])){
	header("Location: " . $_SESSION['login_referal']);
}
else {
	header("Location: index.php");
}

?>