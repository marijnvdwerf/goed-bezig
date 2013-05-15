<?php
include 'config.php';
require '../vendor/autoload.php';

$facebook = new Facebook(array(
    'appId'  => $app_id,
    'secret' => $app_secret,
));
 
$user = $facebook->getUser();

if ($user) {
  $token = $facebook->getAccessToken(); //get access token
  try {
            $me = $facebook->api('/me');
            if($me)
            {
                $_SESSION['fbID'] = $me['id'];
                $fbID = $me['id'];

                file_put_contents('users/'. $fbID . '.json', $token); 
                header( 'Location: http://goedbezig.marijnvdwerf.nl/simulate/facebook/getlatest.php');
                exit(0);
            }
        } catch (FacebookApiException $e) {
          error_log($e);   
        }

//echo "<hr>" . $me['id'];

/*    try {
            $me = $facebook->api('/100005427698197/checkins');
            if($me)
            {
                print_r($me);               
            }
        } catch (FacebookApiException $e) {
          error_log($e);   
        }*/

  
}
else {
$args['scope'] = 'email'; // scope parameter, separate multiple scopes by comma
$loginUrl = $facebook->getLoginUrl($args); //generate login url
echo $loginUrl;
}
?>