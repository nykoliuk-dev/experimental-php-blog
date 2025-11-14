<?php

namespace Unit\Service;

use App\Service\FileMoverInterface;
use App\Service\LocalFileUploader;
use PHPUnit\Framework\TestCase;

class LocalFileUploaderTest extends TestCase
{
    public function testUploadMovesFile(): void
    {
        $uploadDir = __DIR__ . '/../../../tmp/test';

        $mover = $this->createMock(FileMoverInterface::class);
        $mover->expects($this->once())
            ->method('move')
            ->with(
                $this->equalTo('/tmp/testfile'),
                $this->callback(function(string $destination) use ($uploadDir) {
                    return str_starts_with($destination, $uploadDir)
                        && str_ends_with($destination, '.jpg');
                })
            )
            ->willReturn(true);

        $uploader = new LocalFileUploader($uploadDir, $mover);

        $file = [
            'name' => 'photo.jpg',
            'tmp_name' => '/tmp/testfile',
            'error' => UPLOAD_ERR_OK,
        ];

        $newName = $uploader->upload($file);
        $this->assertMatchesRegularExpression('/^img_[a-f0-9]+\.[0-9]+\.(jpg)$/i', $newName);
    }
}
