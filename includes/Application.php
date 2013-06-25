<?php

include 'TextMessage.php';
include 'EmailMessage.php';
include 'NotificationManager.php';

class Application
{
    /**
     * @var NotificationManager
     */
    public $notificationManager;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @param \Monolog\Logger $logger
     * @param bool $testing
     */
    function __construct($logger, $testing = false)
    {
        global $db_host, $db_name, $db_user, $db_pass;
        if (!$testing) {
            R::setup('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass);
        } else {
            R::setup('sqlite::memory:');
        }

        $this->notificationManager = new NotificationManager();
        $this->logger = $logger;
    }


    function loadTestData()
    {
        R::nuke();

        $user = R::dispense('user');
        $user->name = 'John';
        $user->surname = 'Doe';
        $user->email = "goedbezig@marijnvdwerf.nl";
        $user->sex = "Male";
        $user->age = 45;
        $user->phone = '31612345678';
        $user->foursquareId = "00023043287276367263";
        $user->foursquareToken = "RJJEHFDSHJFHJKHF34938598KJHFKJSHFJKHFJHSF9843UIHFJHSFJSIFH04823DHJ";
        $user->facebookId = "";
        $user->facebookToken = "";
        $user->setMeta('cast.foursquareId', 'string');
        $user->setMeta('cast.facebookId', 'string');
        R::store($user);

        $address = R::dispense('address');
        //$user ->ownAddress = [$adress];
        $address->user = $user;
        $address->address = "Hoofdstraat 5";
        $address->postalCode = "1234AB";
        $address->town = "Amsterdam";
        R::store($address);


        $achievement = R::dispense('achievement');
        $achievement->name = "Sporter";
        $achievement->description = "Je bent sportief bezig";
        $achievement->icon = "sporter";
        $achievement->mystery = false;
        $achievement->earnMessage = 'Laat die spierballen maar zien!';
        R::store($achievement);

        $collegeGym = R::dispense('venuetype');
        $collegeGym->type = "College Gym";
        R::store($collegeGym);

        $gym = R::dispense('venuetype');
        $gym->type = "Gym";
        R::store($gym);

        $requirement = R::dispense('requirement');
        $requirement->achievement = $achievement;
        $requirement->sharedVenueTypes = [$collegeGym, $gym];
        $requirement->numberRequired = 2;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement->user = $user;
        $userAchievement->achievement = $achievement;
        $userAchievement->goodieClaimed = null;
        R::store($userAchievement);

        $stamp = R::dispense('stamp');
        $stamp->userachievement = $userAchievement;
        $stamp->venueType = "Pool / Lake";
        $stamp->requirement = $requirement;
        $stamp->datetime = new DateTime();
        R::store($stamp);


        $poolLake = R::dispense('venuetype');
        $poolLake->type = "Pool / Lake";
        R::store($poolLake);

        $waterpark = R::dispense('venuetype');
        $waterpark->type = "Waterpark";
        R::store($waterpark);

        $gymPool = R::dispense('venuetype');
        $gymPool->type = "Gym Pool";
        R::store($gymPool);

        $requirement = R::dispense('requirement');
        $requirement->achievement = $achievement;
        $requirement->sharedVenueTypes = [$poolLake, $waterpark, $gymPool];
        $requirement->numberRequired = 2;
        R::store($requirement);


        $stamp = R::dispense('stamp');
        $stamp->userachievement = $userAchievement;
        $stamp->venueType = "Gym";
        $stamp->requirement = $requirement;
        $stamp->datetime = new DateTime();
        R::store($stamp);

        $achievement = R::dispense('achievement');
        $achievement->name = "Waterrat";
        $achievement->description = "Je bent graag in het water";
        $achievement->icon = "waterrat";
        $achievement->mystery = false;
        $achievement->earnMessage = 'Pas maar op dat je geen vinnen krijgt!';

        $goodie = R::dispense('goodie');
        $goodie->name = "Bidon";
        $goodie->description = "Een lekkere drinkfles";
        $goodie->icon = "bidon";
        $goodie->mystery = false;
        $achievement->goodie = $goodie;

        R::store($achievement);


        $requirement = R::dispense('requirement');
        $requirement->achievement = $achievement;
        $requirement->sharedVenueTypes = [$collegeGym, $gym];
        $requirement->numberRequired = 1;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement->user = $user;
        $userAchievement->achievement = $achievement;
        $userAchievement->goodieClaimed = null;
        R::store($userAchievement);

        $stamp = R::dispense('stamp');
        $stamp->userachievement = $userAchievement;
        $stamp->venueType = "Pool / Lake";
        $stamp->requirement = $requirement;
        $stamp->datetime = new DateTime();
        R::store($stamp);

        $requirement = R::dispense('requirement');
        $requirement->achievement = $achievement;
        $requirement->sharedVenueTypes = [$poolLake, $waterpark, $gymPool];
        $requirement->numberRequired = 1;
        R::store($requirement);

        $user = R::dispense('user');
        $user->name = 'Jeroen';
        $user->surname = 'van der Sanden';
        $user->email = "trend@marijnvdwerf.nl";
        $user->sex = "Male";
        $user->age = 21;
        $user->foursquareId = "55629080";
        $user->foursquareToken = "RJJEHFDSHJFHJKHF3493EEE98KJHFKJSHFJKHFJHSF9843UIHFJHSFJSIFH04823DHJ";
        $user->facebookId = "";
        $user->facebookToken = "";
        $user->setMeta('cast.foursquareId', 'string');
        $user->setMeta('cast.facebookId', 'string');
        R::store($user);

        $address = R::dispense('address');
        //$user ->ownAddress = [$adress];
        $address->user = $user;
        $address->address = "Hoofdstraat 4";
        $address->postalCode = "1234AD";
        $address->town = "Amsterdam";
        R::store($address);
    }

    public function emptyDatabase()
    {
        R::exec('SET FOREIGN_KEY_CHECKS=0;');
        R::wipe('achievement');
        R::wipe('address');
        R::wipe('goodie');
        R::wipe('requirement');
        R::wipe('stamp');
        R::wipe('user');
        R::wipe('userachievement');
        R::wipe('venuetype');
    }

    public function getUserForFoursquareId($userId)
    {
        return $user = R::findOne('user', 'foursquare_id = ? ', [$userId]);
    }

    public function getAchievementsForCategories($categories)
    {
        //GET venuetype ID
        $relatedVenueTypes = R::findAll('venuetype', ' WHERE type IN (' . R::genSlots($categories) . ') ', $categories);

        //GET requirement and achievements owning the venuetype
        R::preload($relatedVenueTypes, '*.requirement, *.achievement');

        //CREATE array with achievements per venue;
        $venueAchievements = [];
        foreach ($relatedVenueTypes as $venueType) {
            foreach ($venueType->sharedRequirement as $requirement) {
                $venueAchievements[] = $requirement->achievement;

            }
        }
        return $venueAchievements;
    }

    public function getUserAchievement($achievementId, $userId)
    {
        $userAchievement = R::findOne('userachievement',
            'achievement_id = :venueAchievement AND user_id = :userID',
            array(
                ':venueAchievement' => $achievementId,
                ':userID' => $userId
            )
        );

        if ($userAchievement === null) {
            $userAchievement = R::dispense('userachievement');
            $userAchievement->user = R::load('user', $userId);
            $userAchievement->achievement = R::load('achievement', $achievementId);
        }

        return $userAchievement;
    }

    public function getFoursquareUrl($url, $version)
    {
        global $foursquare_client_id, $foursquare_client_secret;
        return 'https://api.foursquare.com/' . $url . '?client_id=' . $foursquare_client_id . '&client_secret=' . $foursquare_client_secret . '&v=' . $version;
    }

    public function getCategoryTree($categoryId)
    {
        $data = json_decode(file_get_contents('../data/categories.json'));
        foreach ($data->response->categories as $mainCategory) {
            if ($mainCategory->id === $categoryId) {
                return [$mainCategory->name];
            }

            foreach ($mainCategory->categories as $subCategory) {
                if ($subCategory->id === $categoryId) {
                    return [$subCategory->name, $mainCategory->name];
                }

                foreach ($subCategory->categories as $subsubCategory) {
                    if ($subsubCategory->id === $categoryId) {
                        return [$subsubCategory->name, $subCategory->name, $mainCategory->name];
                    }
                }
            }
        }

        return [];
    }

    public function addFourSquareCheckin($foursquareUserId, $checkinData)
    {
        $user = $this->getUserForFoursquareId($foursquareUserId);

        $this->logger->addDebug('User checked in', [
            'user' => $user->export(),
            'checkin' => $checkinData
        ]);

        $categories = [];
        $url = $this->getFoursquareUrl('v2/venues/' . $checkinData->venue->id, '20130625');
        $venueData = json_decode(file_get_contents($url));

        foreach ($venueData->response->venue->categories as $category) {
            $categories = array_merge($this->getCategoryTree($category->id), $categories);
        }

        $this->logger->addDebug('Categories for venue', $categories);

        if (count($categories) < 1) {
            return;
        }

        $relatedAchievements = $this->getAchievementsForCategories($categories);

        $this->logger->addDebug('Related achievements', $relatedAchievements);

        foreach ($relatedAchievements as $achievement) {
            $this->logger->addDebug('Relevante achievement gevonden', $achievement->export());
            $userAchievement = $this->getUserAchievement($achievement->id, $user->id);
            if ($userAchievement->getProgress() < 1) {
                $stamp = $this->getNewStamp($achievement->id, $categories);
                $this->logger->addInfo('User received stamp', [
                    'achievement' => $achievement->export(),
                    'userAchievement' => $userAchievement->export(),
                    'stamp' => $stamp->export()
                ]);

                $userAchievement->ownStamp[] = $stamp;
                R::store($userAchievement);

                if ($userAchievement->getProgress() == 1) {
                    $message = $this->getNotificationMessage($user->id, 'achievement-earned', $achievement);
                    $this->notificationManager->sendMessage($message);

                    $this->logger->addInfo('User completed achievement', [
                        'achievement' => $achievement->export(),
                        'userAchievement' => $userAchievement->export(),
                        'stamp' => $stamp->export()
                    ]);
                }
            }
        }
    }

    public function getNewStamp($achievementId, $categories)
    {
        $requirements = R::find('requirement', ' achievement_id = ?', [$achievementId]);

        foreach ($requirements as $requirement) {
            foreach ($requirement->sharedVenuetype as $venueType) {
                if (!in_array($venueType->type, $categories)) {
                    continue;
                }
                $stamp = R::dispense('stamp');
                $stamp->venueType = $venueType;
                $stamp->requirement = $requirement;
                $stamp->datetime = new DateTime();
                return $stamp;
            }
        }

        $this->logger->addError('No relevant requirement found', [
            'achievement' => R::findOne('achievement', $achievementId)->export(),
            'requirements' => $requirements,
            'categories' => $categories
        ]);
        throw new Exception('No relevant requirement found');
    }


    /**
     * @param $userId
     * @param $notificationType
     * @param $data
     * @throws Exception
     * @return Message|null
     */
    public function getNotificationMessage($userId, $notificationType, $data)
    {
        /**
         * @var $user Model_User
         */
        $user = R::findOne('user', $userId);

        if ($user->getNotificationSetting($notificationType) === false) {
            return null;
        }
        $this->logger->debug($user->getNotificationMedium());
        switch ($user->getNotificationMedium()) {
            case 'facebook':
                throw new Exception('Message type unimplemented');
                break;
            case 'email':
                return new EmailMessage($user, $notificationType, $data);
            case 'sms';
                return new TextMessage($user, $notificationType, $data);
        }

        return null;
    }

    public function getAchievements()
    {
        return R::findAll('achievement');
    }

    public function getUserForFoursquareToken($token)
    {
        $user = R::findOne('user', 'foursquare_token = ?', [$token]);
        if ($user !== null) {
            return $user;
        }

        $user = R::dispense('user');

        try {
            $foursquare = file_get_contents('https://api.foursquare.com/v2/users/self?v=20130614&oauth_token=' . $token);
        } catch (Exception $e) {
            return false;
        }

        $foursquare = json_decode($foursquare);

        $user->foursquareId = $foursquare->response->user->id;
        $user->foursquareToken = $token;

        $user->name = $foursquare->response->user->firstName;
        if (isset($foursquare->response->user->lastName)) {
            $user->surname = $foursquare->response->user->lastName;
        }

        if (isset($foursquare->response->user->contact->email)) {
            $user->email = $foursquare->response->user->contact->email;
        }

        if (isset($foursquare->response->user->contact->phone)) {
            $user->phone = $foursquare->response->user->contact->phone;
        }

        R::store($user);
        return $user;
    }
}


class Model_Userachievement extends RedBean_SimpleModel
{
    public function getProgress()
    {
        $totalRequired = $this->bean->achievement->getStampsRequired();
        return count($this->bean->ownStamp) / $totalRequired;
    }

