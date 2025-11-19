<?php

declare(strict_types=1);

namespace Xlsboard\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xlsboard\Security;

class SecurityTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear session before each test.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    /**
     * @runInSeparateProcess
     */
    public function testGenerateCsrfToken(): void
    {
        $token1 = Security::generateCsrfToken();
        $this->assertNotEmpty($token1);
        $this->assertEquals(64, strlen($token1)); // 32 bytes = 64 hex chars.

        // Same token should be returned on subsequent calls in same session.
        $token2 = Security::generateCsrfToken();
        $this->assertEquals($token1, $token2);
    }

    /**
     * @runInSeparateProcess
     */
    public function testVerifyCsrfToken(): void
    {
        $token = Security::generateCsrfToken();

        // Valid token.
        $this->assertTrue(Security::verifyCsrfToken($token));

        // Invalid token.
        $this->assertFalse(Security::verifyCsrfToken('invalid_token'));
    }

    public function testEscape(): void
    {
        $this->assertEquals(
            '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
            Security::escape('<script>alert("xss")</script>')
        );

        $this->assertEquals(
            'Normal text',
            Security::escape('Normal text')
        );

        // Note: htmlspecialchars with ENT_HTML5 uses &apos; for single quote.
        $escaped = Security::escape('&<>"\'');
        $this->assertStringContainsString('&amp;', $escaped);
        $this->assertStringContainsString('&lt;', $escaped);
        $this->assertStringContainsString('&gt;', $escaped);
        $this->assertStringContainsString('&quot;', $escaped);
        // Single quote can be &#039; or &apos; depending on PHP version.
        $this->assertTrue(
            strpos($escaped, '&#039;') !== false || strpos($escaped, '&apos;') !== false,
            'Single quote should be escaped'
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testAuthentication(): void
    {
        // Set password via environment.
        putenv('XLSBOARD_ADMIN_PASSWORD=test123');

        // Should not be authenticated initially.
        $this->assertFalse(Security::isAuthenticated());

        // Wrong password.
        $this->assertFalse(Security::authenticate('wrong_password'));
        $this->assertFalse(Security::isAuthenticated());

        // Correct password.
        $this->assertTrue(Security::authenticate('test123'));
        $this->assertTrue(Security::isAuthenticated());

        // Logout.
        Security::logout();
        $this->assertFalse(Security::isAuthenticated());

        // Clean up.
        putenv('XLSBOARD_ADMIN_PASSWORD');
    }
}
