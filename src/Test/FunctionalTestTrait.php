<?php
 
namespace Spiffy\Framework\Test;

use Spiffy\Framework\Application;

trait FunctionalTestTrait
{
    /** @var Application */
    private $app;

    /**
     * @return TestClient
     */
    final public function createClient()
    {
        $client = new TestClient();
        $client->setApplication($this->getApplication());
        
        return $client;
    }

    /**
     * @param array $config
     * @return Application
     */
    final public function getApplication(array $config = [])
    {
        if ($this->app instanceof Application) {
            return $this->app;
        }
        
        $defaults = [
            'environment' => [
                'debug' => true
            ],
            'packages' => []
        ];
                
        $this->app = new Application(array_merge($defaults, $config));
        return $this->app->bootstrap();
    }

    /**
     * @return void
     */
    final public function reset()
    {
        $this->app = null;
    }
}
