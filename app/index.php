<?php

require 'includes/config.php';
require 'includes/vendor/rb.php';


R::setup('mysql:host=mysql.dd;dbname=' . $db_name, $db_user, $db_pass);

$post = R::dispense('post');
$post->text = 'Hello World';

$id = R::store($post);       //Create or Update
$post = R::load('post',$id); //Retrieve
//R::trash($post);             //Delete
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Goed Bezig</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <h1>Hallo wereld</h1>
    </body>
</html>
