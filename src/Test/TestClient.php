<?php
 
namespace Spiffy\Framework\Test;

use Spiffy\Framework\Application;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie as DomCookie;
use Symfony\Component\BrowserKit\Request as DomRequest;
use Symfony\Component\BrowserKit\Response as DomResponse;
use Symfony\Component\HttpFoundation\Request;

class TestClient extends Client
{
    /** @var Application */
    private $app;

    /**
     * @param \Spiffy\Framework\Application $app
     */
    public function setApplication($app)
    {
        $this->app = $app;
    }

    /**
     * @return \Spiffy\Framework\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * {@inheritDoc}
     */
    protected function filterRequest(DomRequest $request)
    {
        $httpRequest = Request::create(
            $request->getUri(),
            $request->getMethod(),
            $request->getParameters(),
            $request->getCookies(),
            $request->getFiles(),
            $request->getServer(),
            $request->getContent()
        );

        return $httpRequest;
    }

    /**
     * {@inheritDoc}
     */
    protected function filterResponse($response)
    {
        $headers = $response->headers->all();
        if ($response->headers->getCookies()) {
            $cookies = array();
            /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
            foreach ($response->headers->getCookies() as $cookie) {
                $cookies[] = new DomCookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            }
            $headers['Set-Cookie'] = $cookies;
        }

        // this is needed to support StreamedResponse
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        return new DomResponse($content, $response->getStatusCode(), $headers);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doRequest($request)
    {
        $this->app->getEvent()->setRequest($request);
        return $this->app->run();
    }
}
