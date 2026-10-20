<?php

namespace Tests\Unit;

use App\Support\FileSize;
use PHPUnit\Framework\TestCase;

class FileSizeTest extends TestCase
{
    public function test_null_returns_null(): void
    {
        $this->assertNull(FileSize::format(null));
    }

    public function test_bytes_below_one_kb(): void
    {
        $this->assertSame('0 B', FileSize::format(0));
        $this->assertSame('512 B', FileSize::format(512));
        $this->assertSame('1023 B', FileSize::format(1023));
    }

    public function test_kilobytes(): void
    {
        $this->assertSame('1,0 KB', FileSize::format(1024));
        $this->assertSame('1,5 KB', FileSize::format(1536));
    }

    public function test_megabytes(): void
    {
        $this->assertSame('1,0 MB', FileSize::format(1_048_576));
        $this->assertSame('1,5 MB', FileSize::format(1_536_000));
        $this->assertSame('5,0 MB', FileSize::format(5 * 1_048_576));
    }

    public function test_gigabytes(): void
    {
        $this->assertSame('1,0 GB', FileSize::format(1_073_741_824));
    }
}