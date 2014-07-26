<?php

namespace Spiffy\Framework\Plugin\PackageManager;

use Spiffy\Event\Event;
use Spiffy\Event\EventManager;
use Spiffy\Framework\ApplicationConfig;
use Spiffy\Package\PackageManager;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\PackageManager\MergeAnnotatedServicesPlugin
 */
class MergeAnnotatedServicesPluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var Event */
    protected $e;
    /** @var \Spiffy\Framework\Plugin\PackageManager\MergeAnnotatedServicesPlugin */
    protected $p;
    /** @var PackageManager */
    protected $pm;

    /**
     * @covers ::__construct
     * @covers ::plug
     */
    public function testPlug()
    {
        $p = $this->p;
        $em = new EventManager();
        $p->plug($em);

        $this->assertCount(2, $em->getEvents(PackageManager::EVENT_MERGE_CONFIG));
    }

    /**
     * @covers ::registerPackagePaths
     */
    public function testRegisterPackagePaths()
    {
        $p = $this->p;
                
        $p->registerPackagePaths($this->e);
        
        $refl = new \ReflectionClass($p);
        $finder = $refl->getProperty('finder');
        $finder->setAccessible(true);
        
        $finder = $finder->getValue($p);
        $this->assertCount(3, $finder);
    }

    /**
     * @covers ::mergeServices
     */
    public function testMergeServicesWithNoDirs()
    {
        $pm = new PackageManager();
        $pm->add('Spiffy\Framework\TestAsset\BasicPackage', 'Spiffy\Framework\TestAsset\BasicPackage\Package');
        $pm->load();

        $e = new Event();
        $e->setTarget($pm);

        $p = $this->p;
        $p->registerPackagePaths($e);
        $this->assertEmpty($p->mergeServices());
    }

    /**
     * @covers ::mergeServices
     */
    public function testMergeServicesWithNoComponents()
    {
        $pm = new PackageManager();
        $pm->add('Spiffy\Framework\TestAsset\ApplicationPackage', 'Spiffy\Framework\TestAsset\ApplicationPackage\Package');
        $pm->load();
        
        $e = new Event();
        $e->setTarget($pm);
        
        $p = $this->p;
        $p->registerPackagePaths($e);
        $this->assertEmpty($p->mergeServices());
    }

    /**
     * @covers ::mergeServices
     */
    public function testMergeServices()
    {
        $p = $this->p;
        $p->registerPackagePaths($this->e);
        $result = $p->mergeServices();
        
        $this->assertSame([
            'framework' => [
                'services' => [
                    'framework.test-asset.annotated-component' => [
                        'Spiffy\Framework\TestAsset\TestPackage\AnnotatedComponent',
                        [
                            '@foo',
                            '$params'
                        ],
                        [
                            'setSetter' => '$setter'
                        ]
                    ]
                ]
            ]
        ], $result);
    }

    protected function setUp()
    {
        $this->p = new MergeAnnotatedServicesPlugin(new ApplicationConfig());

        $pm = new PackageManager();
        $pm->add('Spiffy\Framework\TestAsset\BasicPackage', 'Spiffy\Framework\TestAsset\BasicPackage\Package');
        $pm->add('Spiffy\Framework\TestAsset\TestPackage');
        $pm->add('Spiffy\Framework\TestAsset');
        $pm->load();

        $this->e = $e = new Event();
        $e->setTarget($pm);
        
        $this->pm = $pm;
    }
}
