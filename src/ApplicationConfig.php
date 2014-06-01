<?php

namespace Spiffy\Framework;

class ApplicationConfig
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    final public function __construct(array $config = [])
    {
        $defaults = [
            'config_override_flags' => 0,
            'config_override_pattern' => 'config/override/*.config.php',
            'environment' => ['debug' => false],
            'packages' => [],
            'plugins' => [
                'bootstrap' => 'Spiffy\Framework\Plugin\BootstrapPlugin',
                'dispatch' => 'Spiffy\Framework\Plugin\DispatchPlugin',
                'render' => 'Spiffy\Framework\Plugin\RenderPlugin',
                'respond' => 'Spiffy\Framework\Plugin\RespondPlugin',
                'route' => 'Spiffy\Framework\Plugin\RoutePlugin',
            ]
        ];

        $this->config = array_replace_recursive($defaults, $config);
    }

    /**
     * @return int
     */
    final public function getConfigOverrideFlags()
    {
        return $this->config['config_override_flags'];
    }


    /**
     * @return string
     */
    final public function getConfigOverridePattern()
    {
        return $this->config['config_override_pattern'];
    }

    /**
     * @return array
     */
    final public function getEnvironment()
    {
        return $this->config['environment'];
    }

    /**
     * @return array
     */
    final public function getPackages()
    {
        return $this->config['packages'];
    }

    /**
     * @return array
     */
    final public function getPlugins()
    {
        return $this->config['plugins'];
    }

    /**
     * @return array
     */
    final public function isDebug()
    {
        return $this->config['environment']['debug'];
    }
}
