<?php

require_once("../include/db.inc.php");

$firstname = "Johnny";
$surname = "Sheppard";
$email = "planningcenter1@johnnysheppard.com";
$password = crypt("Password");

$query = "INSERT INTO `pico_users` (`firstname`,`surname`,`email`,`password`) VALUES ('$firstname','$surname','$email','$password')";
$result = mysql_query($query);

?>