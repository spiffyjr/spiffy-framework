<?php

namespace Spiffy\Framework;

final class ApplicationConfig
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $defaults = [
            'config_override_flags' => 0,
            'config_override_pattern' => 'config/override/*.config.php',
            'environment' => ['debug' => false],
            'packages' => [],
            'package_config_cache' => null,
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
    public function getConfigOverrideFlags()
    {
        return $this->config['config_override_flags'];
    }


    /**
     * @return string
     */
    public function getConfigOverridePattern()
    {
        return $this->config['config_override_pattern'];
    }

    /**
     * @return array
     */
    public function getEnvironment()
    {
        return $this->config['environment'];
    }

    /**
     * @return string
     */
    public function getPackageConfigCache()
    {
        return $this->config['package_config_cache'];
    }

    /**
     * @return array
     */
    public function getPackages()
    {
        return $this->config['packages'];
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->config['plugins'];
    }

    /**
     * @return array
     */
    public function isDebug()
    {
        return $this->config['environment']['debug'];
    }
}
