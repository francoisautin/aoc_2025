<?php

declare(strict_types=1);

namespace App\Day;

use Override;

class Day4 extends DayAbstract
{
    public function __construct()
    {
        parent::__construct('input04.txt');
    }

    #[Override]
    public function part1(): string
    {
        [$grid, $x, $y] = $this->buildGridData();
        return (string) self::countAccessibleRolls($grid, $x, $y);
    }

    #[Override]
    public function part2(): string
    {
        [$grid, $x, $y] = $this->buildGridData();
        $total = 0;
        do {
            [$grid, $removed] = self::removeAccessibleRolls($grid, $x, $y);
            $total += $removed;
        } while (0 < $removed);

        return (string) $total;
    }

    /**
     * @return array{0: string[][], 1: int, 2: int}
     */
    public function buildGridData(): array
    {
        $rowToColumns = static fn(string $row) => str_split($row);
        $grid = explode(PHP_EOL, $this->input)
            |> (static fn(array $rows) => array_filter($rows, static fn($row) => '' !== $row))
            |> (static fn(array $rows) => array_map($rowToColumns, $rows));

        return [
            $grid,
            \count($grid[0]), // X
            \count($grid), // Y
        ];
    }

    /**
     * @param string[][] $grid
     */
    private static function countAccessibleRolls(array $grid, int $xMax, int $yMax): int
    {
        $total = 0;

        for ($y = 0; $y < $yMax; $y++) {
            for ($x = 0; $x < $xMax; $x++) {
                // If not a roll, ignore
                if ('.' === $grid[$y][$x]) {
                    continue;
                }
                // If roll, count neighbours
                $neighbours = self::countNeighbours($grid, $y, $x);
                if (4 > $neighbours) {
                    $total++;
                }
            }
        }

        return $total;
    }

    /**
     * @param string[][] $grid
     * @return array{0: string[][], 1: int}
     */
    private static function removeAccessibleRolls(array $grid, int $xMax, int $yMax): array
    {
        $removed = 0;
        $newGrid = [];

        for ($y = 0; $y < $yMax; $y++) {
            for ($x = 0; $x < $xMax; $x++) {
                // If not a roll, ignore
                if ('.' === $grid[$y][$x]) {
                    $newGrid[$y][$x] = '.';
                    continue;
                }
                // If roll, count neighbours
                $neighbours = self::countNeighbours($grid, $y, $x);
                if (4 > $neighbours) {
                    $newGrid[$y][$x] = '.';
                    $removed++;
                } else {
                    $newGrid[$y][$x] = '@';
                }
            }
        }

        return [
            $newGrid,
            $removed,
        ];
    }

    private static function countNeighbours(array $grid, int $y, int $x): int
    {
        $neighbours = 0;
        for ($shiftY = -1; $shiftY < 2; $shiftY++) {
            for ($shiftX = -1; $shiftX < 2; $shiftX++) {
                // Ignoring if on examined cell
                if (0 === $shiftY && 0 === $shiftX) {
                    continue;
                }
                if ('.' !== ($grid[$y + $shiftY][$x + $shiftX] ?? '.')) {
                    $neighbours++;
                }
            }
        }
        return $neighbours;
    }
}
