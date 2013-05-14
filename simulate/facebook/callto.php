<html>
<head>
<title>GoedBezig callto</title>
</head>
<body>
<?php

    $time_stamp = explode(" ", microtime());
    $seconds = (int)$time_stamp[1];

    //echo $seconds;
    $values = array("object" => "user", "entry" => array(array("uid" => "100005427698197", "id" => "100005427698197", "time" => $seconds, "changed_fields" => array("checkins"))));
    $json_string = json_encode($values);


    file_put_contents('testData.json', $json_string);
    echo "written to testData.json";
    

?>

<form action="callto.php" id="selectUserCheckIn">
    <select>
        <option value="100005427698197">Jeroen</option>
    </select>
    <select>
        <option value="Eindhoven">Eindhoven</option>
    </select>
    <input type="submit">
</form>






</body>
</html>