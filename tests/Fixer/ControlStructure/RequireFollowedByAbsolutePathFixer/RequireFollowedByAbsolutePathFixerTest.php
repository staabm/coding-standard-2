<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;
use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class RequireFollowedByAbsolutePathFixerTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return $this->container->get(RequireFollowedByAbsolutePathFixer::class);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
