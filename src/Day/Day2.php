<?php

declare(strict_types=1);

namespace App\Day;

use Override;

use function strlen;

/**
 * I could have used regex
 * But I did not want to
 * I am not sorry
 */
class Day2 extends DayAbstract
{
    public function __construct()
    {
        parent::__construct('input02.txt');
    }

    #[Override]
    public function part1(): string
    {
        $ranges = $this->getSanitizedInput();
        $sumOfInvalidIds = 0;

        foreach ($ranges as $range) {
            // Initializing at 10 before single digit values are automatically invalid
            // Setting it below this threshold will cause an infinite loop
            $number = '10';

            while ($number = $this->findNextInvalidNumber($number, $range)) {
                $sumOfInvalidIds += (int) $number;
            }
        }

        return (string) $sumOfInvalidIds;
    }

    #[Override]
    public function part2(): string
    {
        return $this->getSanitizedInput()
            |> (static fn(array $ranges) => array_map(self::getInvalidNumbersForRange(...), $ranges))
            |> (static fn(array $invalidNumbers) => array_merge(...array_values($invalidNumbers)))
            |> array_unique(...)
            |> (static fn(array $invalidNumbers) => (string) array_reduce($invalidNumbers, static function (int $carry, string $new) {
                $carry += (int) $new;
                return $carry;
            }, 0));
    }

    /**
     * @return array<string[]>
     */
    private function getSanitizedInput(): array
    {
        return explode(',', $this->input)
            |> (static fn($array) => array_filter($array, static fn(string $str) => \is_int(preg_match('/^\d+-\d+$/', $str))))
            |> (static fn($array) => array_map(static fn(string $range) => explode('-', $range), $array));
    }

    private function isInvalid(string $value): bool
    {
        if (0 !== \strlen($value) % 2) {
            return false;
        }

        $firstHalf = substr($value, 0, (int) (floor(\strlen($value) / 2)));
        $secondHalf = substr($value, (int) (floor(\strlen($value) / 2)));

        return $firstHalf === $secondHalf;
    }

    /**
     * @param string[] $range
     */
    private function findNextInvalidNumber(string $previousInvalidValue, array $range): string|false
    {
        // Above upper bound
        if ($previousInvalidValue > $range[1]) {
            return false;
        }

        // Below lower bound
        if ($previousInvalidValue < $range[0]) {
            $previousInvalidValue = $range[0];
            if ($this->isInvalid($previousInvalidValue)) {
                return $previousInvalidValue;
            }
        }

        // Getting halves
        $firstHalf = substr($previousInvalidValue, 0, (int) (floor(\strlen($previousInvalidValue) / 2)));
        $secondHalf = substr($previousInvalidValue, (int) (floor(\strlen($previousInvalidValue) / 2)));

        // If first have is of a lower power of ten than the second half, we compute the next potential invalid value
        // We round the first half to the next power of 10, and we set the value of the second half to that of the first
        if (\strlen($firstHalf) < \strlen($secondHalf)) {
            $numberOfDozens = 10 ** \strlen($firstHalf);
            $half = (string) (ceil((int) $firstHalf / $numberOfDozens) * $numberOfDozens);
            $newValue = $half . $half;
            // Otherwise, we go into the main flow
        } else {
            // If first half is smaller than second half:
            // E.g. 1234_6789 becomes 1235_1235
            if ($firstHalf < $secondHalf) {
                $newValue = ((int) $firstHalf + 1) . ((int) $firstHalf + 1);
                // If first half is greater than second half:
                // E.g. 6789_1234 becomes 6789_6789
            } elseif ($firstHalf > $secondHalf) {
                $newValue = ((int) $firstHalf) . ((int) $firstHalf);
            } else {
                // If both halves are equal, we increment both
                // E.g. 1234_1234 becomes 1235_1235
                $newValue = ((int) $secondHalf + 1) . ((int) $secondHalf + 1);
            }
        }

        // If the new value is out of range, we return false to break the loop
        if ($newValue > $range[1]) {
            return false;
        }

        return $newValue;
    }

    /**
     * I gave up, this is horrible lmao
     * This gets worse as the ranges increase in size
     * For smaller ranges... this works okay
     *
     * @param string[] $range
     * @return string[]
     */
    private static function getInvalidNumbersForRange(array $range): array
    {
        $invalidNumbers = [];
        $currentNumber = $range[0];
        while ((int) $currentNumber <= (int) $range[1] /* Maximum */) {
            if (static::hasPattern($currentNumber)) {
                $invalidNumbers[] = $currentNumber;
            }
            $currentNumber = (string) (((int) $currentNumber) + 1);
        }

        return $invalidNumbers;
    }

    public static function hasPattern(string $currentNumber): bool
    {
        $length = \strlen($currentNumber);
        $halfLength = (int) floor($length / 2);
        for ($patternLength = 1; $patternLength <= $halfLength; $patternLength++) {
            // Only check pattern lengths that evenly divide the string length
            if (\strlen($currentNumber) % $patternLength !== 0) {
                continue;
            }

            $splitString = str_split($currentNumber, $patternLength);
            if (array_all($splitString, static fn($entry) => $splitString[0] === $entry)) {
                return true;
            }
        }
        return false;
    }
}
