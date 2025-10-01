<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;

class PostRepository
{
    private string $storage;

    public function __construct(string $storagePath)
    {
        $this->storage = $storagePath;
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        if (!file_exists($this->storage)){
            throw new \RuntimeException('Cannot read DB file.');
        }
        $data = json_decode(file_get_contents($this->storage), true);

        if (!is_array($data)) {
            return [];
        }

        $posts = [];

        foreach ($data as $item) {
            $posts[(int)$item['id']] = new Post(
                (int)$item['id'],
                $item['date'],
                $item['title'],
                $item['content']
            );
        }

        return $posts;
    }
}