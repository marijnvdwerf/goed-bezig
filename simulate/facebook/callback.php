<?php 
    //change this
   include 'config.php';
   $redirect_url = "http://goedbezig.marijnvdwerf.nl/simulate/callback.php"; //change this

   $code = $_REQUEST["code"];
   session_start();

   if(empty($code)) 
   {
	header( 'Location: http://goedbezig.marijnvdwerf.nl/simulate/loginwithfb.php' ) ; //change this
	exit(0);
   }
   
   $access_token_details = getAccessTokenDetails($app_id,$app_secret,$redirect_url,$code);
   if($access_token_details == null)
   {
		echo "Unable to get Access Token";
		exit(0);
   }   
   
   if($_SESSION['state'] == null || ($_SESSION['state'] != $_REQUEST['state'])) 
   {
		die("May be CSRF attack");
   }
	 
   $_SESSION['access_token'] = $access_token_details['access_token']; //save token is session 
   
   $user = getUserDetails($access_token_details['access_token']);
   $checkins = getCheckIns($access_token_details['access_token']);
   
   if($user)
   {
		print_r ($user);
		echo "Facebook OAuth is OK<br>";
		echo "<h3>User Details</h3><br>";
		echo "<b>ID: </b>".$user->id."<br>";
		echo "<b>Name: </b>".$user->name."<br>";
		echo "<b>First Name: </b>".$user->first_name."<br>";
		echo "<b>Last Name: </b>".$user->last_name."<br>";
		echo "<b>Username: </b>".$user->username."<br>";
		echo "<b>Profile Link: </b>".$user->link."<br>";
		echo "<b>Email: </b>".$user->email."<br>";
		
   }
   
   if($checkins)
   {
	   echo "<h3>Checkins werkt</h3>";
	   print_r ($checkins);
	   
   }
   else
   {
	   echo"<h3>Helaas</h3>";
   }
	
	
function getAccessTokenDetails($app_id,$app_secret,$redirect_url,$code)
{

	$token_url = "https://graph.facebook.com/oauth/access_token?"
	  . "client_id=" . $app_id . "&redirect_uri=" . urlencode($redirect_url)
	  . "&client_secret=" . $app_secret . "&code=" . $code;

	$response = file_get_contents($token_url);
	$params = null;
	parse_str($response, $params);
	
	return $params;

}

function getUserDetails($access_token)
{
	$graph_url = "https://graph.facebook.com/me?access_token=". $access_token;
	$user = json_decode(file_get_contents($graph_url));
	if($user != null && isset($user->name))
	return $user;
	
	return null;
}

function getCheckIns($access_token)
{
	$graph_url = "https://graph.facebook.com/me/checkins?access_token=". $access_token;
	$checkins = json_decode(file_get_contents($graph_url));
	if($checkins != null)
	return $checkins;
	
	return null;
	}
	


 ?>