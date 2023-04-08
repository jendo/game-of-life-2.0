<?php

declare(strict_types=1);

namespace AppTest\Unit\File;

use App\File\FileIsIsNotWritableException;
use App\File\FileNotExistException;
use App\File\Writer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WriterTest extends TestCase
{
    use ProphecyTrait;

    private Writer $fileWriter;

    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileWriter = new Writer();
        $this->filename = __DIR__ . DIRECTORY_SEPARATOR . 'file.txt';
        file_put_contents($this->filename, '');
    }

    /**
     * @return void
     * @throws FileNotExistException
     * @throws FileIsIsNotWritableException
     */
    public function testWriteToExistingFile(): void
    {
        $content = 'file content';
        $this->fileWriter->write($this->filename, $content);

        self::assertSame($content, file_get_contents($this->filename), 'Expected content from file is not same like actual.');
    }

    /**
     * @return void
     * @throws FileIsIsNotWritableException
     * @throws FileNotExistException
     */
    public function testWriteToNotExistingFile(): void
    {
        $fileName = 'dummy-file.txt';
        $this->expectException(FileNotExistException::class);
        $this->fileWriter->write(__DIR__ . DIRECTORY_SEPARATOR . $fileName, 'content');
    }

    /**
     * @return void
     * @throws FileIsIsNotWritableException
     * @throws FileNotExistException
     */
    public function testWriteToNotReadableFile(): void
    {
        chmod($this->filename, 000);
        $this->expectException(FileIsIsNotWritableException::class);
        $this->fileWriter->write($this->filename, 'content');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filename) === true) {
            unlink($this->filename);
        }
    }

}
