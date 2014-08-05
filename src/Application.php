<?php

namespace Spiffy\Framework;

use Spiffy\Event\EventsAwareTrait;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Inject\Injector;
use Spiffy\Inject\InjectorUtils;

final class Application
{
    use EventsAwareTrait;

    const ERROR_DISPATCH_INVALID = 'spiffy.framework:dispatch.invalid';
    const ERROR_DISPATCH_EXCEPTION = 'spiffy.framework:dispatch.exception';
    const ERROR_RENDER_EXCEPTION = 'spiffy.framework:render.exception';
    const ERROR_ROUTE_INVALID = 'spiffy.framework:route.invalid';

    const EVENT_BOOTSTRAP = 'spiffy.framework:bootstrap';
    const EVENT_DISPATCH = 'spiffy.framework:dispatch';
    const EVENT_DISPATCH_ERROR = 'spiffy.framework:dispatch.error';
    const EVENT_RENDER = 'spiffy.framework:render';
    const EVENT_RENDER_ERROR = 'spiffy.framework:render.error';
    const EVENT_RESPOND = 'spiffy.framework:respond';
    const EVENT_RESPOND_ERROR = 'spiffy.framework:respond';
    const EVENT_ROUTE = 'spiffy.framework:route';
    const EVENT_ROUTE_ERROR = 'spiffy.framework:route_error';

    /**
     * @var \Spiffy\Framework\ApplicationConfig
     */
    private $config;

    /**
     * @var \Spiffy\Framework\ApplicationEvent
     */
    private $event;

    /**
     * @var Injector
     */
    private $injector;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new ApplicationConfig($config);
        $this->injector = new Injector();
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->config->isDebug();
    }

    /**
     * Bootstrap the application by firing EVENT_BOOTSTRAP. Certain services are expected
     * to exist After bootstrapping. If you override the default plugins you are required
     * to ensure they exist. There are no safety checks!
     *
     * The following is a list of expected services:
     * - Dispatcher
     * - PackageManager
     * - Router
     * - ViewManager
     *
     * @return $this
     */
    public function bootstrap()
    {
        $event = $this->getEvent();
        $event->setType(self::EVENT_BOOTSTRAP);
        $this->events()->fire($event);

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function run()
    {
        $event = $this->getEvent();

        $event->setType(self::EVENT_ROUTE);
        $this->events()->fire($event);

        $event->setType(self::EVENT_DISPATCH);
        $this->events()->fire($event);

        $event->setType(self::EVENT_RENDER);
        $this->events()->fire($event);

        $event->setType(self::EVENT_RESPOND);
        $this->events()->fire($event);

        return $this->getResponse();
    }

    /**
     * @return \Spiffy\Framework\ApplicationEvent
     */
    public function getEvent()
    {
        if (!$this->event instanceof ApplicationEvent) {
            $this->event = new ApplicationEvent($this);
        }
        return $this->event;
    }

    /**
     * @return \Spiffy\Inject\Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * @return \Spiffy\Framework\ApplicationConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \Spiffy\Package\PackageManager
     */
    public function getPackageManager()
    {
        return $this->getInjector()->nvoke('PackageManager');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->getEvent()->getResponse();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getEvent()->getRequest();
    }

    /**
     * {@inheritDoc}
     */
    protected function attachDefaultPlugins(Manager $events)
    {
        foreach ($this->config->getPlugins() as $index => $plugin) {
            if (is_string($plugin)) {
                $pluginName = $plugin;

                if ($plugin[0] == '?') {
                    if (!$this->isDebug()) {
                        continue;
                    }
                    $plugin = substr($plugin, 1);
                }

                $plugin = InjectorUtils::get($this->injector, $plugin);
            } else if (is_object($plugin)) {
                $pluginName = get_class($plugin);
            } else {
                $pluginName = $index;
            }

            if (null === $plugin) {
                continue;
            }

            if (!$plugin instanceof Plugin) {
                throw new Exception\InvalidPluginException(sprintf(
                    'failed to load plugin "%s": "%s" was invalid - verify plugin and injector configuration',
                    $pluginName,
                    gettype($plugin)
                ));
            }

            $events->plug($plugin);
        }
    }
}
