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

    echo 'Database nuked -> <a href="create-example">Create new example</a>';
        
});

$app->get('/db/create-example', function() use($app){
    
    $user = R::dispense('user');
    $user ->name = 'John Doe';
    $user ->email = "goedbezig@marijnvdwerf.nl";
    $user ->sex = "Male";
    $user ->age = 45;
    $user ->foursquareId = "00023043287276367263";
    $user ->foursquareToken = "RJJEHFDSHJFHJKHF34938598KJHFKJSHFJKHFJHSF9843UIHFJHSFJSIFH04823DHJ";
    $user ->facebookId = "01005002392872387482";
    $user ->facebookToken = "AJFNH764JHFJHFJ583HFHRJH987398479FHISJKJFGJ3476HDGJHGFJ98724JHGFJG";
    $user ->setMeta('cast.foursquareId', 'string');
    $user ->setMeta('cast.facebookId', 'string');
    R::store($user);

        $address = R::dispense('address');
        //$user ->ownAddress = [$adress];
        $address ->user = $user;
        $address ->address = "Hoofdstraat 5";
        $address ->postalCode = "1234AB";
        $address ->town = "Amsterdam";
        R::store($address);


    $achievement = R::dispense('achievement');
    $achievement ->name = "Sporter";
    $achievement ->description = "Je bent sportief bezig";
    $achievement ->icon = "sporter";
    $achievement ->mystery = false;
    R::store($achievement);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venueType = "College Gym; Gym";
        $requirement ->numberRequired = 2;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement ->user = $user;
        $userAchievement ->achievement = $achievement;
        $userAchievement ->progress = 0.80;
        $userAchievement ->goodieClaimed = null;
        R::store($userAchievement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venueType = "Pool / Lake";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venueType = "Pool / Lake;Waterpark;Gym Pool";
        $requirement ->numberRequired = 10;
        R::store($requirement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venueType = "Gym";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);

    $achievement = R::dispense('achievement');
    $achievement ->name = "Waterrat";
    $achievement ->description = "Je bent graag in het water";
    $achievement ->icon = "waterrat";
    $achievement ->mystery = false;
    R::store($achievement);

        $goodie = R::dispense('goodie');
        $goodie ->achievement = $achievement;
        $goodie ->name = "Bidon";
        $goodie ->description = "Een lekkere drinkfles";
        $goodie ->icon = "bidon";
        $goodie ->mystery = false;
        R::store($goodie);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venueType = "College Gym; Gym";
        $requirement ->numberRequired = 10;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement ->user = $user;
        $userAchievement ->achievement = $achievement;
        $userAchievement ->progress = 0.80;
        $userAchievement ->goodieClaimed = null;
        R::store($userAchievement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venueType = "Pool / Lake";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venueType = "Pool / Lake;Waterpark;Gym Pool";
        $requirement ->numberRequired = 3;
        R::store($requirement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venueType = "Gym";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);


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
            'achievement_id' => 1,
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

    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode(['cards' => $cards]));
});

$app->run();