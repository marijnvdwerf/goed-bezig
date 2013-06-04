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
    
     
    /* OUD:  $ors = [];
     foreach($categories as $category) {
        $ors[] = 'venue_category LIKE \'%' . $category . '%\'';
    } 
    var_dump($ors);
        $relatedAchievements = R::find('achievement',
    ' id in (SELECT achievement_id FROM requirement WHERE venue_category ' . implode(' OR ', $ors) . ')');
    OUD */
    
    /*var_dump($checkin);
    
    var_dump($relatedAchievements);
    R::debug(true);*/

    
    $relatedVenueTypes = R::findAll('venuetype', ' WHERE type IN ('.R::genSlots($categories).') ', $categories);

    //var_dump($relatedVenueTypes);
    
    R::preload($relatedVenueTypes, '*.requirement, *.achievement');
    //$relatedRequirements = R::findAll('requirement_venuetype', 'requirement_id = 2');

    foreach($relatedVenueTypes as $venueType) {
        //var_dump($venueType->type);

        //var_dump($categories);
        foreach($venueType->sharedRequirement as $requirement) {
            //var_dump($requirement);
            /*var_dump($requirement->id);*/
            var_dump($requirement->achievement->name);
        }
    }
    






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