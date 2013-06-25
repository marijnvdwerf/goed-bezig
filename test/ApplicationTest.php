<?php
include dirname(__FILE__) . '/../vendor/autoload.php';
include dirname(__FILE__) . '/../includes/vendor/rb.php';
include dirname(__FILE__) . '/../includes/Application.php';
include dirname(__FILE__) . '/../includes/config.php';

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    protected function setUp()
    {
        $this->app = new Application(new \Monolog\Logger('TestLog'), true);
    }

    function testGetParentCategories()
    {
        $categories = $this->app->getCategoryTree('4bf58dd8d48988d1a8941735');
        $this->assertSame(['General College & University', 'College & University'], $categories);

        $categories = $this->app->getCategoryTree('4d954b0ea243a5684a65b473');
        $this->assertSame(['Convenience Store', 'Shop & Service'], $categories);

        $categories = $this->app->getCategoryTree('4bf58dd8d48988d105951735');
        $this->assertSame(['Kids Store', 'Clothing Store', 'Shop & Service'], $categories);
    }


    function testGetRelevantAchievements()
    {
        $this->app->loadTestData();
        $categories = ['Gym', "Shop & Service", "Gym / Fitness Center"];
        $achievements = $this->app->getAchievementsForCategories($categories);
        $this->assertCount(2, $achievements);
    }

    function testGetUserAchievements()
    {
        $this->app->loadTestData();

        $userAchievement = $this->app->getUserAchievement(1, 1);
        $this->assertEquals(0.5, $userAchievement->getProgress());
        $this->assertTrue(is_array($userAchievement->ownStamp));
        $this->assertCount(2, $userAchievement->ownStamp);

        $userAchievement = $this->app->getUserAchievement(2, 1);
        $this->assertEquals(0.5, $userAchievement->getProgress());
        $this->assertTrue(is_array($userAchievement->ownStamp));
        $this->assertCount(1, $userAchievement->ownStamp);

        $userAchievement = $this->app->getUserAchievement(1, 2);
        $this->assertEquals(0, $userAchievement->getProgress());
        $this->assertCount(0, $userAchievement->ownStamp);

        $userAchievement = $this->app->getUserAchievement(2, 2);
        $this->assertEquals(0, $userAchievement->getProgress());
        $this->assertCount(0, $userAchievement->ownStamp);
    }

    function testGetUserForFoursquareId()
    {
        $userA = $this->app->getUserForFoursquareId('00023043287276367263');
        $this->assertSame('John', $userA->name);
        $this->assertSame('1', $userA->id);

        $userB = $this->app->getUserForFoursquareId('55629080');
        $this->assertSame('Jeroen', $userB->name);
        $this->assertSame('2', $userB->id);
    }

    function testGetUserForFoursquareToken()
    {
        $userA = $this->app->getUserForFoursquareToken('RJJEHFDSHJFHJKHF34938598KJHFKJSHFJKHFJHSF9843UIHFJHSFJSIFH04823DHJ');
        $this->assertEquals('1', $userA->id);
        $this->assertEquals('John', $userA->name);

        $userB = $this->app->getUserForFoursquareToken('MWZW05JBGRLOYSEOC35JWYEPATTBNTJNJOUWOKR23XMGO3VM');
        $this->assertEquals('3', $userB->id);
        $this->assertEquals('Jeroen', $userB->name);
        $this->assertEquals('van der Sanden', $userB->surname);
        $this->assertEquals(null, $userB->phone);
        $this->assertEquals('trend@marijnvdwerf.nl', $userB->email);
        $this->assertEquals('55629080', $userB->foursquareId);
        $this->assertEquals('MWZW05JBGRLOYSEOC35JWYEPATTBNTJNJOUWOKR23XMGO3VM', $userB->foursquareToken);

        $userC = $this->app->getUserForFoursquareToken('MWZW05JBGRLOYSEOC35JWYEPATTBNTJNJOUWOKR23XMGO3VN');
        $this->assertSame(false, $userC);
    }

    function testAddingFoursquareCheckin()
    {
        $checkinData = '{"id":"51a0ecad498ec1fa9824f0ff","createdAt":1369500845,"type":"checkin","timeZone":
        "Europe\/Amsterdam","timeZoneOffset":120,"user":{"id":"55629080","firstName":"Jeroen","lastName":
        "van der Sanden","gender":"male","relationship":"self","photo":"https:\/\/foursquare.com\/img\/blank_boy.png",
        "tips":{"count":0},"lists":{"groups":[{"type":"created","count":1,"items":[]}]},"homeCity":"Eindhoven, 06",
        "bio":"","contact":{"email":"trend@marijnvdwerf.nl","facebook":"100005427698197"}},"venue":{"id":
        "4cb4aafc75ebb60c2a53e4ad","name":"Delphis Womens Health Club","contact":{"phone":"0235257833","formattedPhone":
        "023 525 7833"},"location":{"address":"Zijlweg 314a+b","lat":52.386945,"lng":4.610468,"postalCode":"2015 CP",
        "city":"Haarlem","state":"Noord-Holland","country":"The Netherlands","cc":"NL"},"canonicalUrl":
        "https:\/\/foursquare.com\/v\/delphis-womens-health-club\/4cb4aafc75ebb60c2a53e4ad","categories":[{"id":
        "4bf58dd8d48988d176941735","name":"Gym","pluralName":"Gyms","shortName":"Gym","icon":
        "https:\/\/foursquare.com\/img\/categories\/building\/gym.png","parents":["Shop & Service",
        "Gym \/ Fitness Center"],"primary":true}],"verified":false,"stats":{"checkinsCount":387,"usersCount":26,
        "tipCount":1},"likes":{"count":0,"groups":[]},"beenHere":{"count":0}}}';
        $checkinData = json_decode($checkinData);

        $jeroenId = '55629080';
        $this->app->addFourSquareCheckin($jeroenId, $checkinData);
        $jeroen = $this->app->getUserForFoursquareId($jeroenId);
        $this->assertCount(2, $jeroen->ownUserachievement);
        $this->assertEquals(0.25, $jeroen->ownUserachievement[3]->getProgress());
        $this->assertEquals(0.5, $jeroen->ownUserachievement[4]->getProgress());

        $johnId = '00023043287276367263';

        // For John we need to check if a notification is sent, so we create a fake NotificationManager
        // We create a class that only has the sendMessage method
        $fakeNotificationManager = $this->getMock('NotificationManager', array('sendMessage'));
        $fakeNotificationManager
            ->expects($this->once())
            ->method('sendMessage');
        $this->app->notificationManager = $fakeNotificationManager;

        $this->app->addFourSquareCheckin($johnId, $checkinData);
        $john = $this->app->getUserForFoursquareId($johnId);
        $this->assertCount(2, $john->ownUserachievement);
        $this->assertEquals(0.75, $john->ownUserachievement[1]->getProgress());
        $this->assertEquals(1, $john->ownUserachievement[2]->getProgress());
    }

    function testNotificationGeneration()
    {
        $user = R::findOne('user', 1);

        $user->setSetting('notification-medium', 'sms');
        $user->setNotificationSetting('achievement-earned', true);
        $user->facebookToken = null;
        R::store($user);

        $achievement = R::findOne('achievement', 1);
        $message = $this->app->getNotificationMessage(1, 'achievement-earned', $achievement);
        $this->assertInstanceOf('TextMessage', $message);
        $this->assertSame('Gefeliciteerd John, je hebt een achievement vrijgespeeld. Laat die spierballen maar zien!', $message->body);
        $this->assertSame('31612345678', $message->recipient);
        $this->assertSame('GoedBezig', $message->origin);

        $user->setNotificationSetting('achievement-earned', false);
        R::store($user);
        $message = $this->app->getNotificationMessage(1, 'achievement-earned', 1);
        $this->assertSame(null, $message);
    }

    function testGetAchievements()
    {
        $achievements = $this->app->getAchievements();
        $this->assertCount(2, $achievements);

        $achievement = $achievements[1];
        $this->assertEquals('Sporter', $achievement->name);
        $this->assertEquals('Je bent sportief bezig', $achievement->description);
        $this->assertEquals('sporter', $achievement->icon);
        $this->assertEquals(false, (boolean)$achievement->mystery);

        $achievement = $achievements[2];
        $this->assertEquals('Waterrat', $achievement->name);
        $this->assertEquals('Je bent graag in het water', $achievement->description);
        $this->assertEquals('waterrat', $achievement->icon);
        $this->assertEquals(false, (boolean)$achievement->mystery);
    }

    function testUserSettings()
    {
        $this->app->loadTestData();
        $user = R::findOne('User', 1);

        $this->assertSame(false, (boolean)$user->getSetting('test', false));
        $this->assertSame(false, (boolean)$user->getSetting('test', true));

        $this->assertSame(true, $user->getNotificationSetting('achievement-earned'));
        $this->assertSame(true, $user->getNotificationSetting('goodie-earned'));
        $this->assertSame(false, $user->getNotificationSetting('new-stamp'));

        $this->assertSame(['achievement-earned', 'goodie-earned'], $user->getSelectedNotifications());

        $user->facebookToken = null;
        $user->phone = '0612345678';
        $this->assertSame(['sms', 'email'], $user->getNotificationMediumOptions());
        $this->assertSame('sms', $user->getNotificationMedium());

        $user->phone = null;
        $this->assertSame(['email'], $user->getNotificationMediumOptions());
        $this->assertSame('email', $user->getNotificationMedium());

        //$user->facebookToken = '663366';
        //$this->assertSame('facebook', $user->getNotificationMedium());
    }
}
