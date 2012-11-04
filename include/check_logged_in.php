<?php
require_once("db.inc.php");
require_once("keys.inc.php");
require_once("OAuth.php");
require_once("common.php"); 

session_start();

$user_id = $user_firstname = $user_lastname = $user_email = $user_access_token = $user_access_token_secret = "";
$try_to_login = 0;

function check_logged_in(){
	global $user_id, $user_firstname, $user_lastname, $user_email, $user_access_token, $user_access_token_secret, $user_fullname, $try_to_login;

	//check cookie first. If it's there and gives us a valid user, then fill in the valid user session var.
	if (isset($_COOKIE["login"])){
		$query = "SELECT * FROM `pico_users` WHERE `login_token`='" . $_COOKIE["login"] . "'";
		$result = mysql_query($query);
		if ($result){
			if (mysql_num_rows($result) == 1){
				$row = mysql_fetch_array($result);
				$user_id = $row["id"];
				$user_firstname = $row["firstname"];
				$user_lastname = $row["surname"];
				$user_fullname = $user_firstname . " " . $user_lastname;
				$user_email = $row["email"];
				$user_access_token = $row["access_token"];
				$user_access_token_secret = $row["access_token_secret"];
				$_SESSION['valid_user'] = $user_id;
				//and check that the user is connected to Planning Center. If not, then connect them.
				check_connected_to_pco($user_id);
				
				//return true
				return true;
			}
			else {
				$try_to_login = 1;
			}
		}
		else {
			$try_to_login = 1;
		}
	}
	elseif ((isset($_SESSION['valid_user']) && ($_SESSION['valid_user'] > 0)) || ($try_to_login == 1)){
		//User is logged in, so get some details from database
		$query = "SELECT * FROM `pico_users` WHERE `id`=" . $_SESSION['valid_user'];
		$result = mysql_query($query);
		if ($result){
			if (mysql_num_rows($result) == 1){
				$row = mysql_fetch_array($result);
				$user_id = $row["id"];
				$user_firstname = $row["firstname"];
				$user_lastname = $row["surname"];
				$user_email = $row["email"];
				$user_access_token = $row["access_token"];
				$user_access_token_secret = $row["access_token_secret"];
			}
		}
		//and check that the user is connected to Planning Center. If not, then connect them.
		check_connected_to_pco($user_id);
		
		//return true
		return true;
	}
	else {
		if ((isset($_SESSION['login_failed'])) && ($_SESSION['login_failed'] == 1)){
			return false;
		}
		else{
			//redirect to login page.
		}
	}
}

function check_connected_to_pco($id=0){
	global $user_access_token, $user_access_token_secret, $pco_key, $pco_secret, $request_token_endpoint, $callback_url, $authorize_endpoint;
	$needs_pco_attach = 0;
	
	if ($id > 0){
		if ((isset($user_access_token)) && ($user_access_token != "")){ //if the access token has been previously set and stored, then make sure it works.
			$url = sprintf("https://www.planningcenteronline.com/organization.xml");
			$test_consumer  = new OAuthConsumer($pco_key, $pco_secret, NULL);
			$access_consumer = new OAuthConsumer($user_access_token, $user_access_token_secret, NULL);
			
			// build and sign request
			$request = OAuthRequest::from_consumer_and_token($test_consumer,
			  $access_consumer, 
			  'GET',
			  $url, 
			  NULL);
			$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),
			  $test_consumer, 
			  $access_consumer
			);

			// make request
			$response = run_curl($request, 'GET');
			if ($response == "API call not authorized."){
				$needs_pco_attach = 1;
			}
		}
		else{ //if not, the user needs attaching to a planning center account, so flag this:
			$needs_pco_attach = 1;
		}
	
		if ($needs_pco_attach == 1){
			//grab the calling url:
			$calling_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$_SESSION['calling_url'] = $calling_url;
			
			//initialize consumer
			$consumer = new OAuthConsumer($pco_key, $pco_secret, NULL);

			//prepare to get request token
			$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
			$parsed = parse_url($request_token_endpoint);
			$params = array('oauth_callback' => $callback_url);

			//sign request and get request token
			$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $request_token_endpoint, $params);
			$req_req->sign_request($sig_method, $consumer, NULL);
			$req_token = run_curl($req_req->to_url(), 'GET');

			//if fetching request token was successful we should have oauth_token and oauth_token_secret
			parse_str($req_token, $tokens);
			$oauth_token = $tokens['oauth_token'];
			$oauth_token_secret = $tokens['oauth_token_secret'];

			//store pco_key and token details in cookie to pass to complete stage
			setcookie("requestToken", "key=$pco_key&token=$oauth_token&token_secret=$oauth_token_secret");
				   
			//build authentication url following sign-in and redirect user
			$auth_url = $authorize_endpoint . "?oauth_token=$oauth_token";
			header("Location: $auth_url");	
		
		}
	
	}
}

?>