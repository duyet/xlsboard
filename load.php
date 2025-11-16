<?php

declare(strict_types=1);

/**
 * Load spreadsheet data
 *
 * This file maintains backward compatibility while using the new architecture
 */

require_once __DIR__ . '/bootstrap.php';

use Xlsboard\Cache;
use Xlsboard\SpreadsheetLoader;

// Get spreadsheet key from file or environment
$defaultSpreadSheet = env('XLSBOARD_DEFAULT_SPREADSHEET', '1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE');
$spreadsheetKey = loadSpreadSheetKey($defaultSpreadSheet);

// Initialize loader with caching
$cacheTtl = (int) env('XLSBOARD_CACHE_TTL', '300');
$loader = new SpreadsheetLoader(new Cache(), $cacheTtl);

// Load spreadsheet data
$errorMessage = '';
$finalData = [];
$maxRow = 0;
$maxCol = 'A';
$tableColumnWidth = 100;

try {
    $finalData = $loader->load($spreadsheetKey);
    $maxRow = $loader->getMaxRow($finalData);
    $maxCol = $loader->getMaxColumn($finalData);

    $numOfCol = $loader->getColumnCount($maxCol);
    if ($numOfCol > 0) {
        $tableColumnWidth = 100 / $numOfCol;
    }
} catch (\Exception $e) {
    $errorMessage = $e->getMessage();

    if (env('APP_DEBUG', 'false') === 'true') {
        $errorMessage .= "\n\nDebug info:\n" . $e->getTraceAsString();
    }
}

// Backward compatibility: Keep old function for reading spreadsheet key
function loadSpreadSheetKey(string $default = ''): string
{
    $file = __DIR__ . '/data.txt';

    if (file_exists($file)) {
        $fileData = file_get_contents($file);
        if (!empty($fileData)) {
            return trim($fileData);
        }
    }

    return $default;
}
