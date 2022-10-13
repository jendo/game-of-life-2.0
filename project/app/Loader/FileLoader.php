<?php

declare(strict_types=1);

namespace App\Loader;

class FileLoader
{
    /**
     * @param string $fileName
     * @return string
     * @throws FileIsIsNotReadableException
     * @throws FileNotExistException
     */
    public function load(string $fileName): string
    {
        if (file_exists($fileName) === false) {
            throw new FileNotExistException(sprintf('The file `%s` does not exist.', $fileName));
        }

        $content = @file_get_contents($fileName);

        if ($content === false) {
            throw new FileIsIsNotReadableException(sprintf('Can not read the file `%s`', $fileName));
        }

        return trim($content);
    }
}
