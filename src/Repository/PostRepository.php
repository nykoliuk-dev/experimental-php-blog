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

    public function getPost(int $id): ?Post
    {
        $posts = $this->getPosts();
        return $posts[$id] ?? null;
    }

    public function lastId(): int
    {
        $posts = $this->getPosts();
        if (empty($posts)) {
            return 0;
        }

        return max(array_keys($posts));
    }

    public function addPost(Post $post): int
    {
        $posts = $this->getPosts();
        $id = $post->getId();
        $posts[$id] = $post;
        $this->savePosts($posts);
        return $id;
    }

    public function removePost(int $id): bool
    {
        $posts = $this->getPosts();
        if (!isset($posts[$id])){
            return false;
        }
        unset($posts[$id]);
        $this->savePosts($posts);
        return true;
    }

    private function savePosts(array $posts): void
    {
        $res = file_put_contents($this->storage, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($res === false) {
            throw new \RuntimeException('Cannot write to DB file.');
        }
    }
}