<?php

declare(strict_types=1);

/**
 * Settings Management Page
 *
 * Secure configuration interface with authentication, CSRF protection, and input validation
 */

require_once __DIR__ . '/bootstrap.php';

use Xlsboard\Security;
use Xlsboard\Validator;

// Initialize session for security features
Security::ensureSession();

// Handle logout
if (isset($_GET['logout'])) {
    Security::logout();
    header('Location: m.php');
    exit;
}

$error = '';
$success = '';
$showLoginForm = !Security::isAuthenticated();

// Handle login
if ($showLoginForm && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $password = $_POST['password'] ?? '';

    if (Security::authenticate($password)) {
        $showLoginForm = false;
        $success = 'Login successful!';
    } else {
        $error = 'Invalid password. Please try again.';
    }
}

// Handle settings update (only for authenticated users)
if (!$showLoginForm && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Verify CSRF token
    $token = $_POST['csrf_token'] ?? '';

    if (!Security::verifyCsrfToken($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Validate and sanitize inputs
        $spreadsheetKey = $_POST['data'] ?? '';
        $pageTitle = $_POST['page_title'] ?? '';

        $spreadsheetKey = Validator::sanitizeSpreadsheetKey($spreadsheetKey);
        $pageTitle = Validator::sanitizeTitle($pageTitle);

        $errors = [];

        if (!Validator::isValidSpreadsheetKey($spreadsheetKey)) {
            $errors[] = 'Invalid spreadsheet key format. Please check the key and try again.';
        }

        if (!Validator::isValidTitle($pageTitle)) {
            $errors[] = 'Invalid page title. Title must be between 1-200 characters.';
        }

        if (empty($errors)) {
            // Save to files
            $dataFile = __DIR__ . '/data.txt';
            $titleFile = __DIR__ . '/title.txt';

            $dataSaved = file_put_contents($dataFile, $spreadsheetKey) !== false;
            $titleSaved = file_put_contents($titleFile, $pageTitle) !== false;

            if ($dataSaved && $titleSaved) {
                $success = 'Settings saved successfully!';
            } else {
                $error = 'Failed to save settings. Please check file permissions.';
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
}

// Load current settings
$dataFile = __DIR__ . '/data.txt';
$titleFile = __DIR__ . '/title.txt';

$currentData = '';
$currentTitle = '';

if (file_exists($dataFile)) {
    $data = file_get_contents($dataFile);
    $currentData = $data !== false ? $data : '';
}

if (file_exists($titleFile)) {
    $title = file_get_contents($titleFile);
    $currentTitle = $title !== false ? $title : '';
}

// Generate CSRF token
$csrfToken = Security::generateCsrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings - xlsboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
    </style>
</head>
<body>

<div class="settings-container">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">
                <i class="fas fa-cog"></i> xlsboard Settings
            </h3>
        </div>
        <div class="card-body">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo Security::escape($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($showLoginForm): ?>
                <!-- Login Form -->
                <h5 class="mb-3">Please Login</h5>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required autofocus>
                        <div class="form-text">
                            Default password: <code>admin</code> (Change via <code>XLSBOARD_ADMIN_PASSWORD</code> in .env)
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            <?php else: ?>
                <!-- Settings Form -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Configuration</h5>
                    <a href="?logout" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> How to get your spreadsheet key:</h6>
                    <ol class="mb-0">
                        <li>Open your Google Spreadsheet</li>
                        <li>Go to <strong>File</strong> → <strong>Share</strong> → <strong>Publish to web</strong></li>
                        <li>Click <strong>Publish</strong></li>
                        <li>Copy the key from the URL: <code>https://docs.google.com/spreadsheets/d/<strong>YOUR_KEY_HERE</strong>/pubhtml</code></li>
                    </ol>
                </div>

                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::escape($csrfToken); ?>">

                    <div class="mb-3">
                        <label for="data" class="form-label">
                            <i class="fas fa-table"></i> Google Spreadsheet Key
                        </label>
                        <input type="text" class="form-control" id="data" name="data" value="<?php echo Security::escape($currentData); ?>" required placeholder="1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE">
                        <div class="form-text">
                            Example: 1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="page_title" class="form-label">
                            <i class="fas fa-heading"></i> Page Title
                        </label>
                        <input type="text" class="form-control" id="page_title" name="page_title" value="<?php echo Security::escape($currentTitle); ?>" required placeholder="My Dashboard">
                        <div class="form-text">
                            This will be displayed as the page title
                        </div>
                    </div>

                    <button type="submit" name="update_settings" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </form>
            <?php endif; ?>

        </div>
        <div class="card-footer text-muted text-center">
            <small>&copy; <?php echo date('Y'); ?> xlsboard - Secure Settings</small>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>
