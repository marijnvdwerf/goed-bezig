<?php
session_start();
$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection

include 'config.php';
$redirect_url = "http://goedbezig.marijnvdwerf.nl/simulate/callback.php"; //change this

$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
       . $app_id . "&redirect_uri=" . urlencode($redirect_url) . "&state="
       . $_SESSION['state'] . "&scope=user_birthday,email,user_checkins";

?>
<html>
<body>
<h1>GoedBezig</h1>

Klik op die knop
<a href="<?php echo $dialog_url;?>"><img src="login-fb2.jpg"></a>
</html>