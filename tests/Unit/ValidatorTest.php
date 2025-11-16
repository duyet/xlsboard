<?php

declare(strict_types=1);

namespace Xlsboard\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xlsboard\Validator;

class ValidatorTest extends TestCase
{
    public function testIsValidSpreadsheetKey(): void
    {
        // Valid keys
        $this->assertTrue(Validator::isValidSpreadsheetKey('1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE'));
        $this->assertTrue(Validator::isValidSpreadsheetKey('abc123-_XYZ'));

        // Invalid keys
        $this->assertFalse(Validator::isValidSpreadsheetKey(''));
        $this->assertFalse(Validator::isValidSpreadsheetKey('short'));
        $this->assertFalse(Validator::isValidSpreadsheetKey('contains spaces'));
        $this->assertFalse(Validator::isValidSpreadsheetKey('contains@special'));
    }

    public function testIsValidTitle(): void
    {
        // Valid titles
        $this->assertTrue(Validator::isValidTitle('My Dashboard'));
        $this->assertTrue(Validator::isValidTitle('Test'));
        $this->assertTrue(Validator::isValidTitle(str_repeat('a', 200)));

        // Invalid titles
        $this->assertFalse(Validator::isValidTitle(''));
        $this->assertFalse(Validator::isValidTitle('   '));
        $this->assertFalse(Validator::isValidTitle(str_repeat('a', 201)));
    }

    public function testSanitizeSpreadsheetKey(): void
    {
        $this->assertEquals(
            '1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE',
            Validator::sanitizeSpreadsheetKey('1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE')
        );

        $this->assertEquals(
            'abc123-_XYZ',
            Validator::sanitizeSpreadsheetKey('abc123-_XYZ!@#$%')
        );

        $this->assertEquals(
            '',
            Validator::sanitizeSpreadsheetKey('!@#$%^&*()')
        );
    }

    public function testSanitizeTitle(): void
    {
        $this->assertEquals(
            'My Dashboard',
            Validator::sanitizeTitle('My Dashboard')
        );

        $this->assertEquals(
            'Clean Title',
            Validator::sanitizeTitle('<script>Clean Title</script>')
        );

        $this->assertEquals(
            'Trimmed',
            Validator::sanitizeTitle('  Trimmed  ')
        );
    }
}
