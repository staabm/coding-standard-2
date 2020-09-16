<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMultiArrayAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoMultiArrayAssignRule;

final class NoMultiArrayAssignRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MultiArrayAssign.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MultiSingleNestedArrayAssign.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MultiArrayAssignWithVariableDim.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipDifferntArrayAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyDimAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoMultiArrayAssignRule();
    }
}
