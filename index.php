<?php

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/vendor/rb.php';
require 'includes/Application.php';


$logger = new Monolog\Logger('Logger', [], [
    // Adds the line/file/class/method from which the log call originated.
    new Monolog\Processor\IntrospectionProcessor(),

    // Adds the current request URI, request method and client IP to a log record.
    new Monolog\Processor\WebProcessor()
]);

// Log warnings (and up) to log+goedbezig@marijnvdwerf.nl
$mailHandler = new MarijnvdWerf\Monolog\Handler\NativeHtmlMailerHandler('log+goedbezig@marijnvdwerf.nl', 'LOG', 'goedbezig@marijnvdwerf.nl', Monolog\Logger::WARNING);
$mailHandler->setFormatter(new MarijnvdWerf\Monolog\Formatter\HtmlFormatter());
$logger->pushHandler($mailHandler);

// Log to ChromeLogger
$chromePhpHandler = new Monolog\Handler\ChromePHPHandler();
$logger->pushHandler($chromePhpHandler);


$app = new Application($logger);


$slim = new \Slim\Slim([
    'debug' => true,
    'log.level' => \Slim\Log::WARN,
    'log.writer' => new \Flynsarmy\SlimMonolog\Log\MonologWriter([
        'handlers' => [
            $mailHandler,
            $chromePhpHandler
        ]
    ])
]);


$slim->get('/', function () use ($slim, $foursquare_client_id) {
    $slim->render('index.php', [
        'foursquare_client_id' => $foursquare_client_id
    ]);
});

$slim->post('/api/login/foursquare', function () use ($slim, $app) {
    $request = $slim->request();

    $response = $slim->response();
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

    $user = $app->getUserForFoursquareToken($token);

    if($user === false) {
        $response->status(400);
        $response->body(json_encode([
            'error' => 'Invalid token'
        ], JSON_PRETTY_PRINT));
        return;
    }

    $body = [
        'settings' => [
            'user' => [
                'id' => $user->id,
                'firstName' => $user->name,
                'lastName' => $user->surname,
                'address' => $user->street,
                'town' => $user->town,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'notifications' => [
                'types' => [
                    'get-goodie',
                    'get-achievement'
                ],
                'availableMethods' => $user->getNotificationMediumOptions(),
                'selectedMethod' => $user->getNotificationMedium()
            ]
        ],

        'achievements' => [
            [
                'id' => 1,
                'name' => 'Completed and collected',
                'description' => 'Lorem ipsum',
                'completed' => true,
                'progress' => 1.0,
                'stamps_required' => 4,
                'stamps' => [
                    [
                        'timestamp' => '2013-06-01T14:30',
                        'type' => 'pool',
                        'new' => false
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
                    ]
                ],
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Collected goodie',
                    'claimed' => true
                ]
            ],

            [
                'id' => 2,
                'name' => 'Completed and unclaimed',
                'description' => 'Lorem ipsum',
                'completed' => true,
                'progress' => 1.0,
                'stamps_required' => 4,
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
                    ]
                ],
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Unclaimed goodie',
                    'claimed' => false
                ]
            ],

            [
                'id' => 3,
                'name' => 'Some progress (1/4)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => (1 / 4),
                'stamps_required' => 4,
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'train'
                    ]
                ],
                'goodie' => null
            ],

            [
                'id' => 4,
                'name' => 'Some progress (1/8)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => (1 / 8),
                'stamps_required' => 8,
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'gym',
                        'new' => false
                    ]
                ],
                'goodie' => null
            ],


            [
                'id' => 5,
                'name' => 'NEW progress (1/8)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => (1 / 8),
                'stamps_required' => 8,
                'stamps' => [
                    [
                        'timestamp' => '2013-01-01T14:30',
                        'type' => 'pool',
                        'new' => true
                    ]
                ],
                'goodie' => null
            ],

            [
                'id' => 6,
                'name' => 'No progress (4)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => 0.0,
                'stamps_required' => 4,
                'stamps' => [
                ],
                'goodie' => [
                    'mystery' => false,
                    'title' => 'Visible goodie',
                    'claimed' => false
                ]
            ],

            [
                'id' => 7,
                'name' => 'No progress (8)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => 0.0,
                'stamps_required' => 8,
                'stamps' => [],
                'goodie' => null
            ],

            [
                'id' => 8,
                'name' => 'No progress (12)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => 0.0,
                'stamps_required' => 12,
                'stamps' => [],
                'goodie' => null
            ],

            [
                'id' => 9,
                'name' => 'No progress (16)',
                'description' => 'Lorem ipsum',
                'completed' => false,
                'progress' => 0.0,
                'stamps_required' => 16,
                'stamps' => [],
                'goodie' => null
            ]
        ]
    ];

    $response->body(json_encode($body, JSON_PRETTY_PRINT));
});

$slim->map('/db/reset', function () use ($slim, $app) {
    if($slim->request()->isPost()) {
        $achievements = file_get_contents('data/achievements.json');
        $achievements = json_decode($achievements);
        $app->loadTestData();
        $app->emptyDatabase();

        $venueTypes = [];
        foreach($achievements as $achievementData) {
            $achievement = R::dispense('achievement');
            $achievement->name = $achievementData->name;
            $achievement->mystery = false;
            $achievement->description = $achievementData->description;
            $achievement->icon = $achievementData->description;
            foreach($achievementData->requirements as $requirementData) {
                $requirement = R::dispense('requirement');
                $requirement->numberRequired = $requirementData->required;
                foreach($requirementData->types as $type) {
                    if(!isset($venueTypes[$type])) {
                        $venueTypes[$type] = R::dispense('venuetype');
                        $venueTypes[$type]->type = $type;
                    }
                    $requirement->sharedVenuetype[] = $venueTypes[$type];
                }
                $achievement->ownRequirement[] = $requirement;
            }
            R::store($achievement);
        }
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

    $app->addFourSquareCheckin($fourSquareUser->id, $checkin);
});

$slim->run();
