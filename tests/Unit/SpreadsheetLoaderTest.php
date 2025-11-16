<?php

declare(strict_types=1);

namespace Xlsboard\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xlsboard\Cache;
use Xlsboard\SpreadsheetLoader;

class SpreadsheetLoaderTest extends TestCase
{
    public function testGetMaxRow(): void
    {
        $loader = new SpreadsheetLoader(new Cache());

        $cellData = [
            'A1' => 'Header 1',
            'B1' => 'Header 2',
            'A2' => 'Row 2',
            'B2' => 'Row 2',
            'A10' => 'Row 10',
        ];

        $this->assertEquals(10, $loader->getMaxRow($cellData));
    }

    public function testGetMaxRowEmpty(): void
    {
        $loader = new SpreadsheetLoader(new Cache());
        $this->assertEquals(0, $loader->getMaxRow([]));
    }

    public function testGetMaxColumn(): void
    {
        $loader = new SpreadsheetLoader(new Cache());

        $cellData = [
            'A1' => 'Col A',
            'B1' => 'Col B',
            'C1' => 'Col C',
            'Z1' => 'Col Z',
        ];

        $this->assertEquals('Z', $loader->getMaxColumn($cellData));
    }

    public function testGetMaxColumnEmpty(): void
    {
        $loader = new SpreadsheetLoader(new Cache());
        $this->assertEquals('A', $loader->getMaxColumn([]));
    }

    public function testGetColumnCount(): void
    {
        $loader = new SpreadsheetLoader(new Cache());

        $this->assertEquals(1, $loader->getColumnCount('A'));
        $this->assertEquals(2, $loader->getColumnCount('B'));
        $this->assertEquals(3, $loader->getColumnCount('C'));
        $this->assertEquals(26, $loader->getColumnCount('Z'));
    }

    public function testLoadWithInvalidKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $loader = new SpreadsheetLoader(new Cache());
        $loader->load('');
    }
}
