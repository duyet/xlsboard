<?php

declare(strict_types=1);

// Redirect to settings page if requested.
if (isset($_GET['e'])) {
    require_once 'm.php';
    exit;
}

// Load spreadsheet data.
require_once 'load.php';

use Xlsboard\Security;

// Ensure variables are defined (they are set in load.php, but PHPStan can't track this).
$maxRow ??= 0;
$maxCol ??= 'A';
$finalData ??= [];
$errorMessage ??= '';

// Get page title.
$titleFile = __DIR__ . '/title.txt';
$pageTitle = env('XLSBOARD_PAGE_TITLE', 'xlsboard');
if (file_exists($titleFile)) {
    $fileTitle = file_get_contents($titleFile);
    if ($fileTitle !== false && !empty(trim($fileTitle))) {
        $pageTitle = trim($fileTitle);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Security::escape($pageTitle); ?></title>
    <meta name="description" content="Google Spreadsheet Display">
    <meta name="keywords" content="spreadsheet, dashboard">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="fullwidth bodycontainer colour1 clearfix" id="topcontainer">
                <h5 class="text-center py-3"><?php echo Security::escape($pageTitle); ?></h5>
            </div>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error loading spreadsheet:</strong>
                    <pre class="mb-0 mt-2"><?php echo Security::escape($errorMessage); ?></pre>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <?php
                        for ($i = 1; $i <= $maxRow; $i++) {
                            echo '<tr>';
                            for ($j = 'A'; $j <= $maxCol; $j++) {
                                $cellValue = isset($finalData["$j$i"]) ? Security::escape($finalData["$j$i"]) : '&nbsp;';
                                echo '<td>' . $cellValue . '</td>';
                            }
                            echo '</tr>';
                        }
        ?>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="text-center py-3">
                <p class="text-muted">
                    <small>&copy; <?php echo date('Y'); ?> xlsboard</small>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>
