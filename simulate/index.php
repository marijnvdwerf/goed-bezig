<?php

require 'vendor/autoload.php';

function getFiles($type){
    $files = scandir('foursquare/' . $type);
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

session_start();

$curl = new Curl();
$curl->follow_redirects = false;

if(isset($_GET['target_url'])) {
    $_SESSION['target_url'] = $_GET['target_url'];
}
if(!isset($_SESSION['target_url'])) {
    $_SESSION['target_url'] = 'http://requestb.in/ndf5oynd';
}
$target_url = $_SESSION['target_url'];
    
if(isset($_GET['facebook'])){
    // Facebook

    $time_stamp = explode(" ", microtime());
    $seconds = (int)$time_stamp[1];

    $values = array("object" => "user", "entry" => array(array("uid" => "100005427698197", "id" => "100005427698197", "time" => $seconds, "changed_fields" => array("checkins"))));

    $response = $curl->post($target_url, $values);
} else if(isset($_GET['foursquare'])){
    require 'foursquare/config.php';
    // Foursquare
    $json_user_content = file_get_contents('foursquare/user/' . $_GET['user']);
    $json_checkin_content = file_get_contents('foursquare/checkin/' . $_GET['checkin']);

    $json_content = [
        'user' => $json_user_content,
        'checkin' => $json_checkin_content,
        'secret' => $push_secret
    ]; 

    $response = $curl->post($target_url, $json_content);
}
?>
<!doctype html>
<html>
<head>
    <title>GoedBezig callto</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <style type="text/css">
        body {
            margin: 40px 0 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <form class="form-inline">
            <fieldset>
                <label>URL: </label>
                <input type="text" name="target_url" value="<?= htmlentities($target_url); ?>" />
            </fieldset>
            <fieldset>
                <legend>Facebook</legend>
                <select name="fbUser">
                    <option value="34647475754585">Jeroen</option>
                    <option value="ffuser">Sjaak</option>
                </select>
                <select name="fbLocation">
                    <option value="location1">Eindhoven</option>
                    <option value="location2">Veghel</option>
                </select>
                <input type="submit" class="btn btn-primary" name="facebook"/>
            </fieldset>

            <fieldset>
                <legend>Foursquare</legend>
                <?= getSelectBox('user'); ?>
                <?= getSelectBox('checkin'); ?>
                <input type="submit" class="btn btn-primary" name="foursquare"/>
            </fieldset>
        </form>
    
        <?php if(isset($response)): ?>
            <fieldset>
                <legend>Request</legend>
                <p>Verstuurd naar <a href="<?= htmlentities($target_url); ?>?inspect"><?= htmlentities($target_url); ?></a>.</p>
                <pre><?= htmlentities($response); ?></pre>
            </fieldset>
        <?php endif; ?>
    </div>
</body>
</html>