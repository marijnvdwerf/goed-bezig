<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';


R::setup('mysql:host=mysql.dd;dbname=' . $db_name, $db_user, $db_pass);

$post = R::dispense('post');
$post->text = 'Hello World';

$id = R::store($post);       //Create or Update
$post = R::load('post',$id); //Retrieve
//R::trash($post);             //Delete

$app = new \Slim\Slim();

$app->get('/', function() use($app) {
    $app->render('index.php');
});

$app->run();
