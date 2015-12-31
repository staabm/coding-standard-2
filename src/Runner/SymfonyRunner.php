<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symfony\Component\Process\Process;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

final class SymfonyRunner implements RunnerInterface
{
    /**
     * {@inheritdoc}
     */
    public function runForDirectory($directory)
    {
        $process = new Process(
            sprintf(
                'php vendor/bin/php-cs-fixer fix %sr --dry-run --diff -v --level=symfony',
                $directory
            )
        );
        $process->run();

        return $process->getOutput();
    }
}
