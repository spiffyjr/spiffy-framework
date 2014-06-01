<?php

namespace Spiffy\Framework;

use Spiffy\Package\Feature\ConfigProvider;

interface ApplicationPackage extends ConfigProvider
{
    /**
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return array
     */
    public function getActions();

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @return array
     */
    public function getServices();
}
