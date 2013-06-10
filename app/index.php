<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';
require 'includes/Application.php';


$app = new Application();
$slim = new \Slim\Slim();

$slim->get('/', function() use($slim) {
    $slim->render('index.php');
});

$slim->get('/achievements', function() use($slim) {
    $slim->render('achievements.php', [
        'achievements' => R::findAll('achievement')
    ]);
});

$slim->map('/db/reset', function() use($slim, $app) {
    if($slim->request()->isPost()) {
        $app->loadTestData();
        echo 'Database nuked and filled with sample data';
        return;
    }

     $slim->render('db_reset.php');
})->via('GET', 'POST');

$slim->post('/checkin/foursquare', function() use($slim, $app){
    $user = R::find('user', 1);
    
    $checkin = $slim->request()->params('checkin');
    $checkin = json_decode($checkin);
    
    $categories = [];
    foreach($checkin->venue->categories as $category){
        $categories[] = $category->name;
        $categories = array_merge($category->parents,$categories);
    }
    
    /*R::debug(true);*/

    $venueAchievements = $app->getAchievementsForCategories($categories);
    //var_dump($venueAchievements);

    //CREATE array with current started achievements of the user
    $user = R::findOne('user', 'foursquare_id = ? ',[$checkin->user->id]);
    

    //DEBUG:
    //$userAchievements = R::find('userachievement', 'user_id = ?', ['1']);
    
    foreach($venueAchievements as $venueAchievement) {
        //var_dump($venueAchievement->id);
        $userAchievement = R::findOne('userachievement',
                                      'achievement_id = :venueAchievement AND user_id = :userID',
                                      array(
                                            ':venueAchievement'=>$venueAchievement->id,
                                            ':userID'=>$user->id
                                            //':userID'=>'1' DEBUG!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                           )
                                     );
        
        





        var_dump($userAchievement->progress);



    }




/*foreach($userAchievements as $userAchievement) {
    
    if($userAchievement->progress < 1){
        print_r("completed");
    }
    else {

    }*/


    //var_dump($userAchievement);


//}




/*WHERE user is jeroen
{ if achievement in user achievement}
dan ga naar stamps
{ else }
maak nieuwe aan*/


    






});

$slim->get('/api/stamps', function() use($slim) {
    $count = rand(0, 1);
    $stamps = [];

    for($i = 0; $i < $count; $i++) {
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

$slim->get('/api/cards', function() use($slim) {
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