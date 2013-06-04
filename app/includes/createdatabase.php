<?php

function create_db() 
{

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

        }