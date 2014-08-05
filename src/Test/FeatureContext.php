<?php

namespace Spiffy\Framework\Test;

use Behat\Behat\Context\Context;

class FeatureContext implements Context
{
    use FunctionalTestTrait;

    /** @var \Spiffy\Framework\Test\TestClient */
    private $client;
    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $crawler;

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
        return $this->client->getResponse()->getStatusCode() == $statusCode;
    }

    /**
     * @return TestClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }
}
