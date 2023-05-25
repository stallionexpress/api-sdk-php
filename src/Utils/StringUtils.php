<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Utils;

class StringUtils
{
    /**
     * Transforms a string in snake_case to camelCase.
     */
    public static function snakeToCamelCase(string $snakeCase): string
    {
        return lcfirst(self::snakeToPascalCase($snakeCase));
    }

    /**
     * Transforms a string from snake_case to PascalCase.
     */
    public static function snakeToPascalCase(string $snakeCase): string
    {
        return str_replace('_', '', ucwords($snakeCase, '_'));
    }

    /**
     * Transforms a string from camelCase to snake_case.
     */
    public static function camelToSnakeCase(string $camelCase): string
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $camelCase));
    }
}
