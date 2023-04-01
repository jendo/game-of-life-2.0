<?php

declare(strict_types=1);

namespace App\File;

class Writer
{
    /**
     * @param string $fileName
     * @param string $content
     * @return void
     * @throws FileIsIsNotWritableException
     * @throws FileNotExistException
     */
    public function write(string $fileName, string $content): void
    {
        if (file_exists($fileName) === false) {
            throw new FileNotExistException(sprintf('The file `%s` does not exist.', $fileName));
        }

        $result = file_put_contents($fileName, $content);

        if ($result === false) {
            throw new FileIsIsNotWritableException(sprintf('Can not read the file `%s`', $fileName));
        }
    }
}
