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

// Log to HipChat
$hipChatHandler = new Monolog\Handler\HipChatHandler('4b8954da631985d35b6c0e46a08bc4', 'Log', 'Monolog', true, \Monolog\Logger::DEBUG);
$logger->pushHandler($hipChatHandler);

// Log to ChromeLogger
$chromePhpHandler = new Monolog\Handler\ChromePHPHandler();
$logger->pushHandler($chromePhpHandler);


$app = new Application($logger);


$slim = new \Slim\Slim([
    'debug' => true,
    'log.level' => \Slim\Log::DEBUG,
    'log.writer' => new \Flynsarmy\SlimMonolog\Log\MonologWriter([
        'handlers' => [
            $mailHandler,
            $hipChatHandler,
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
    if ($token === null) {
        $response->status(400);
        $response->body(json_encode([
            'error' => 'Missing token parameter'
        ], JSON_PRETTY_PRINT));
        return;
    }

    $user = $app->getUserForFoursquareToken($token);

    if ($user === false) {
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

        'achievements' => []
    ];

    $achievements = $app->getAchievements();
    foreach ($achievements as $achievement) {
        $userAchievement = $app->getUserAchievement($achievement->id, $user->id);
        $a = [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'icon' => $achievement->icon,
            'description' => $achievement->description,
            'completed' => ($userAchievement->getProgress() == 1),
            'progress' => $userAchievement->getProgress(),
            'stamps_required' => $achievement->getStampsRequired(),
            'stamps' => [],
            'goodie' => null
        ];

        foreach ($userAchievement->getStamps() as $stamp) {
            $a['stamps'][] = [
                'timestamp' => $stamp->datetime,
                'type' => $stamp->venuetype->type,
                'new' => false,
            ];
        }
        if ($achievement->goodie !== null) {
            $a['goodie'] = [
                'id' => $achievement->goodie->id,
                'name' => $achievement->goodie->name,
                'icon' => $achievement->goodie->icon,
                'mystery' => (boolean)$achievement->goodie->mystery,
                'claimed' => false
            ];
        }

        $body['achievements'][] = $a;
    }

    $response->body(json_encode($body, JSON_PRETTY_PRINT));
});

$slim->map('/db/reset', function () use ($slim, $app) {
    if ($slim->request()->isPost()) {
        $achievements = file_get_contents('data/achievements.json');
        $achievements = json_decode($achievements);
        $app->loadTestData();
        $app->emptyDatabase();

        $venueTypes = [];
        foreach ($achievements as $achievementData) {
            $achievement = R::dispense('achievement');
            $achievement->name = $achievementData->name;
            $achievement->nickname = $achievementData->nickname;
            $achievement->mystery = false;
            $achievement->description = $achievementData->description;
            if ($achievementData->goodie !== null) {
                $goodie = R::dispense('goodie');
                $goodie->name = $achievementData->goodie->name;
                $goodie->description = $achievementData->goodie->description;
                $goodie->icon = $achievementData->goodie->icon;
                $goodie->mystery = $achievementData->goodie->mystery;
                $achievement->goodie = $goodie;
            }
            $achievement->icon = $achievementData->icon;
            foreach ($achievementData->requirements as $requirementData) {
                $requirement = R::dispense('requirement');
                $requirement->numberRequired = $requirementData->required;
                foreach ($requirementData->types as $type) {
                    if (!isset($venueTypes[$type])) {
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
