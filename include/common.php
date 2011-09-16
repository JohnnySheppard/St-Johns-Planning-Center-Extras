<?php

$callback_url = "http://www.johnnysheppard.com/stjohns/include/complete.php"; //this needs changing so it works out where the script is located. (Rather than having the complete address hard coded.)

$debug = true;
$request_token_endpoint = "https://www.planningcenteronline.com/oauth/request_token";
$authorize_endpoint = "https://www.planningcenteronline.com/oauth/authorize";
$oauth_access_token_endpoint = "https://www.planningcenteronline.com/oauth/access_token";

/***************************************************************************
 * Function: Run CURL
 * Description: Executes a CURL request
 * Parameters: url (string) - URL to make request to
 *             method (string) - HTTP transfer method
 *             headers - HTTP transfer headers
 *             postvals - post values
 **************************************************************************/
function run_curl($url, $method = 'GET', $headers = null, $postvals = null){
    $ch = curl_init($url);
    
    if ($method == 'GET'){
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    } else {
        $options = array(
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $postvals,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 3
        );
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

function get_pco_data($url,$method = "GET",$content = Null){
	global $pco_key, $pco_secret, $user_access_token, $user_access_token_secret;

	$test_consumer  = new OAuthConsumer($pco_key, $pco_secret, NULL);
	$access_consumer = new OAuthConsumer($user_access_token, $user_access_token_secret, NULL);

	// build and sign request
	$request = OAuthRequest::from_consumer_and_token($test_consumer,
	  $access_consumer, 
	  $method,
	  $url, 
	  NULL);
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),
	  $test_consumer, 
	  $access_consumer
	);
	
	if (isset($content)){
		//define request headers
		$headers = array("Accept: application/xml");
		$headers[] = $request->to_header();
		$headers[] = "Content-type: application/xml";
		$response = run_curl($url, $method, $headers, $content);
	}
	else {
		// make GET request
		$response = run_curl($request, $method);
	}
	
	return $response;

}

?>