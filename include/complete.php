<?php
require_once("OAuth.php");       //oauth library
require_once("common.php");      //common functions and variables
require_once("db.inc.php");

session_start();

//get request token params from cookie and parse values
$requestToken   = $_COOKIE["requestToken"];
parse_str($requestToken, $request_token_array);
$secret         = $secret;
$key            = $request_token_array["key"];
$token          = $request_token_array["token"];
$token_secret   = $request_token_array["token_secret"];
$oauth_verifier = $_GET['oauth_verifier'];

//create required consumer variables
$test_consumer  = new OAuthConsumer($key, $secret, NULL);
$req_token      = new OAuthConsumer($token, $token_secret, NULL);
$sig_method     = new OAuthSignatureMethod_HMAC_SHA1();
//echo "<p>============================================</p>";
//echo $req_token;
//echo "<p>============================================</p>";

//exchange authenticated request token for access token
$params         = array('oauth_verifier' => $oauth_verifier);
$acc_req        = OAuthRequest::from_consumer_and_token($test_consumer, $req_token, "GET", $oauth_access_token_endpoint, $params);
$acc_req->sign_request($sig_method, $test_consumer, $req_token);
$access_ret     = run_curl($acc_req->to_url(), 'GET');

// //if access token fetch succeeded, we should have oauth_token and oauth_token_secret parse and generate access consumer from values
parse_str($access_ret, $access_token);
// echo "<p>============================================</p>";
// echo print_r($access_token);
// echo "<p>============================================</p>";

$access_consumer = new OAuthConsumer($access_token['oauth_token'], $access_token['oauth_token_secret'], NULL);

// =========== NOTE: Important =======================
// The $access_token array here is what you want to save to your db for your application. 
// That way you can repull out the oauth_token and oauth_token_secret any time you wish
// and re-initiate the consumer without going back to the oauth authorize page.
// 
// The Array looks something like this:
// access_token: Array ( [oauth_token] => 3SBONsKwtq3tOaMS8QXq [oauth_token_secret] => fw518viDIL95oRS3WP279WcuUrJb5q2F0FxXOu7A ) 1
// 
// So you might store this data to 2 fields in your database called consumer_aouth_token 
// and consumer_oauth_token_secret or the whole array into a serialized column in your table 
// (if using mysql for your db)
// 
// $access_consumer = new OAuthConsumer($consumer_oauth_token, $consumer_oauth_token_secret, NULL);
/*$consumer_oauth_token = '5IIv1CmwR2AdZgniCJQX';
$consumer_oauth_token_secret = 'uviNsnHmKeIgqMBCnlHV7cXmZe9u3PXfHoVOBLzt';
$consumer_oauth_token = '';
$consumer_oauth_token_secret = '';
$access_consumer = new OAuthConsumer($consumer_oauth_token, $consumer_oauth_token_secret, NULL);
*/

//Update the users planning center access_token + secret
if (isset($_SESSION['valid_user']) && ($_SESSION['valid_user'] > 0)){
	$query = "UPDATE `pico_users` SET `access_token` = '" . $access_token['oauth_token'] . "', `access_token_secret`='" . $access_token['oauth_token_secret'] . "' WHERE `id`=" . $_SESSION['valid_user'];
	$result = mysql_query($query);
}

$redirect = $_SESSION['calling_url'];

header("Location: $redirect");

?>