<?php

declare(strict_types=1);

namespace App\Loader;

final class Loader
{
    /**
     * @throws InputLoadException
     */
    public static function loadFileContents(string $fileName): string
    {
        $path = "input/$fileName";
        if (!file_exists($path)) {
            throw new InputLoadException("File $path does not exist.");
        }

        $fileHandle = fopen($path, 'r');
        if (!$fileHandle) {
            throw new InputLoadException("Failed to open file $fileName.");
        }

        $fileSize = filesize($path);
        if (false === $fileSize) {
            throw new InputLoadException("Failed to get file size of $fileName.");
        } elseif (0 === $fileSize) {
            throw new InputLoadException("Failed to read file $fileName: empty.");
        }

        $contents = fread($fileHandle, $fileSize);
        if (false === $contents) {
            throw new InputLoadException("Failed to read file $fileName: fread failed.");
        }

        fclose($fileHandle);
        return $contents;
    }
}
