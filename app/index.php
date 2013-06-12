<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';
require 'includes/Application.php';


$app = new Application();
$slim = new \Slim\Slim();

$slim->get('/', function () use ($slim) {
    $slim->render('index.php');
});

$slim->get('/achievements', function () use ($slim) {
    $slim->render('achievements.php', [
        'achievements' => R::findAll('achievement')
    ]);
});

$slim->map('/db/reset', function () use ($slim, $app) {
    if ($slim->request()->isPost()) {
        $app->loadTestData();
        echo 'Database nuked and filled with sample data';
        return;
    }

    $slim->render('db_reset.php');
})->via('GET', 'POST');

$slim->post('/checkin/foursquare', function () use ($slim, $app) {
    $checkin = $slim->request()->params('checkin');
    $checkin = json_decode($checkin);

    $fourSquareUser = $slim->request()->params('user');
    $fourSquareUser = json_decode($fourSquareUser);

    $user = $app->getUserForFoursquareId($fourSquareUser->id);

    $categories = [];
    foreach ($checkin->venue->categories as $category) {
        $categories[] = $category->name;
        $categories = array_merge($category->parents, $categories);
    }

    $venueAchievements = $app->getAchievementsForCategories($categories);

    foreach ($venueAchievements as $achievement) {
        $userAchievement = $app->getRelevantUserAchievements($achievement->id, [$user->id]);
    }
});

$slim->get('/api/stamps', function () use ($slim) {
    $count = rand(0, 1);
    $stamps = [];

    for ($i = 0; $i < $count; $i++) {
        $stamps[] = [
            'achievement_id' => 1,
            'datetime' => date('c'),
            'type' => 'spa'
        ];
    }

    $response = $slim->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode(['stamps' => $stamps]));
});

$slim->get('/api/cards', function () use ($slim) {
    $cards = [];

    $cards[] = [
        'id' => 1,
        'name' => 'Waterrat',
        'description' => 'Lorem ipsum dolor sit amet',
        'icon' => 'waterrat',
        'mystery' => false,
        'progress' => 0.2,
        'goodie' => null,
        'stamps' => [
            [
                'datetime' => date('c', strtotime('2013-05-24 14:35:30')),
                'type' => 'Pool / Lake'
            ]
        ]
    ];

    $cards[] = [
        'id' => 2,
        'name' => 'Boswandeling',
        'description' => 'Lorem ipsum dolor sit amet',
        'icon' => 'boswandeling',
        'mystery' => false,
        'progress' => 0,
        'goodie' => null,
        'stamps' => [
        ]
    ];

    $response = $slim->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode(['cards' => $cards]));
});

$slim->run();