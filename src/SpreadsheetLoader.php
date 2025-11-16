<?php

declare(strict_types=1);

namespace Xlsboard;

use SimpleXMLElement;

/**
 * Google Spreadsheet Loader
 *
 * Fetches and parses data from Google Spreadsheets public XML feed.
 */
class SpreadsheetLoader
{
    private const GOOGLE_SHEETS_URL = 'https://spreadsheets.google.com/feeds/cells/%s/%d/public/values';

    private Cache $cache;
    private int $cacheTtl;

    public function __construct(?Cache $cache = null, int $cacheTtl = 300)
    {
        $this->cache = $cache ?? new Cache();
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * Load spreadsheet data by key
     *
     * @param string $key Spreadsheet key
     * @param int $sheetId Sheet ID (default: 1)
     * @return array<string, string> Cell data
     * @throws \RuntimeException
     */
    public function load(string $key, int $sheetId = 1): array
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Spreadsheet key cannot be empty');
        }

        // Try cache first
        $cacheKey = "sheet_{$key}_{$sheetId}";
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch from Google
        $data = $this->fetchFromGoogle($key, $sheetId);

        // Parse into cell array
        $cellData = $this->parseData($data);

        // Cache the result
        $this->cache->set($cacheKey, $cellData, $this->cacheTtl);

        return $cellData;
    }

    /**
     * Fetch spreadsheet XML from Google
     *
     * @param string $key Spreadsheet key
     * @param int $sheetId Sheet ID
     * @return SimpleXMLElement
     * @throws \RuntimeException
     */
    private function fetchFromGoogle(string $key, int $sheetId): SimpleXMLElement
    {
        if (!function_exists('simplexml_load_file')) {
            throw new \RuntimeException('SimpleXML extension is not available');
        }

        $url = sprintf(self::GOOGLE_SHEETS_URL, $key, $sheetId);

        // Suppress warnings and capture errors
        $previousErrorHandler = set_error_handler(function () {});
        $xml = simplexml_load_file($url);
        restore_error_handler();

        if ($xml === false) {
            throw new \RuntimeException(
                "Failed to load spreadsheet from Google. " .
                "Please check the spreadsheet key and ensure it's published to the web."
            );
        }

        return $xml;
    }

    /**
     * Parse XML data into cell array
     *
     * @param SimpleXMLElement $data XML data
     * @return array<string, string>
     */
    private function parseData(SimpleXMLElement $data): array
    {
        $cellData = [];

        foreach ($data as $entry) {
            if (isset($entry->title, $entry->content)) {
                $cell = (string) $entry->title;
                $value = (string) $entry->content;
                $cellData[$cell] = $value;
            }
        }

        return $cellData;
    }

    /**
     * Get maximum row number from cell data
     *
     * @param array<string, string> $cellData
     * @return int
     */
    public function getMaxRow(array $cellData): int
    {
        if (empty($cellData)) {
            return 0;
        }

        $maxRow = 0;
        foreach (array_keys($cellData) as $cell) {
            $rowNum = (int) substr((string) $cell, 1);
            if ($rowNum > $maxRow) {
                $maxRow = $rowNum;
            }
        }

        return $maxRow;
    }

    /**
     * Get maximum column letter from cell data
     *
     * @param array<string, string> $cellData
     * @return string
     */
    public function getMaxColumn(array $cellData): string
    {
        if (empty($cellData)) {
            return 'A';
        }

        $maxCol = 'A';
        foreach (array_keys($cellData) as $cell) {
            $colChar = substr((string) $cell, 0, 1);
            if (!empty($colChar) && $colChar > $maxCol) {
                $maxCol = $colChar;
            }
        }

        return $maxCol;
    }

    /**
     * Calculate number of columns
     *
     * @param string $maxCol Maximum column letter
     * @return int
     */
    public function getColumnCount(string $maxCol): int
    {
        $count = 0;
        for ($i = 'A'; $i <= $maxCol; $i++) {
            $count++;
        }

        return $count;
    }
}
