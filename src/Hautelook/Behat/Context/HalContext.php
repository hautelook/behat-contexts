<?php

namespace Hautelook\Behat\Context;

use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Guzzle\Parser\UriTemplate\UriTemplate;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class HalContext extends RawMinkContext
{
    /**
     * @Then /^there should be a "([^"]+)" HAL link to "([^"]+)"$/
     */
    public function thereShouldBeAXLinkToX($rel, $href)
    {
        return array(
            new Then(sprintf('the JSON node [\'_links\'][\'%s\'][\'href\'] should be equal to "%s"', $rel, $href)),
        );
    }
    /**
     * @Then /^the JSON node (\[.+\]) should have a "([^"]+)" HAL link to "([^"]+)"$/
     */
    public function theNodeXShouldHaveAXHALLinkToX($path, $rel, $href)
    {
        return array(
            new Then(
                sprintf('the JSON node %s[\'_links\'][\'%s\'][\'href\'] should be equal to "%s"', $path, $rel, $href)
            ),
        );
    }

    /**
     * @Then /^there should be a "([^"]+)" HAL link$/
     */
    public function thereShouldBeAXLink($rel)
    {
        return array(
            new Then(sprintf('the JSON node [\'_links\'][\'%s\'][\'href\'] should exist', $rel)),
        );
    }

    /**
     * @Then /^there should be a "([^"]+)" uri template HAL link$/
     */
    public function thereShouldBeAXUriTemplateLink($rel)
    {
        return array(
            new Then(sprintf('the JSON node [\'_links\'][\'%s\'][\'href\'] should exist', $rel)),
            new Then(sprintf('the JSON node [\'_links\'][\'%s\'][\'templated\'] should be true', $rel)),
        );
    }

    /**
     * @Then /^there should be a "([^"]+)" embedded HAL resource$/
     */
    public function thereShouldBeAXHalEmbeddedResource($rel)
    {
        return array(
            new Then(sprintf('the JSON node [\'_embedded\'][\'%s\'] should exist', $rel)),
        );
    }

    /**
     * @When /^I follow the "([^"]+)" HAL link$/
     */
    public function iFollowTheXLink($rel)
    {
        $href = JsonUtil::getJsonPath($this, sprintf('[\'_links\'][\'%s\'][\'href\']', $rel));
        $uriTemplate = new UriTemplate();
        $href = $uriTemplate->expand($href, array()); // in case that's a template, expand with no parameters

        return array(
            new Then(sprintf('I go to "%s"', $href)),
        );
    }

    /**
     * @When /^I follow the "([^"]+)" uri template HAL link with the following parameters:$/
     */
    public function iFollowTheXUriTemplateLinkWithTheFollowingParameters($rel, TableNode $table)
    {
        $parameters = array();
        foreach ($table->getHash() as $rows) {
            foreach ($rows as $key => $value) {
                if (preg_match('/\[\]$/', $key)) {
                    $parameters[$key][] = $value;
                } else {
                    $parameters[$key] = $value;
                }
            }
        }
        $parameters = array_map(
            function ($value) {
                return is_array($value) ? array_filter($value) : $value;
            },
            $parameters
        );
        $parameters = array_combine(
            array_map('urlencode', array_keys($parameters)),
            array_values($parameters)
        );

        $uriTemplateParser = new UriTemplate();
        $href = $uriTemplateParser->expand(
            JsonUtil::getJsonPath($this, sprintf('[\'_links\'][\'%s\'][\'href\']', $rel)),
            $parameters
        );

        return array(
            new When(sprintf('I go to "%s"', $href)),
        );
    }



    /**
     * @When /^I follow the "([^"]+)" HAL link in ([^"]+)$/
     */
    public function iFollowTheXLinkInX($rel, $path)
    {
        $href = JsonUtil::getJsonPath($this, sprintf('%s[\'_links\'][\'%s\'][\'href\']', $path, $rel));

        return array(
            new Then(sprintf('I go to "%s"', $href)),
        );
    }

    /**
     * @When /^I send a (\S+) request to the "([^"]+)" HAL link in ([^"]+)$/
     */
    public function iSendAXRequestToTheXHALLinkInX($method, $rel, $path)
    {
        $href = JsonUtil::getJsonPath($this, sprintf('%s[\'_links\'][\'%s\'][\'href\']', $path, $rel));

        return array(
            new Then(sprintf('I send a %s request on "%s"', $method, $href)),
        );
    }

    /**
     * @When /^I send a (\S+) request to the "([^"]+)" HAL link with json body:$/
     */
    public function iSendAXRequestToTheXHALLinkWithJsonBody($method, $rel, PyStringNode $jsonBody)
    {
        $href = JsonUtil::getJsonPath($this, sprintf('[\'_links\'][\'%s\'][\'href\']', $rel));

        return array(
            new Then(sprintf('I send a %s request on "%s" with json body:', $method, $href), $jsonBody),
        );
    }
}
