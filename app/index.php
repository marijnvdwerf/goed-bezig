<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';


R::setup('mysql:host=mysql.dd;dbname=' . $db_name, $db_user, $db_pass);

$app = new \Slim\Slim();

$app->get('/', function() use($app) {
    $app->render('index.php');
});

$app->get('/achievements', function() use($app) {
    $app->render('achievements.php', [
        'achievements' => R::findAll('achievement')
    ]);
});

$app->get('/db', function() use($app) {
    $achievement = R::dispense('achievement');
    $achievement->title = 'Hello World';
    $achievement->progress = 0.7;
    $achievement->description = 'Hello World';
    $achievement->iconId = 'Hello World';
    
    
    R::store($achievement);       //Create or Update
});

$app->run();
