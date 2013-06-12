<?php

include 'TextMessage.php';

class Application
{
    function __construct($testing = false)
    {
        global $db_host, $db_name, $db_user, $db_pass;
        if (!$testing) {
            R::setup('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass);
        } else {
            R::setup('sqlite::memory:');
        }
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
        $user->facebookId = "01005002392872387482";
        $user->facebookToken = "AJFNH764JHFJHFJ583HFHRJH987398479FHISJKJFGJ3476HDGJHGFJ98724JHGFJG";
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
        $userAchievement->progress = 0.80;
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
        R::store($achievement);

        $goodie = R::dispense('goodie');
        $goodie->achievement = $achievement;
        $goodie->name = "Bidon";
        $goodie->description = "Een lekkere drinkfles";
        $goodie->icon = "bidon";
        $goodie->mystery = false;
        R::store($goodie);


        $requirement = R::dispense('requirement');
        $requirement->achievement = $achievement;
        $requirement->sharedVenueTypes = [$collegeGym, $gym];
        $requirement->numberRequired = 2;
        R::store($requirement);

        $userAchievement = R::dispense('userachievement');
        $userAchievement->user = $user;
        $userAchievement->achievement = $achievement;
        $userAchievement->progress = 0.80;
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
        $requirement->numberRequired = 2;
        R::store($requirement);

        $stamp = R::dispense('stamp');
        $stamp->userachievement = $userAchievement;
        $stamp->venueType = "Gym";
        $stamp->requirement = $requirement;
        $stamp->datetime = new DateTime();
        R::store($stamp);

        $user = R::dispense('user');
        $user->name = 'Jeroen';
        $user->surname = 'van der Sanden';
        $user->email = "trend@marijnvdwerf.nl";
        $user->sex = "Male";
        $user->age = 21;
        $user->foursquareId = "55629080";
        $user->foursquareToken = "RJJEHFDSHJFHJKHF3493EEE98KJHFKJSHFJKHFJHSF9843UIHFJHSFJSIFH04823DHJ";
        $user->facebookId = "01005002393472387482";
        $user->facebookToken = "AJFNH764JHFJHFJ583HFHRRT587398479FHISJKJFGJ3476HDGJHGFJ98724JHGFJG";
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
                var_dump($requirement->achievement->name);
            }
        }

        return $venueAchievements;
    }

    /**
     * @return TextMessage
     */
    public function getNotificationMessage($userId, $notificationType, $data)
    {
        $user = R::findOne('user', $userId);


        switch ($notificationType) {
            case 'achievement-earned':
                $achievement = R::findOne('achievement', $data);
                $body = 'Gefeliciteerd ' . $user->name . ', je hebt een achievement vrijgespeeld. ' . $achievement->earnMessage;
                break;
        }

        $message = new TextMessage();
        $message->body = $body;
        $message->origin = 'GoedBezig';
        $message->recipient = $user->phone;
        return $message;
    }
}