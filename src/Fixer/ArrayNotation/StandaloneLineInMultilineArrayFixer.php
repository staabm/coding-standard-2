<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\LineLength\BreakArrayListFixer;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\TokenRunner\Configuration\Configuration;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

/**
 * @todo consider to make part of
 * @see BreakArrayListFixer
 */
final class StandaloneLineInMultilineArrayFixer implements DefinedFixerInterface
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var bool
     */
    private $isOldArray = false;

    /**
     * @var string
     */
    private $indentWhitespace;

    /**
     * @var string
     */
    private $newlineIndentWhitespace;

    /**
     * @var IndentDetector
     */
    private $indentDetector;

    /**
     * @var string
     */
    private $closingBracketNewlineIndentWhitespace;

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockStartAndEndFinder
     */
    private $blockStartAndEndFinder;

    public function __construct(
        ArrayWrapperFactory $arrayWrapperFactory,
        TokenSkipper $tokenSkipper,
        IndentDetector $indentDetector,
        Configuration $configuration,
        LineLengthTransformer $lineLengthTransformer,
        BlockStartAndEndFinder $blockStartAndEndFinder
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->tokenSkipper = $tokenSkipper;
        $this->indentDetector = $indentDetector;
        $this->configuration = $configuration;
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockStartAndEndFinder = $blockStartAndEndFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Indexed PHP arrays with 2 and more items should have 1 item per line.',
            [
                new CodeSample('<?php $values = [1 => \'hey\', 2 => \'hello\'];'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isAllTokenKindsFound([T_DOUBLE_ARROW, ',']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByBlockStart($tokens, $index);

            $arrayWrapper = $this->arrayWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            $this->isOldArray = $arrayWrapper->isOldArray();

            if (! $arrayWrapper->isAssociativeArray()) {
                continue;
            }

            $this->fixArray($tokens, $arrayWrapper, $blockStartAndEndInfo);
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function fixArray(
        Tokens $tokens,
        ArrayWrapper $arrayWrapper,
        BlockStartAndEndInfo $blockStartAndEndInfo
    ): void {
        $itemCount = $arrayWrapper->getItemCount();
        if ($itemCount <= 1) {
            return;
        }

        $this->lineLengthTransformer->prepareIndentWhitespaces($tokens, $arrayWrapper->getStartIndex());
        $this->prepareIndentWhitespaces($tokens, $arrayWrapper->getStartIndex());

        $arrayStart = $blockStartAndEndInfo->getStart();
        $arrayEnd = $blockStartAndEndInfo->getEnd();

        for ($i = $arrayEnd - 1; $i >= $arrayStart; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            $token = $tokens[$i];

            if (! $token->equals(',')) { // item separator behind it
                continue;
            }

            $nextToken = $tokens[$i + 1];

            $nextNextToken = $tokens[$i + 2];
            // if next token is just space, turn it to newline
            if ($nextToken->isWhitespace(' ') && ! $nextNextToken->isComment()) {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
                ++$i;
            }
        }

        $this->insertNewlineBeforeClosingIfNeeded($tokens, $arrayWrapper->getEndIndex());
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $arrayWrapper->getStartIndex());
    }

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        $offset = $this->isOldArray ? 1 : 0;
        if ($tokens[$arrayStartIndex + $offset + 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayStartIndex + $offset, 1, $this->newlineIndentWhitespace);
    }

    private function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, int $arrayEndIndex): void
    {
        if ($tokens[$arrayEndIndex - 1]->isGivenKind(T_WHITESPACE)) {
            $tokens[$arrayEndIndex - 1] = new Token([T_WHITESPACE, $this->closingBracketNewlineIndentWhitespace]);
        } else {
            $tokens->ensureWhitespaceAtIndex($arrayEndIndex, 0, $this->closingBracketNewlineIndentWhitespace);
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex, $this->configuration);

        $this->indentWhitespace = str_repeat($this->configuration->getIndent(), $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $this->configuration->getLineEnding() . str_repeat(
            $this->configuration->getIndent(),
            $indentLevel
        );
        $this->newlineIndentWhitespace = $this->configuration->getLineEnding() . $this->indentWhitespace;
    }
}
