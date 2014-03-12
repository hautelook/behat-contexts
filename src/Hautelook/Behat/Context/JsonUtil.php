<?php

namespace Hautelook\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonUtil
{
    private static $expressionLanguage;

    /**
     * @return ExpressionLanguage
     */
    private static function getExpressionLanguage()
    {
        if (null === self::$expressionLanguage) {
            self::$expressionLanguage = new ExpressionLanguage();
        }

        return self::$expressionLanguage;
    }

    public static function getJson(RawMinkContext $context)
    {
        $content = $context->getSession()->getPage()->getContent();

        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('The response is not in JSON');
        }

        return $result;
    }

    public static function getArrayPath(array $array, $path)
    {
        if (!preg_match('/^\[/', $path)) {
            throw new \InvalidArgumentException(sprintf('The path should start with a "[", the path was "%s"', $path));
        }

        $expression = sprintf('data%s', $path);

        return static::getExpressionLanguage()->evaluate($expression, array('data' => $array));
    }

    public static function getJsonPath(RawMinkContext $context, $path)
    {
        return static::getArrayPath(self::getJson($context), $path);
    }
}
