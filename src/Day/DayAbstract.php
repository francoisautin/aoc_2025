<?php

declare(strict_types=1);

namespace App\Day;

use App\Loader\InputLoadException;
use App\Loader\Loader;

abstract class DayAbstract
{
    protected readonly ?string $input;

    abstract public function part1(): string;
    abstract public function part2(): string;

    public function __construct(string $fileName)
    {
        try {
            $this->input = Loader::loadFileContents($fileName);
        } catch (InputLoadException) {
            $this->input = null;
        }
    }

    final public function getResults(): string
    {
        if (null === $this->input) {
            return 'Failed to load input file.';
        } else {
            $start = microtime(true);
            $resultPartOne = $this->part1();
            $timeElapsedPartOne = number_format(microtime(true) - $start, 4);
            $start = microtime(true);
            $resultPartTwo = $this->part2();
            $timeElapsedPartTwo = number_format(microtime(true) - $start, 4);
            return <<<EOD
                Part 1: $resultPartOne in $timeElapsedPartOne seconds
                Part 2: $resultPartTwo in $timeElapsedPartTwo seconds
                EOD;
        }
    }
}
