<?php

require 'vendor/autoload.php';

$curl = new Curl();
$curl->follow_redirects = false;





    
        if(isset($_GET['fbuser']) && isset($_GET['location'])){
            //fb call
            echo "fb arrived";

            $time_stamp = explode(" ", microtime());
            $seconds = (int)$time_stamp[1];

            //echo $seconds;
            $values = array("object" => "user", "entry" => array(array("uid" => "100005427698197", "id" => "100005427698197", "time" => $seconds, "changed_fields" => array("checkins"))));
            $json_string = json_encode($values);
            
            $curl->post('http://requestb.in/ndf5oynd', $values);
            echo "<br>Verstuurd naar <a href=\"http://requestb.in/ndf5oynd?inspect\">site</a>";


        }
        if(isset($_GET['user']) && isset($_GET['checkin'])){
            //4sq call
            echo "4sq arrived";
            require 'foursquare/config.php';

            $place_to_be = 'foursquare/user/' . $_GET['user'];
            echo $place_to_be;

            $json_user_content = file_get_contents($place_to_be);
            $json_checkin_content = file_get_contents('foursquare/checkin/' . $_GET['checkin']);

            $json_content = [
                'user' => $json_user_content,
                'checkin' => $json_checkin_content,
                'secret' => $push_secret
            ]; 

            $arrived = true;

            $curl->post('http://requestb.in/ndf5oynd', $json_content);
            echo "<br>Verstuurd naar <a href=\"http://requestb.in/ndf5oynd?inspect\">site</a>";
        }
?>
<html>
<head>
<title>GoedBezig callto</title>
</head>
<body>
<?php

    

    


    file_put_contents('testData.json', $json_string);
    
    

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

?>



<form action="callto.php" id="selectUserCheckInFacebook">
    <select name="fbuser">
        <option value="34647475754585">Jeroen</option>
        <option value="ffuser">Sjaak</option>
    </select>
    <select name="location">
        <option value="location1">Eindhoven</option>
        <option value="location2">Veghel</option>
    </select>
    <input type="submit">Submit facebook</input>
</form>

<form action="callto.php" id="selectUserCheckInFoursquare">
    <?= getSelectBox('user'); ?>
    <?= getSelectBox('checkin'); ?>
    <input type="submit">Submit Foursquare</input>
</form>






</body>
</html>