<?php

namespace Spiffy\Framework\Plugin\PackageManager;

use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\ApplicationPackage;
use Spiffy\Package\PackageManager;

final class MergeApplicationConfigPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        // custom merging of our actions, routes, and services.
        $events->on(PackageManager::EVENT_MERGE_CONFIG, [$this, 'mergeApplicationPackageConfig']);
    }

    /**
     * @param Event $e
     * @return array
     */
    public function mergeApplicationPackageConfig(Event $e)
    {
        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $e->getTarget();

        $config = [
            'framework' => [
                'routes' => [],
                'services' => [],
            ]
        ];
        foreach ($pm->getPackages() as $package) {
            if (!$package instanceof ApplicationPackage) {
                continue;
            }

            $config['framework']['routes'] = $pm->merge($config['framework']['routes'], $package->getRoutes());
            $config['framework']['services'] = $pm->merge($config['framework']['services'], $package->getServices());
        }

        return $config;
    }
}
