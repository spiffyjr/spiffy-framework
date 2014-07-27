<?php

namespace Spiffy\Framework;

use Spiffy\Framework\Test\FunctionalTestTrait;
use Spiffy\View\ViewModel;

class RenderTest extends \PHPUnit_Framework_TestCase
{
    use FunctionalTestTrait;

    public function testRenderMissingTemplate()
    {
        $app = $this->getApplication();
        
        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $dispatcher = $app->getInjector()->nvoke('Dispatcher');
        $dispatcher->add('foo', function() {
            $model = new ViewModel();
            $model->setTemplate('testing');
            
            return $model;
        });

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('h1'));
        $this->assertSame('500 <small>application error</small>', $crawler->filter('h1')->html());
    }
    
    public function testRenderWithValidTemplate()
    {
        $app = $this->getApplication();
        
        /** @var \Twig_Environment $twig */
        $twig = $app->getInjector()->nvoke('twig.environment');
        $twig->getLoader()->addPath(__DIR__ . '/view');

        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $dispatcher = $app->getInjector()->nvoke('Dispatcher');
        $dispatcher->add('foo', function() {
            $model = new ViewModel();
            $model->setTemplate('render');

            return $model;
        });

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('bar', $crawler->filter('div.foo')->html());
    }
    
    protected function setUp()
    {
        $this->reset();

        $app = $this->getApplication();

        /** @var \Spiffy\Route\Router $router */
        $router = $app->getInjector()->nvoke('Router');
        $router->add('home', '/', ['defaults' => ['action' => 'foo']]);
    }
}
