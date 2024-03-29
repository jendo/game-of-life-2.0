<?php

declare(strict_types=1);

namespace AppTest\Unit\File;

use App\File\FileIsIsNotReadableException;
use App\File\Loader;
use App\File\FileNotExistException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class LoaderTest extends TestCase
{
    use ProphecyTrait;

    private Loader $loader;

    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new Loader();
        $this->filename = __DIR__ . DIRECTORY_SEPARATOR . 'file.txt';
    }

    /**
     * @throws FileNotExistException
     * @throws FileIsIsNotReadableException
     */
    public function testLoadWithExistingFile(): void
    {
        $expectedContent = 'file content';
        file_put_contents($this->filename, $expectedContent);
        $content = $this->loader->load($this->filename);

        self::assertSame($expectedContent, $content, 'Expected content from file is not same like actual.');
    }

    /**
     * @throws FileIsIsNotReadableException
     */
    public function testLoadWithNotExistingFile(): void
    {
        $fileName = 'dummy.txt';
        $this->expectException(FileNotExistException::class);
        $this->loader->load(__DIR__ . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @throws FileNotExistException
     */
    public function testLoadWithNotReadableFile(): void
    {
        file_put_contents($this->filename, '');
        chmod($this->filename, 000);
        $this->expectException(FileIsIsNotReadableException::class);
        $this->loader->load($this->filename);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filename) === true) {
            unlink($this->filename);
        }
    }

}
