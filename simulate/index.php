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

$curl = new Curl();
$curl->follow_redirects = false;
$target_url = 'http://requestb.in/ndf5oynd';
    
if(isset($_GET['fbuser']) && isset($_GET['location'])){
    // Facebook

    $time_stamp = explode(" ", microtime());
    $seconds = (int)$time_stamp[1];

    $values = array("object" => "user", "entry" => array(array("uid" => "100005427698197", "id" => "100005427698197", "time" => $seconds, "changed_fields" => array("checkins"))));

    $response = $curl->post($target_url, $values);


}
if(isset($_GET['user']) && isset($_GET['checkin'])){
    require 'foursquare/config.php';
    // Foursquare

    $place_to_be = 'foursquare/user/' . $_GET['user'];

    $json_user_content = file_get_contents($place_to_be);
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
</head>
<body>
    <form id="selectUserCheckInFacebook">
        <fieldset>
            <legend>Facebook</legend>
            <select name="fbuser">
                <option value="34647475754585">Jeroen</option>
                <option value="ffuser">Sjaak</option>
            </select>
            <select name="location">
                <option value="location1">Eindhoven</option>
                <option value="location2">Veghel</option>
            </select>
            <input type="submit" />
        </fieldset>
    </form>

    <form id="selectUserCheckInFoursquare">
        <fieldset>
            <legend>Foursquare</legend>
            <?= getSelectBox('user'); ?>
            <?= getSelectBox('checkin'); ?>
            <input type="submit" />
        </fieldset>
    </form>
    
    <?php if(isset($response)): ?>
        <p>Verstuurd naar <a href="<?= htmlentities($target_url); ?>?inspect"><?= htmlentities($target_url); ?></a>.</p>
    <pre><?= $response; ?></pre>
    <?php endif; ?>
</body>
</html>