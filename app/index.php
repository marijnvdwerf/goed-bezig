<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';
require 'includes/createdatabase.php';



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

$app->map('/db/reset', function() use($app) {
    if($app->request()->isPost()) {
        R::nuke();
        echo 'Database nuked -> <a href="create-example">Create new example</a>';
        return;
    }

     $app->render('db_reset.php');
})->via('GET', 'POST');

$app->get('/db/create-example', function() use ($app){
    //function in 'includes/createdatabase.php'
    create_db();
});

$app->post('/checkin/foursquare', function() use($app){
    $user = R::find('user', 1);
    
    $checkin = $app->request()->params('checkin');
    $checkin = json_decode($checkin);
    
    $categories = [];
    foreach($checkin->venue->categories as $category){
        $categories[] = $category->name;
        $categories = array_merge($category->parents,$categories);
    }
    
    /*R::debug(true);*/

    //GET venuetype ID
    $relatedVenueTypes = R::findAll('venuetype', ' WHERE type IN ('.R::genSlots($categories).') ', $categories);

    //GET requirement and achievements owning the venuetype 
    R::preload($relatedVenueTypes, '*.requirement, *.achievement');
    
    //CREATE array with achievements per venue;
    $venueAchievements = [];
    foreach($relatedVenueTypes as $venueType) {
        //var_dump($venueType->type); //name of the venuetype
        foreach($venueType->sharedRequirement as $requirement) {
            //name of the achievement
            //var_dump($requirement->achievement->id);
            $venueAchievements[] = $requirement->achievement;
            //var_dump($requirement->achievement->name);
        }
    }
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