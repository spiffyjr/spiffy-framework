<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\ApplicationPackage;
use Spiffy\Framework\View\ViewManager;
use Spiffy\Inject\InjectorUtils;
use Spiffy\Package\PackageManager;
use Spiffy\Route\Router;
use Spiffy\View\ViewStrategy;

final class BootstrapPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'injectEnvironment'], 1000);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'createPackageManager'], 900);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'injectApplicationPackageConfigs'], 800);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'injectServices'], 600);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'injectPlugins'], 500);

        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'createDispatcher']);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'createRouter']);
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'createViewManager']);

        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'bootstrapApplicationPackages'], -1000);
    }

    /**
     * @param ApplicationEvent $e
     */
    public function injectEnvironment(ApplicationEvent $e)
    {
        $config = $e->getApplication()->getConfig();

        foreach ($config->getEnvironment() as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    /**
     * @param ApplicationEvent $e
     */
    public function injectPlugins(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        foreach ($i['framework']['plugins'] as $plugin) {
            if (empty($plugin)) {
                continue;
            }
            $app->events()->plug(InjectorUtils::get($i, $plugin));
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function injectServices(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();
        
        foreach ($i['framework']['services'] as $name => $spec) {
            if (empty($spec)) {
                continue;
            }

            $i->nject($name, $spec);
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function injectApplicationPackageConfigs(ApplicationEvent $e)
    {
        $i = $e->getApplication()->getInjector();
        $pm = $i->nvoke('PackageManager');
        $pmConfig = $pm->getMergedConfig();

        foreach ($pm->getPackages() as $package) {
            if ($package instanceof ApplicationPackage && isset($pmConfig[$package->getName()])) {
                $i[$package->getName()] = $pmConfig[$package->getName()];
            }
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function bootstrapApplicationPackages(ApplicationEvent $e)
    {
        $pm = $e->getApplication()->getInjector()->nvoke('PackageManager');

        foreach ($pm->getPackages() as $package) {
            if ($package instanceof ApplicationPackage) {
                $package->bootstrap($e->getApplication());
            }
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     * @throws Exception\InvalidFallbackStrategy
     */
    public function createViewManager(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        $config = $i['framework']['view_manager'];
        $fallback = InjectorUtils::get($i, $config['fallback_strategy']);

        if (!$fallback instanceof ViewStrategy) {
            throw new Exception\InvalidFallbackStrategy(sprintf(
                'Invalid or missing fallback strategy: %s given',
                gettype($fallback)
            ));
        }

        $vm = new ViewManager($fallback);

        foreach ($config['strategies'] as $strategy) {
            if (empty($strategy)) {
                continue;
            }

            $vm->addStrategy(InjectorUtils::get($i, $strategy));
        }

        $vm->setErrorTemplate($config['error_template']);
        $vm->setNotFoundTemplate($config['not_found_template']);

        $app->events()->plug($vm);

        $i->nject('ViewManager', $vm);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function createPackageManager(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();
        $appConfig = $app->getConfig();

        $pm = new PackageManager(
            $appConfig->getConfigOverridePattern(),
            $appConfig->getConfigOverrideFlags(),
            $appConfig->getPackageConfigCache()
        );
        
        foreach ($appConfig->getPackageManagerPlugins() as $plugin) {
            $pm->events()->plug(new $plugin());
        }

        $pm->add('Spiffy\\Framework');
        foreach ($appConfig->getPackages() as $package) {
            if ($package[0] == '?') {
                if (!$app->isDebug()) {
                    continue;
                }
                $package = substr($package, 1);
            }
            $pm->add($package);
        }

        $pm->load();
        $i->nject('PackageManager', $pm);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function createDispatcher(ApplicationEvent $e)
    {
        $r = new Dispatcher();

        $i = $e->getApplication()->getInjector();
        $i->nject('Dispatcher', $r);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function createRouter(ApplicationEvent $e)
    {
        $r = new Router();

        $i = $e->getApplication()->getInjector();
        $i->nject('Router', $r);
    }
}
