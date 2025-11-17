<?php

declare(strict_types=1);

namespace Xlsboard;

/**
 * Input validation
 */
class Validator
{
    /**
     * Validate Google Spreadsheet key format
     *
     * @param string $key Spreadsheet key to validate
     * @return bool
     */
    public static function isValidSpreadsheetKey(string $key): bool
    {
        // Google spreadsheet keys are typically 44 characters, alphanumeric with dashes and underscores
        // Example: 1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE
        return preg_match('/^[a-zA-Z0-9_-]{20,100}$/', $key) === 1;
    }

    /**
     * Validate page title
     *
     * @param string $title Title to validate
     * @return bool
     */
    public static function isValidTitle(string $title): bool
    {
        // Title should be reasonable length and not contain HTML tags
        $cleaned = trim(strip_tags($title));

        return strlen($cleaned) > 0 && strlen($cleaned) <= 200;
    }

    /**
     * Sanitize spreadsheet key
     *
     * @param string $key Key to sanitize
     * @return string
     */
    public static function sanitizeSpreadsheetKey(string $key): string
    {
        // Remove any characters that aren't alphanumeric, dash, or underscore
        $result = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

        return $result !== null ? $result : '';
    }

    /**
     * Sanitize title
     *
     * @param string $title Title to sanitize
     * @return string
     */
    public static function sanitizeTitle(string $title): string
    {
        // Remove HTML tags and trim
        return trim(strip_tags($title));
    }
}
