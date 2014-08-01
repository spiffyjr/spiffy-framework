<?php

namespace Spiffy\Framework\Test;

use Behat\Behat\Context\Context;

/**
 * Behat context class.
 */
class FeatureContext implements Context
{
    use FunctionalTestTrait;

    /** @var \Spiffy\Framework\Test\TestClient */
    private $client;
    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $crawler;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @When /^a request to "([^"]*)" is made$/
     */
    public function aRequestToIsMade($route)
    {
        $this->client = $this->createClient();
        $this->crawler = $this->client->request('GET', $route);
    }

    /**
     * @Given /^the route "([^"]*)" for path "([^"]*)" exists with action "([^"]*)"$/
     */
    public function theRouteForPathExistsWithAction($name, $path, $action)
    {
        $app = $this->getApplication();
        /** @var \Spiffy\Route\Router $router */
        $router = $app->getInjector()->nvoke('Router');
        $router->add($name, $path, ['defaults' => ['action' => $action]]);
    }

    /**
     * @Given /^the action "([^"]*)" exists$/
     */
    public function theActionExists($action)
    {
        $app = $this->getApplication();
        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $dispatcher = $app->getInjector()->nvoke('Dispatcher');
        $dispatcher->add($action, function () {
            return new \Spiffy\View\JsonModel();
        });
    }

    /**
     * @Then /^.* a (\d+) (?:status|response) code$/
     */
    public function iShouldGetAResponseCode($statusCode)
    {
        \PHPUnit_Framework_Assert::assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }
}
