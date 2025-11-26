<?php
declare(strict_types=1);

namespace App\Service\Interface;

interface FileUploaderInterface
{
    public function upload(array $file): string;
}