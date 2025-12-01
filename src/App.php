<?php

declare(strict_types=1);

namespace App;

use App\Day\DayAbstract;
use JetBrains\PhpStorm\NoReturn;

/**
 * @phpstan-type AocConfig array{help: boolean, days: array<array-key, int>}
 */
final class App
{
    public function run(): void
    {
        $config = $this->parseArgs();

        if ($config['help']) {
            $this->printHelp();
        }

        if (empty($config['days'])) {
            $this->printNoDaysError();
        }

        foreach ($config['days'] as $day) {
            $this->runDay($day);
        }
    }

    /**
     * @return AocConfig
     */
    public function parseArgs(): array
    {
        if (!isset($_SERVER['argv'])) {
            throw new \RuntimeException('Unprocessable error: No arguments passed to script.');
        }

        $config = [
            'help' => false,
            'days' => [],
        ];

        $skippedScriptName = false;
        foreach ($_SERVER['argv'] as $arg) {
            if ($skippedScriptName) {
                $this->updateConfig($arg, $config);
            } else {
                $skippedScriptName = true;
            }
        }

        return $config;
    }

    /**
     * @param string $arg
     * @param AocConfig $config
     * @return void
     */
    public function updateConfig(string $arg, array &$config): void
    {
        if ('--help' === $arg || '-h' === $arg) {
            $config['help'] = true;
        }
        if ('--all' === $arg || '-a' === $arg) {
            $config['days'] = range(1, 25);
        }
        if (is_numeric($arg)) {
            $number = (int) $arg;
            if (0 < $number && 26 > $number) {
                if (!\in_array($number, $config['days'], true)) {
                    $config['days'][] = $number;
                }
            } else {
                fwrite(STDERR, "Ignoring number $number: invalid day.\n");
            }
        }
    }

    #[NoReturn]
    public function printHelp(int $code = 0): void
    {
        $helpText = <<<EOD

            =================================
            François Autin's AOC 2025 answers
            =================================
            Usage:
              ./aoc_2025 [parameters] [space separated list of days]
              Valid days are 1 through 12

            Parameters:
              -h | --help: Print this help screen
              -a | --all: Run all 12 challenges


            EOD;
        fwrite(STDOUT, $helpText);
        exit($code);
    }

    #[NoReturn]
    public function printNoDaysError(): void
    {
        fwrite(STDOUT, "No days were provided. Exiting...\n");
        $this->printHelp(1);
    }

    public function runDay(int $day): void
    {
        $className = 'App\Day\Day' . $day;
        /** @var DayAbstract $dayClass */
        $dayClass = new $className();
        $results = $dayClass->getResults();

        $dayResults = <<<EOD
            Day $day:
            $results

            EOD;
        fwrite(STDOUT, $dayResults);
    }
}
