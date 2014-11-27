<?php

namespace BabyMonitor\test\BabyMonitoryTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Db\Resultset\Resultset;
use BabyMonitor\Model\FeedModel;

class ControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(__DIR__) . '/../TestConfig.php.dist'
        );
        parent::setUp();
    }

    public function testFeedHomePageShowsNoRecordsMessageWhenNoRecordsArePresent()
    {
        $mockTable = \Mockery::mock('BabyMonitor\Tables\FeedTable');
        $mockTable->shouldReceive('fetchMostRecentFeeds')
            ->once()
            ->andReturn(new ResultSet());

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'BabyMonitor\Tables\FeedTable', $mockTable
        );
        $serviceManager->setService(
            'BabyMonitor\Cache\Application', null
        );

        $this->dispatch('/baby-monitor/feeds');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('BabyMonitor');
        $this->assertControllerName(
            'BabyMonitor\Controller\Feeds'
        );
        $this->assertControllerClass(
            'FeedsController'
        );
        $this->assertActionName("Index");
        $this->assertMatchedRouteName('feeds/action');
        $this->assertQueryContentContains(
            "h1", "Feeds Home"
        );
        $this->assertQueryContentContains(
            "td", "No Records Available"
        );
    }

    public function testFeedHomePageCanBeAccessed()
    {
        $this->dispatch('/baby-monitor/feeds');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('BabyMonitor');
        $this->assertControllerName(
            'BabyMonitor\Controller\Feeds'
        );
        $this->assertControllerClass(
            'FeedsController'
        );
        $this->assertActionName("Index");
        $this->assertMatchedRouteName('feeds/action');
        $this->assertQueryContentContains(
            "h1", "Feeds Home"
        );

    }

    public function testFeedManagePageCanBeAccessed()
    {
        $this->dispatch('/baby-monitor/feeds/manage');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('BabyMonitor');
        $this->assertControllerName(
            'BabyMonitor\Controller\Feeds'
        );
        $this->assertControllerClass(
            'FeedsController'
        );
        $this->assertActionName("Manage");
        $this->assertMatchedRouteName('feeds/manage');
        $this->assertQueryContentContains(
            "h1", "Manage Feeds"
        );
    }

    public function testFeedHomePageShowsRecordsWhenRecordsArePresent()
    {
        $feed = new FeedModel();
        $feed->exchangeArray(array(
            'userId' => 2,
            'feedId' => 1,
            'feedDate' => "01-01-2014",
            'feedTime' => "19:00",
            'feedAmount' => 131,
            'feedTemperature' => 34,
            'feedNotes' => "Nothing to say really"
        ));

        $resultSet = new ResultSet();
        $resultSet->initialize(array($feed));

        $mockTable = \Mockery::mock('BabyMonitor\Tables\FeedTable');
        $mockTable->shouldReceive('fetchMostRecentFeeds')
            ->once()
            ->andReturn($resultSet);

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'BabyMonitor\Tables\FeedTable', $mockTable
        );
        
        $this->dispatch('/baby-monitor/feeds');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('BabyMonitor');
        $this->assertControllerName(
            'BabyMonitor\Controller\Feeds'
        );
        $this->assertControllerClass(
            'FeedsController'
        );
        $this->assertActionName("Index");
        $this->assertMatchedRouteName('feeds/action');
        $this->assertQueryContentContains(
            "h1", "Feeds Home"
        );

        $this->assertQueryContentContains(
            "td[class='feed-date']", "Jan 1, 2014, 7:00:00 PM"
        );
        $this->assertQueryContentContains(
            "td[class='feed-amount']", "131"
        );
        $this->assertQueryContentContains(
            "td[class='feed-temperature']", "34"
        );
    }

}
