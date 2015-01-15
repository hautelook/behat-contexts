<?php

namespace Hautelook\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonContext extends RawMinkContext
{
    /**
     * @Then /^the response should be in JSON/
     */
    public function theResponseShouldBeInJSON()
    {
        JsonUtil::getJson($this);
    }

    /**
     * @Then /^the JSON node ?(.*) should exist$/
     */
    public function theNodeXShouldExist($path)
    {
        JsonUtil::getJsonPath($this, $path);
    }

    /**
     * @Then /^the JSON node ?(.*) should be (true|false)$/
     */
    public function theNodeXShouldBeBoolean($path, $expectedValue)
    {
        $expectedValue = filter_var($expectedValue, FILTER_VALIDATE_BOOLEAN);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedValue,
            JsonUtil::getJsonPath($this, $path)
        );
    }

    /**
     * @Then /^the JSON node ?(.*) should be equal to "([^"]+)"$/
     */
    public function theNodeXShouldBeEqualToX($path, $expectedValue)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedValue,
            JsonUtil::getJsonPath($this, $path)
        );
    }

    /**
     * @Then /^the JSON node ?(.*) should contain "([^"]+)"$/
     */
    public function theNodeXShouldContainX($path, $expectedValue)
    {
        \PHPUnit_Framework_Assert::assertContains(
            $expectedValue,
            JsonUtil::getJsonPath($this, $path)
        );
    }

    /**
     * @Then /^the JSON node ?(.*) should not contain "([^"]+)"$/
     */
    public function theNodeXShouldNotContainX($path, $expectedValue)
    {
        \PHPUnit_Framework_Assert::assertNotContains(
            $expectedValue,
            JsonUtil::getJsonPath($this, $path)
        );
    }

    /**
     * @Then /^the JSON node ?(.*) should contain (\d+) elements?$/
     */
    public function theNodeXShouldContainXElements($path, $expectedValue)
    {
        \PHPUnit_Framework_Assert::assertCount(
            (integer) $expectedValue,
            JsonUtil::getJsonPath($this, $path)
        );
    }

    /**
     * @Then /^the JSON node ?(.*) should contain the following objects:$/
     */
    public function theNodeXShouldContainTheFollowingObjects($path, TableNode $table)
    {
        $firstNode = JsonUtil::getJsonPath($this, $path);

        foreach ($table->getHash() as $properties) {
            foreach ($firstNode as $childNode) {
                foreach ($properties as $propertyPath => $expectedValue) {
                    if ($expectedValue != JsonUtil::getArrayPath($childNode, $propertyPath)) {
                        continue 2;
                    }
                }

                continue 2;
            }

            throw new \Exception(
                sprintf(
                    'The JSON node "%s" does not contain the following object: %s.',
                    $path,
                    json_encode($properties)
                )
            );
        }
    }

    /**
     * @Then /^the JSON node ?(.*) should not contain the following objects:$/
     */
    public function theNodeXShouldNotContainTheFollowingObjects($path, TableNode $table)
    {
        $firstNode = JsonUtil::getJsonPath($this, $path);

        foreach ($table->getHash() as $properties) {
            foreach ($firstNode as $childNode) {
                foreach ($properties as $propertyPath => $expectedValue) {
                    if ($expectedValue != JsonUtil::getArrayPath($childNode, $propertyPath)) {
                        continue 2;
                    }
                }

                throw new \Exception(
                    sprintf(
                        'The JSON node "%s" contains the following object: %s.',
                        $path,
                        json_encode($properties)
                    )
                );
            }
        }
    }

    /**
     * @Then /^the JSON node ?(.*) should contain the following ordered objects:$/
     */
    public function theNodeXShouldContainTheFollowingOrderedObjects($path, TableNode $table)
    {
        $firstNode = JsonUtil::getJsonPath($this, $path);

        foreach ($table->getHash() as $index => $properties) {
            $node = $firstNode[$index];

            foreach ($properties as $propertyPath => $expectedValue) {
                if ($expectedValue != JsonUtil::getArrayPath($node, $propertyPath)) {
                    throw new \Exception(
                        sprintf(
                            'The JSON node "%s[\'%d\']" does not contain the following object: %s.',
                            $path,
                            $index,
                            json_encode($properties)
                        )
                    );
                }
            }
        }
    }

    /**
     * @Then /^the JSON node ?(.*) should match:$/
     */
    public function theNodeXShouldMatch($path, TableNode $table)
    {
        $node = JsonUtil::getJsonPath($this, $path);

        $properties = $table->getHash()[0];
        foreach ($properties as $propertyPath => $expectedValue) {
            if ($expectedValue != JsonUtil::getArrayPath($node, $propertyPath)) {
                throw new \Exception(
                    sprintf(
                        'The JSON node "%s" does not contain the following object: %s.',
                        $path,
                        json_encode($properties)
                    )
                );
            }
        }
    }
}
