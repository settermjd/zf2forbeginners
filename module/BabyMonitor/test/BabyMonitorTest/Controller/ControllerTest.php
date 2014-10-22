<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 23/08/14
 * Time: 07:28
 */

namespace BabyMonitor\test\BabyMonitoryTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

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
}