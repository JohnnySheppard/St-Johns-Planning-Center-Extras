<?php
require_once("db.inc.php");

//work out what 1 week ago is
$delete_date = time() - 604800;

//Remove any old password reset entries.
$query = "DELETE FROM `pico_password_reset` WHERE `date_time` <= " . $delete_date;
$result = mysql_query($query);

?>