<?php
include dirname(__FILE__) . '/../vendor/autoload.php';
include dirname(__FILE__) . '/../includes/vendor/rb.php';
include dirname(__FILE__) . '/../includes/Application.php';

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    protected function setUp()
    {
        $this->app = new Application(true);
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

    function testNotificationGeneration()
    {
        $message = $this->app->getNotificationMessage(1, 'achievement-earned', 1);
        $this->assertInstanceOf('TextMessage', $message);
        $this->assertSame('Gefeliciteerd John, je hebt een achievement vrijgespeeld. Laat die spierballen maar zien!', $message->body);
        $this->assertSame('31612345678', $message->recipient);
        $this->assertSame('GoedBezig', $message->origin);
    }

}
