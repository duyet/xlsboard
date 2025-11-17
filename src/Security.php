<?php

declare(strict_types=1);

namespace Xlsboard;

/**
 * Security utilities
 */
class Security
{
    private const SESSION_TOKEN_KEY = 'xlsboard_csrf_token';

    /**
     * Start session if not already started
     */
    public static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Generate CSRF token
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_TOKEN_KEY])) {
            $_SESSION[self::SESSION_TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_TOKEN_KEY];
    }

    /**
     * Verify CSRF token
     *
     * @param string $token Token to verify
     * @return bool
     */
    public static function verifyCsrfToken(string $token): bool
    {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_TOKEN_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_TOKEN_KEY], $token);
    }

    /**
     * Escape HTML output
     *
     * @param string $value Value to escape
     * @return string
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        self::ensureSession();

        return isset($_SESSION['xlsboard_authenticated']) && $_SESSION['xlsboard_authenticated'] === true;
    }

    /**
     * Authenticate user
     *
     * @param string $password Password to check
     * @return bool
     */
    public static function authenticate(string $password): bool
    {
        $envPassword = getenv('XLSBOARD_ADMIN_PASSWORD');

        // If no password set in env, use default (insecure!).
        if ($envPassword === false || $envPassword === '') {
            $envPassword = 'admin'; // Default password for backwards compatibility
        }

        if ($password === $envPassword) {
            self::ensureSession();
            $_SESSION['xlsboard_authenticated'] = true;

            return true;
        }

        return false;
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::ensureSession();
        unset($_SESSION['xlsboard_authenticated']);
        session_destroy();
    }

    /**
     * Require authentication or redirect
     *
     * @param string $redirectUrl URL to redirect to if not authenticated
     */
    public static function requireAuth(string $redirectUrl = '/'): void
    {
        if (!self::isAuthenticated()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}
