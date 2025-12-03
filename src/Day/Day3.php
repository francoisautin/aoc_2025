<?php

declare(strict_types=1);

namespace App\Day;

use Override;

class Day3 extends DayAbstract
{
    public function __construct()
    {
        parent::__construct('input03.txt');
    }

    #[Override]
    public function part1(): string
    {
        return (string) ($this->getBanks()
            |> (static fn(array $banks) => array_map(static::getJolts(...), $banks))
            |> (static fn(array $joltsPerBank) => array_reduce($joltsPerBank, (static fn(int $carry, int $item) => $carry += $item), 0)));
    }

    #[Override]
    public function part2(): string
    {
        return (string) ($this->getBanks()
            |> (static fn(array $banks) => array_map(static::getMoreJolts(...), $banks))
            |> (static fn(array $joltsPerBank) => array_reduce($joltsPerBank, (static fn(int $carry, string $item) => $carry += (int) $item), 0)));
    }

    /**
     * @return string[]
     */
    public function getBanks(): array
    {
        return explode(PHP_EOL, $this->input)
            |> (static fn($lines) => array_filter($lines, static fn(string $line) => '' !== $line));
    }

    public static function getJolts(string $bank): int
    {
        $cellJoltages = str_split($bank);
        $numberOfCells = \count($cellJoltages);
        $dozen = $cellJoltages[0];
        $unit = 0;

        for ($i = 1; $i < $numberOfCells - 1; $i++) {
            if ($dozen < $cellJoltages[$i]) {
                $dozen = $cellJoltages[$i];
                $unit = 0;
            } elseif ($unit < $cellJoltages[$i]) {
                $unit = $cellJoltages[$i];
            }
        }

        if (0 === $unit || $unit < $cellJoltages[$numberOfCells - 1]) {
            $unit = $cellJoltages[$numberOfCells - 1];
        }

        return 10 * (int) $dozen + (int) $unit;
    }

    public static function getMoreJolts(string $bank): string
    {
        $windowSize = \strlen($bank) - 12 + 1;
        $enabledJolts = '';

        for ($i = 0; $i < \strlen($bank) && 12 > \strlen($enabledJolts); $i++) {
            $window = substr($bank, $i, $windowSize);
            $index = self::getIndexOfLargestWithinWindow($window);
            $enabledJolts .= $window[$index];
            if (0 !== $index) {
                $i += $index;
                $windowSize -= $index;
            }
        }

        return $enabledJolts;
    }

    public static function getIndexOfLargestWithinWindow(string $window): int
    {
        $largest = 0;
        $index = 0;
        for ($i = 0; $i < \strlen($window); $i++) {
            if ($largest < $window[$i]) {
                $largest = $window[$i];
                $index = $i;
            }
        }

        return $index;
    }
}
