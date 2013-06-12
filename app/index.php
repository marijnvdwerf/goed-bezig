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

$app->run();