    public function getStamps()
    {
        return $this->bean->ownStamp;
    }
}


class Model_Achievement extends RedBean_SimpleModel
{
    public function getStampsRequired()
    {
        $totalRequired = 0;

        $requirements = $this->bean->ownRequirement;
        foreach ($requirements as $requirement) {
            $totalRequired += (int)$requirement->numberRequired;
        }

        return $totalRequired;
    }
}


class Model_User extends RedBean_SimpleModel
{
    private $notificationDefaults = [
        'achievement-earned' => true,
        'goodie-earned' => true,
        'new-stamp' => false,
    ];

    public function getSetting($settingName, $defaultValue)
    {
        $setting = R::findOne('usersetting', 'user_id = :userid AND name = :setting', [
            ':setting' => $settingName,
            ':userid' => $this->bean->id
        ]);

        if ($setting === null) {
            $setting = R::dispense('usersetting');
            $setting->userId = $this->bean->id;
            $setting->name = $settingName;
            $setting->value = $defaultValue;
            R::store($setting);
        }

        return $setting->value;
    }

    public function setSetting($settingName, $value)
    {
        $setting = R::findOne('usersetting', 'user_id = :userid AND name = :setting', [
            ':setting' => $settingName,
            ':userid' => $this->bean->id
        ]);

        $setting->value = $value;

        R::store($setting);
    }

    public function getNotificationSetting($type)
    {
        return (boolean)$this->getSetting('notify-' . $type, $this->notificationDefaults[$type]);
    }

    public function getSelectedNotifications()
    {
        $output = [];
        foreach ($this->notificationDefaults as $type => $value) {
            if ($this->getNotificationSetting($type)) {
                $output[] = $type;
            }
        }
        return $output;
    }

    public function setNotificationSetting($type, $value)
    {
        $this->setSetting('notify-' . $type, $value);
    }

    public function getNotificationMedium()
    {
        $options = $this->getNotificationMediumOptions();

        $selectedMedium = $this->getSetting('notification-medium', $options[0]);

        if (!in_array($selectedMedium, $options)) {
            $this->setSetting('notification-medium', $options[0]);
        }

        return $this->getSetting('notification-medium', $options[0]);
    }

    public function getNotificationMediumOptions()
    {
//        if ($this->bean->facebook_token !== null) {
//            return ['facebook'];
//        }

        $options = [];
        if ($this->bean->phone !== null) {
            $options[] = 'sms';
        }

        $options[] = 'email';

        return $options;
    }
}
