<?php

namespace Spiffy\Framework\Plugin\PackageManager;

use Spiffy\Event\Event;
use Spiffy\Event\EventManager;
use Spiffy\Framework\ApplicationConfig;
use Spiffy\Package\PackageManager;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\PackageManager\MergeApplicationConfigPlugin
 */
class PackageManagerPluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Spiffy\Framework\Plugin\PackageManager\MergeApplicationConfigPlugin */
    protected $p;

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $p = $this->p;
        $em = new EventManager();
        $p->plug($em);

        $this->assertCount(1, $em->getEvents(PackageManager::EVENT_MERGE_CONFIG));
    }

    /**
     * @covers ::mergeApplicationPackageConfig
     */
    public function testMergeApplicationPackageConfig()
    {
        $pm = new PackageManager();
        
        $event = new Event();
        $event->setTarget($pm);
        
        $pm->add('Spiffy\Package\TestAsset\Path');
        $pm->add('Spiffy\Framework\TestAsset\ApplicationPackage');
        $pm->load();
        
        $p = $this->p;
        $result = $p->mergeApplicationPackageConfig($event);
        
        $this->assertTrue(is_array($result));
        $this->assertSame(
            $result['framework']['routes'],
            include __DIR__ . '/../../TestAsset/ApplicationPackage/config/routes.php'
        );
    }

    protected function setUp()
    {
        $this->p = new MergeApplicationConfigPlugin(new ApplicationConfig());
    }
}
