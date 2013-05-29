<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';


R::setup('mysql:host=mysql.dd;dbname=' . $db_name, $db_user, $db_pass);

$app = new \Slim\Slim();

$app->get('/', function() use($app) {
    $app->render('index.php');
});

$app->post('/api/login/foursquare', function () use ($app) {
    $request = $app->request();

    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Content-Type'] = 'application/json';

    $token = $request->params('token');
    if($token === null) {
        $response->status(400);
        $response->body(json_encode([
            'error' => 'Missing token parameter'
        ], JSON_PRETTY_PRINT));
        return;
    }

    $body = [
        'settings' => [
            'user' => [
                'id' => '663366',
                'firstName' => 'Jeroen',
                'surName' => 'van der Sanden',
                'address' => 'Straat 1',
                'town' => 'Plaatsnaam',
                'mail' => 'jeroen@goedbezig.nl',
                'phone' => '0612345678',
            ],
            'notifications' => [
                'types' => [
                    'get-goodie',
                    'get-achievement'
                ],
                'availableMethods' => [
                    'facebook', 'email', 'sms'
                ],
                'selectedMethod' => 'sms'
            ]
        ],

        'achievements' => [
            [
                'name' => 'Completed and collected',
                'description' => 'Lorem ipsum',
                'completed' => true,
                'progress' => 1,
                'stamps' => [
                    [
                        'timestamp' => '2013-06-01T14:30',
                        'type' => 'pool'
                    ],
                    [
                        'timestamp' => '2013-06-02T14:30',
                        'type' => 'gym'
                    ],
                    [
                        'timestamp' => '2013-01-03T14:30',
                        'type' => 'school'
                    ],
                    [
                        'timestamp' => '2013-01-04T14:30',
                        'type' => 'library'
                    ],
                    [
                        'timestamp' => '2013-01-05T14:30',
                        'type' => 'train'
                    ]
                ],
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Collected goodie',
                    'claimed' => true
                ]
            ],

            [
                'name' => 'Completed and unclaimed',
                'description' => 'Lorem ipsum',
                'completed' => true,
                'progress' => 1,
                'stamps' => [
                    [
                        'timestamp' => '2013-06-01T14:30',
                        'type' => 'pool',
                        'new' => false,
                    ],
                    [
                        'timestamp' => '2013-06-02T14:30',
                        'type' => 'gym',
                        'new' => false
                    ],
                    [
                        'timestamp' => '2013-01-03T14:30',
                        'type' => 'school',
                        'new' => false
                    ],
                    [
                        'timestamp' => '2013-01-04T14:30',
                        'type' => 'library',
                        'new' => false
                    ],
                    [
                        'timestamp' => '2013-01-05T14:30',
                        'type' => 'train',
                        'new' => false
                    ]
                ],
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Unclaimed goodie',
                    'claimed' => false
                ]
            ],

            [
                'name' => 'Some progress (1/5)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 5,
                'goodie' => [],
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'train'
                    ]
                ]
            ],

            [
                'name' => 'Some progress (1/10)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 10,
                'goodie' => [],
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'gym',
                        'new' => false
                    ]
                ]
            ],


            [
                'name' => 'NEW progress (1/10)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 10,
                'goodie' => [],
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'pool',
                        'new' => true
                    ]
                ]
            ],

            [
                'name' => 'No progress (5)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 5,
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Visible goodie',
                    'claimed' => false
                ],
                'stamps' => [
                ]
            ],

            [
                'name' => 'No progress (10)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 10,
                'stamps' => []
            ],

            [
                'name' => 'No progress (15)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 15,
                'stamps' => []
            ],

            [
                'name' => 'No progress (20)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'stamps_required' => 20,
                'stamps' => []
            ]
        ]
    ];

    $response->body(json_encode($body, JSON_PRETTY_PRINT));
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
    $user ->facebookId = "01005002392872387482";
    $user ->setMeta('cast.foursquareId', 'string');
    $user ->setMeta('cast.facebookId', 'string');
    R::store($user);

        $address = R::dispense('address');
        //$user ->ownAddress = [$adress];
        $address ->user = $user;
        $address ->address = "Hoofdstraat 5";
        $address ->postal_code = "1234AB";
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
        $requirement ->venue_type = "College Gym; Gym";
        $requirement ->number_required = 2;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement ->user = $user;
        $userAchievement ->achievement = $achievement;
        $userAchievement ->progress = 0.80;
        $userAchievement ->goodie_claimed = null;
        R::store($userAchievement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venue_type = "Pool / Lake";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venue_type = "Pool / Lake;Waterpark;Gym Pool";
        $requirement ->number_required = 10;
        R::store($requirement);

        

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venue_type = "Gym";
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
        $requirement ->venue_type = "College Gym; Gym";
        $requirement ->number_required = 10;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement ->user = $user;
        $userAchievement ->achievement = $achievement;
        $userAchievement ->progress = 0.80;
        $userAchievement ->goodie_claimed = null;
        R::store($userAchievement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venue_type = "Pool / Lake";
            $stamp ->requirement = $requirement;
            $stamp ->datetime = new DateTime();
            R::store($stamp);

        $requirement = R::dispense('requirement');
        $requirement ->achievement = $achievement;
        $requirement ->venue_type = "Pool / Lake;Waterpark;Gym Pool";
        $requirement ->number_required = 3;
        R::store($requirement);

            $stamp = R::dispense('stamp');
            $stamp ->userachievement = $userAchievement;
            $stamp ->venue_type = "Gym";
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