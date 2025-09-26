<?php
declare(strict_types=1);

namespace App\Core;

use App\Repository\PostRepository;

class Controller
{
    public function __construct(protected PostRepository $repo) {}
}