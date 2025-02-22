<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;

final class StandaloneLineInMultilineArrayFixerTestPhp80Test extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideData(): Iterator
    {
        return self::yieldFiles(__DIR__ . '/FixturePhp80');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
