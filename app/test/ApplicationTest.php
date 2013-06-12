<?php
include dirname(__FILE__) . '/../includes/Application.php';
include dirname(__FILE__) . '/../vendor/autoload.php';
include dirname(__FILE__) . '/../includes/vendor/rb.php';
require dirname(__FILE__) . '/../includes/config.php';

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
        $start = microtime(true);
        $this->app->loadTestData();
        echo microtime(true) - $start . 's';
        $categories = ['Gym', "Shop & Service", "Gym / Fitness Center"];
        $achievements = $this->app->getAchievementsForCategories($categories);
        $this->assertCount(2, $achievements);
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
