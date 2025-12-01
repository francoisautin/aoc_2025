<?php

declare(strict_types=1);

namespace App\Day;

class Day1 extends DayAbstract
{
    private const int INITIAL_POSITION = 50;
    private const int NUMBER_OF_POSITIONS = 100;

    public function __construct()
    {
        parent::__construct('input01.txt');
    }

    public function part1(): string
    {
        // Setting up initial values
        $password = 0;
        $currentValue = self::INITIAL_POSITION;

        // Setting up input
        $lines = $this->getSanitizedInput();

        // For each line, spin the dial
        // If the value at the end of the operation is 0, we increment the password
        foreach ($lines as $line) {
            $currentValue = $this->spinDial($currentValue, $line);
            if (0 === $currentValue) {
                $password++;
            }
        }

        return (string) $password;
    }

    public function part2(): string
    {
        // Setting up initial values
        $password = 0;
        $currentValue = self::INITIAL_POSITION;

        // Setting up input
        $lines = $this->getSanitizedInput();

        // For each line, we first count the number of full rotations
        // We increase the password for each full rotation
        // We then subtract these rotations from the input
        // With the new clicks, we spin the dial
        // Depending on the value after rotation, and on the direction of the rotation, we increment the password
        //
        //             | DIFFERENCE BETWEEN CURRENT AND NEW VALUES |
        // | DIRECTION | CURRENT > NEW       | NEW > CURRENT       |
        // | LEFT      | inc pass if NEW=0   | increase password   |
        // | RIGHT     | increase password   | inc pass if NEW=0   |
        //
        // And if the new value is equal to 0, we increase the password as well
        //
        // Beware: If the previous rotation ended on a 0, a left rotation will always be "negative"
        // That would not count as landing on a Zero: beware of counting two zeroes for one!
        foreach ($lines as $line) {
            $direction = str_starts_with($line, 'R');
            $clicks = (int) substr($line, 1);

            $fullRotations = intdiv($clicks, self::NUMBER_OF_POSITIONS);
            $password += $fullRotations;
            $clicks -= $fullRotations * self::NUMBER_OF_POSITIONS;

            if (0 === $clicks) {
                continue;
            }

            // Rotation
            $newValue = ($currentValue + ($direction ? $clicks : -$clicks)) % self::NUMBER_OF_POSITIONS;
            if ($newValue < 0) {
                $newValue += self::NUMBER_OF_POSITIONS;
            }

            // If left move started at 0, remove 1 from password
            if (!$direction && 0 === $currentValue) {
                $password--;
            }

            // Computing new password value
            if (($direction && ($currentValue > $newValue)) || (!$direction && ($newValue > $currentValue))) {
                $password++;
            } elseif (0 === $newValue) {
                $password++;
            }

            $currentValue = $newValue;
        }

        return (string) $password;
    }

    /**
     * @return string[]
     */
    private function getSanitizedInput(): array
    {
        return $this->input
            // Splitting file into individual lines
            |> (static fn(string $input) => explode(PHP_EOL, $input))
            // Filtering out garbage
            |> (static fn(array $lines) => array_filter($lines, static fn($line) => 1 === preg_match('/^[L-R]\d*$/', $line)));
    }

    private function spinDial(int $currentDialValue, string $line): int
    {
        // False for left, True for right
        $direction = str_starts_with($line, 'R');
        $clicks = (int) substr($line, 1);

        $newValue = ($currentDialValue + ($direction ? $clicks : -$clicks)) % self::NUMBER_OF_POSITIONS;
        if ($newValue < 0) {
            $newValue += self::NUMBER_OF_POSITIONS;
        }

        return $newValue;
    }
}
