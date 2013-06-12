<?php
include 'config.php';
require '../vendor/autoload.php';

$facebook = new Facebook(array(
    'appId'  => $app_id,
    'secret' => $app_secret,
));

if(isset($_GET['users'])){
    $json_accesstoken = file_get_contents('users/' . $_GET['users']);
    //echo $json_accesstoken . '<hr>';
}

$user = $facebook->getUser();

$facebook->setAccessToken($json_accesstoken);


if ($user) {
  $token = $facebook->getAccessToken(); //get access token
  try {
            $collect = $facebook->api('/me/checkins');
            if($collect)
            {
                
                //ALLE CHECKINS
                //echo '<pre>' . json_encode($collect, JSON_PRETTY_PRINT). '</pre><hr>';
                ChromePhp::log($collect);

                
                            

                foreach($collect['data'] as $checkin){
                    

                    if(!checkIfUserAlreadyExist($checkin['from']['name'])) {
                        createJsonFile('checkin-users', $checkin['from']['name'], $checkin['from']);
                        //ChromePhp::log("user json created");
                    } else {//ChromePhp::log("checked");
                }

                    if(!checkIfLocationAlreadyExist($checkin['place']['name'])) {
                        createJsonFile('checkin-locations', $checkin['place']['name'], $checkin['place']);
                        //ChromePhp::log("location json created");
                    } else {//ChromePhp::log("checked too");
                }
                   
                    //if hij al bestaat --> niets, else make file and write

                    //get locatie
                    //if hij al bestaat --> niets, else make file and write


                    //ChromePhp::log($checkin['from']['name'] . " @ " . $checkin['place']['name']);
                    
                    //echo $place . '/n';
                }







            }
        } catch (FacebookApiException $e) {
          error_log($e);   
        }

  
}
else {
$args['scope'] = 'email'; // scope parameter, separate multiple scopes by comma
$loginUrl = $facebook->getLoginUrl($args); //generate login url
//echo $loginUrl;
}


function checkIfUserAlreadyExist($name){

    $all_users = getFiles('checkin-users');
    return in_array($name . '.json', $all_users);
};
function checkIfLocationAlreadyExist($location){

    $all_locations = getFiles('checkin-locations');
    return in_array($location . '.json', $all_locations);
};

function createJsonFile($folder, $name, $data){
    file_put_contents($folder .'/'. $name . '.json', json_encode($data));
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