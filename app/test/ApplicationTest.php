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
        $this->app = new Application();
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

}
