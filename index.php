<?php
session_start();
?>
<html>
<head>
<title>St John's Planning Center Extras</title>
</head>
<body>
<h1>Welcome to St John's Planning Center Extras</h1>
<?php
if (isset($_SESSION['valid_user']) && $_SESSION['valid_user'] !=0){
	echo "Hi<br><Br>";
}
?>
Please choose what you would like to do:<br>
<a href=""></a><br>
<a href=""></a><br>
<a href="login_display.php">Login</a><br>
<a href="logout.php">Logout</a><br>

</body>
</html>