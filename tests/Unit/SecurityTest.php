<?php

declare(strict_types=1);

namespace Xlsboard\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xlsboard\Security;

class SecurityTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear session before each test
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testGenerateCsrfToken(): void
    {
        $token1 = Security::generateCsrfToken();
        $this->assertNotEmpty($token1);
        $this->assertEquals(64, strlen($token1)); // 32 bytes = 64 hex chars

        // Same token should be returned on subsequent calls in same session
        $token2 = Security::generateCsrfToken();
        $this->assertEquals($token1, $token2);
    }

    public function testVerifyCsrfToken(): void
    {
        $token = Security::generateCsrfToken();

        // Valid token
        $this->assertTrue(Security::verifyCsrfToken($token));

        // Invalid token
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

        $this->assertEquals(
            '&amp;&lt;&gt;&quot;&#039;',
            Security::escape('&<>"\'')
        );
    }

    public function testAuthentication(): void
    {
        // Set password via environment
        putenv('XLSBOARD_ADMIN_PASSWORD=test123');

        // Should not be authenticated initially
        $this->assertFalse(Security::isAuthenticated());

        // Wrong password
        $this->assertFalse(Security::authenticate('wrong_password'));
        $this->assertFalse(Security::isAuthenticated());

        // Correct password
        $this->assertTrue(Security::authenticate('test123'));
        $this->assertTrue(Security::isAuthenticated());

        // Logout
        Security::logout();
        $this->assertFalse(Security::isAuthenticated());

        // Clean up
        putenv('XLSBOARD_ADMIN_PASSWORD');
    }
}
