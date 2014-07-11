<?php

namespace Spiffy\Framework\Twig;
use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Framework\Twig\TwigLoaderFactory
 */
class TwigLoaderFactoryTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $i = new Injector();
        $i['framework'] = [
            'twig' => [
                'paths' => [
                    realpath(__DIR__),
                    realpath(__DIR__ . '/..'),
                ] 
            ]
        ];
        
        $factory = new TwigLoaderFactory();
        $result = $factory->createService($i);
        
        $this->assertInstanceOf('Twig_Loader_Filesystem', $result);
        $this->assertSame(array_reverse($i['framework']['twig']['paths']), $result->getPaths());
    }
}
