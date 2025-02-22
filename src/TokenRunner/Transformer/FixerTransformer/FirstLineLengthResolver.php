<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObjectFactory\LineLengthAndPositionFactory;

final class FirstLineLengthResolver
{
    public function __construct(
        private readonly LineLengthAndPositionFactory $lineLengthAndPositionFactory
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function resolveFromTokensAndStartPosition(Tokens $tokens, BlockInfo $blockInfo): int
    {
        // compute from here to start of line
        $currentPosition = $blockInfo->getStart();

        // collect length of tokens on current line which precede token at $currentPosition
        $lineLengthAndPosition = $this->lineLengthAndPositionFactory->createFromTokensAndLineStartPosition(
            $tokens,
            $currentPosition
        );
        $lineLength = $lineLengthAndPosition->getLineLength();
        $currentPosition = $lineLengthAndPosition->getCurrentPosition();

        /** @var Token $currentToken */
        $currentToken = $tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count line feeds as characters
        $endOfLineCount = substr_count($currentToken->getContent(), "\n");
        $lineLength -= $endOfLineCount;

        // compute from here to end of line
        $currentPosition = $blockInfo->getStart() + 1;

        // collect length of tokens on current line which follow token at $currentPosition
        while (! $this->isEndOFArgumentsLine($tokens, $currentPosition)) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$currentPosition];

            // in case of multiline string, we are interested in length of the part on current line only
            $explode = explode("\n", $currentToken->getContent(), 2);
            // string follows current token, so we are interested in beginning only
            $lineLength += strlen($explode[0]);

            ++$currentPosition;

            if (count($explode) > 1) {
                // no longer need to continue searching for end of arguments
                break;
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $lineLength;
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function isEndOFArgumentsLine(Tokens $tokens, int $position): bool
    {
        if (! isset($tokens[$position])) {
            throw new TokenNotFoundException($position);
        }

        if (\str_starts_with($tokens[$position]->getContent(), "\n")) {
            return true;
        }

        return $tokens[$position]->isGivenKind(CT::T_USE_LAMBDA);
    }
}
