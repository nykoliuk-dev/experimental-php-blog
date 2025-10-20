<?php
declare(strict_types=1);

namespace App\Service;

interface FileUploaderInterface
{
    public function upload(array $file): string;
}