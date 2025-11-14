<?php

namespace Integration\Service;

use App\Service\FileMoverInterface;
use App\Service\LocalFileUploader;
use PHPUnit\Framework\TestCase;

class LocalFileUploaderTest extends TestCase
{
    public function testUploadMovesFile(): void
    {
        $testDir = __DIR__ . '/../../../tmp/test';
        $fromDir = $testDir . '/from';
        $toDir = $testDir . '/to';
        if (!is_dir($fromDir)) {
            mkdir($fromDir, 0777, true);
        }
        if (!is_dir($toDir)) {
            mkdir($toDir, 0777, true);
        }

        $tempFile = tempnam($fromDir, 'json_test_');
        file_put_contents($tempFile, 'Hello');

        $mover = $this->createMock(FileMoverInterface::class);
        $mover->method('move')
            ->willReturnCallback(function($src, $dst)
            {
                return rename($src, $dst);
            });

        $uploader = new LocalFileUploader($toDir, $mover);

        $file = [
            'name' => 'photo.jpg',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
        ];

        $newName = $uploader->upload($file);

        $this->assertFileExists($toDir . '/' . $newName);
        $this->assertSame('Hello', file_get_contents($toDir . '/' . $newName));
    }
}
