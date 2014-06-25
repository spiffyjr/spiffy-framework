<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Package\PackageManager;

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

        $this->event = $event = new ApplicationEvent($app);
        $this->p = $this->createPlugin();
    }
}
