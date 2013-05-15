<?php
include 'config.php';
require '../vendor/autoload.php';

$facebook = new Facebook(array(
    'appId'  => $app_id,
    'secret' => $app_secret,
));

if(isset($_GET['users'])){
    $json_accesstoken = file_get_contents('users/' . $_GET['users']);
    echo $json_accesstoken . '<hr>';
}

$user = $facebook->getUser();

$facebook->setAccessToken($json_accesstoken);

if ($user) {
  $token = $facebook->getAccessToken(); //get access token
  try {
            $me = $facebook->api('/me/checkins');
            if($me)
            {
                
                echo '<pre>' . json_encode($me, JSON_PRETTY_PRINT). '</pre><hr>';
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





function getFiles($type){
        $files = scandir($type);
        $output = [];
        foreach($files as $file) {
            if($file === '.' || $file === '..' || $file === 'empty') {
                continue;
            }
            $output[] = $file;
        }

        return $output;
    }

    function getSelectBox($type) {
        $options = getFiles($type);
        foreach($options as &$option) {
            $option = '<option>' . $option . '</option>';
        };

        return '<select name="' . $type . '">' . implode("\r\n", $options) . '</select>';
    }


    ?>


    <html>
    <head>
        <title>Get latest checkin from random user</title>
    </head>
    <body>
    <form action="getlatest.php" id="randomFormName">
    <?= getSelectBox('users'); ?>
    <input type="submit">
</form>
    </body>
    </html>