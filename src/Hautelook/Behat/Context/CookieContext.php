<?php

namespace Hautelook\Behat\Context;

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\BrowserKit\Cookie;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Ahmed Khanzada <ahmed.khanzada@hautelook.com>
 */
class CookieContext extends RawMinkContext
{
    private function getDriver()
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException('This step is only supported by the BrowserKitDriver');
        }

        return $driver;
    }

    private function getCookie($cookieName)
    {
        $driver = $this->getDriver();
        $client = $driver->getClient();
        $cookie = $client->getCookieJar()->get($cookieName);

        if (null === $cookie) {
            throw new \Exception(sprintf('The cookie "%s" does not exist.', $cookieName));
        }

        return $cookie;
    }
    
    /**
     * @Then /^the "([^"]+)" cookie JSON node ?(.*) should be equal to "([^"]+)"$/
     */
    public function theCookieJsonNodeShouldBeEqualTo($cookieName, $path, $expectedValue)
    {
        $cookie = $this->getCookie($cookieName);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedValue,
            JsonUtil::getArrayPath(json_decode($cookie->getValue(), true), $path)
        );
    }

    /**
     * @Then /^the "([^"]+)" cookie should be equal to "([^"]+)"$/
     */
    public function theCookieShouldBeEqualTo($cookieName, $expectedValue)
    {
        $cookie = $this->getCookie($cookieName);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedValue,
            $cookie->getValue()
        );
    }
}
