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

$app->get('/db/reset', function() use($app) {
    R::nuke();
        
});

$app->get('/db/create-example', function() use($app){
    
    $user = R::dispense('user');
    $user ->name = 'John Doe';
    $user ->email = "goedbezig@marijnvdwerf.nl";
    $user ->sex = "Male";
    $user ->age = 45;
    $user ->foursquare_id = 00023043287276367263;
    $user ->facebook_id = 100002392872387482;

    R::store($user);

    $address = R::dispense('address');
    //$user ->ownAddress = [$adress];
    $address ->user = $user;
    $address ->address = "Hoofdstraat 5";

    R::store($address)


});

$app->post('/checkin/foursquare', function() use($app){
    $user = R::find('user', 1);
    
    $checkin = $app->request()->params('checkin');
    $checkin = json_decode($checkin);
    
    $categories = [];
    foreach($checkin->venue->categories as $category){
        $categories[] = $category->name;
        $categories = array_merge($categories, $category->parents);
    }
    
    $relatedAchievements = R::find('achievement',
    ' id in (SELECT achievement_id FROM requirement WHERE venue_category IN ('.R::genSlots($categories).'))',$categories);
    
    var_dump($relatedAchievements);
});

$app->get('/api/stamps', function() use($app) {
    $count = rand(0, 1);
    $stamps = [];

    for($i = 0; $i < $count; $i++) {
        $stamps[] = [
            'datetime' => date('c'),
            'type' => 'spa'
        ];
    }

    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode(['stamps' => $stamps]));
});

$app->get('/api/cards', function() use($app) {
    $cards = [];

    $cards[] = [
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
        'name' => 'Boswandeling',
        'description' => 'Lorem ipsum dolor sit amet',
        'icon' => 'boswandeling',
        'mystery' => false,
        'progress' => 0,
        'goodie' => null,
        'stamps' => [
        ]
    ];

    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode(['cards' => $cards]));
});

$app->run();