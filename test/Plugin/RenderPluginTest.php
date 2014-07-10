<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\View\ViewManager;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\RenderPlugin
 */
class RenderPluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\RenderPlugin
     */
    protected $p;

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $em = new EventManager();

        $p = $this->p;
        $p->plug($em);

        $this->assertCount(1, $em->getEvents(Application::EVENT_RENDER));
    }

    /**
     * @covers ::handleRenderException
     */
    public function testRenderHandlesExceptions()
    {
        $ex = new \RuntimeException();
        
        $p = $this->p;
        $event = $this->event;
        $event->set('exception', $ex);
        
        $this->assertNull($p->handleRenderException($event));
        
        $event->setError(Application::ERROR_RENDER_EXCEPTION);
        $p->handleRenderException($event);
        
        $expected = new ViewModel([
            'type' => 'exception',
            'exception_class' => 'RuntimeException',
            'exception' => $ex,
            'previous_exceptions' => []
        ]);
        $expected->setTemplate('error/exception');
        
        $this->assertEquals($expected, $event->getDispatchResult());
        $this->assertEquals($expected, $event->getModel());
        $this->assertSame(500, $event->getResponse()->getStatusCode());
    }

    /**
     * @covers ::injectTemplate
     */
    public function testInjectTemplate()
    {
        $p = $this->p;
        $event = $this->event;
        $this->assertNull($p->injectTemplate($event));
        
        $event = clone $this->event;
        $model = new ViewModel();
        $model->setTemplate('foo');
        $event->setModel($model);
        $this->assertNull($p->injectTemplate($event));
        
        $event = clone $this->event;
        $model = new ViewModel();
        $event->setModel($model);
        $this->assertNull($p->injectTemplate($event));

        $event->setAction('My\Class\IsCoolAction');
        $p->injectTemplate($event);
        
        $this->assertSame('my/class/is-cool', $model->getTemplate());
    }

    protected function setUp()
    {
        parent::setUp();
        $i = $this->app->getInjector();
        $i->nject('ViewManager', new ViewManager(new VardumpStrategy()));
    }

    /**
     * @return \Spiffy\Event\Plugin|RenderPlugin
     */
    protected function createPlugin()
    {
        return new RenderPlugin();
    }
}
