<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\View\ViewManager;
use Spiffy\Package\PackageManager;
use Spiffy\Route\Router;
use Spiffy\View\VardumpStrategy;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Spiffy\Framework\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Spiffy\Framework\ApplicationEvent
     */
    protected $event;

    /**
     * @var \Spiffy\Event\Plugin
     */
    protected $p;

    /**
     * @return \Spiffy\Event\Plugin
     */
    abstract protected function createPlugin();

    protected function setUp()
    {
        $this->config = $config = ['environment' => ['foo' => 'bar']];

        $pm = new PackageManager();
        $pm->add('Spiffy\Framework\TestAsset');
        $pm->load();

        $this->app = $app = new Application($config);
        $i = $app->getInjector();
        $i->nject('PackageManager', $pm);

        $i['framework'] = [
            'actions' => [
                'test' => 'Spiffy\Framework\TestAsset\TestAction',
            ],
            'plugins' => [
                'Spiffy\Framework\TestAsset\TestPlugin',
                '',
                null
            ],
            'services' => [
                'stdclass' => 'StdClass',
                '',
                null
            ],
            'view_manager' => [
                'error_template' => '',
                'fallback_strategy' => 'Spiffy\View\VardumpStrategy',
                'not_found_template' => '',
                'strategies' => [
                    'Spiffy\View\VardumpStrategy',
                    '',
                    null
                ],
            ]
        ];

        $this->event = $app->getEvent();
        $this->p = $this->createPlugin();
    }
}
