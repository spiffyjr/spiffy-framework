<?php

namespace Spiffy\Framework;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

abstract class AbstractPackage implements ApplicationPackage
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $path;

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrapConsole(ConsoleApplication $console)
    {
        $consoleDir = realpath($this->getPath() . '/Console');
        if (!$consoleDir) {
            return;
        }

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*Command.php')
            ->in($consoleDir);

        foreach ($finder as $file) {
            $classes = get_declared_classes();
            include_once $file;
            $newClasses = get_declared_classes();

            foreach (array_diff($newClasses, $classes) as $className) {
                if ($className == 'Spiffy\Mvc\ConsoleCommand') {
                    continue;
                }

                $refl = new \ReflectionClass($className);
                if ($refl->isAbstract()) {
                    continue;
                }
                $command = $refl->newInstance();
                if (!$command instanceof Command) {
                    continue;
                }
                $console->add(new $command);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    final public function getName()
    {
        if ($this->name) {
            return $this->name;
        }

        $replace = function ($match) {
            return $match[1] . '-' . $match[2];
        };

        $name = preg_replace('@Package$@', '', $this->getNamespace());
        $name = str_replace('\\', '.', $name);
        $name = preg_replace_callback('@([a-z])([A-Z])@', $replace, $name);
        $name = strtolower($name);

        if (strstr($name, '.')) {
            $this->name = substr($name, strpos($name, '.') + 1);
        } else {
            $this->name = $name;
        }

        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    final public function getNamespace()
    {
        if ($this->namespace) {
            return $this->namespace;
        }
        $class = get_class($this);
        $this->namespace = substr($class, 0, strrpos($class, '\\'));

        return $this->namespace;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        if ($this->path) {
            return $this->path;
        }

        $refl = new \ReflectionObject($this);
        $this->path = dirname($refl->getFileName());
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions()
    {
        $path = $this->getPath();
        return file_exists($path . '/../config/actions.php') ? include $path . '/../config/actions.php' : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        $path = $this->getPath();
        return file_exists($path . '/../config/package.php') ? include $path . '/../config/package.php' : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes()
    {
        $path = $this->getPath();
        return file_exists($path . '/../config/routes.php') ? include $path . '/../config/routes.php' : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getServices()
    {
        $path = $this->getPath();
        return file_exists($path . '/../config/services.php') ? include $path . '/../config/services.php' : [];
    }
}
