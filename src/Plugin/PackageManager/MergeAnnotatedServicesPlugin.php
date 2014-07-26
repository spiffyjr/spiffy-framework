<?php

namespace Spiffy\Framework\Plugin\PackageManager;

use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\ApplicationPackage;
use Spiffy\Inject\Finder\ComponentFinder;
use Spiffy\Inject\Generator\ArrayGenerator;
use Spiffy\Inject\Metadata\MetadataFactory;
use Spiffy\Package\PackageManager;

final class MergeAnnotatedServicesPlugin implements Plugin
{
    /** @var \Spiffy\Inject\Finder\ComponentFinder */
    private $finder;
    
    public function __construct()
    {
        $this->finder = new ComponentFinder();
    }
    
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(PackageManager::EVENT_MERGE_CONFIG, [$this, 'registerPackagePaths'], 1000);
        $events->on(PackageManager::EVENT_MERGE_CONFIG, [$this, 'mergeServices']);
    }

    /**
     * @param Event $e
     * @return array
     */
    public function registerPackagePaths(Event $e)
    {
        foreach ($e->getTarget()->getPackages() as $package) {
            if (!$package instanceof ApplicationPackage) {
                continue;
            }
            
            if (!$package->isAutoloadServicesEnabled()) {
                continue;
            }

            $this->finder->in($package->getPath());
        }
        
        return [];
    }

    /**
     * @return array
     */
    public function mergeServices()
    {
        try {
            if ($this->finder->count() == 0) {
                return [];
            }
        } catch (\LogicException $ex) {
            return [];
        }
        
        $factory = new MetadataFactory();
        $generator = new ArrayGenerator();
        $config = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($this->finder as $file) {
            $contents = $file->getContents();
            $className = null;
            $namespace = null;

            if (!preg_match('@class\s+(\w+)[^\w]@', $contents, $matches)) {
                continue;
            }
            $className = $matches[1];

            // ROFL @ php and your stupid backslash. I hate you sometimes. :(
            if (!preg_match('@namespace\s+([\\\\\w]+)[\s;]+@', $contents, $matches)) {
                continue;
            }
            $namespace = $matches[1];

            $metadata = $factory->getMetadataForClass($namespace . '\\' . $className);

            $config['framework']['services'][$metadata->getName()] = $generator->generate($metadata);
        }
        
        return $config;
    }
}
