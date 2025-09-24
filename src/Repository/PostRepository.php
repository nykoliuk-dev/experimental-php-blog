<?php
declare(strict_types=1);

namespace App\Repository;

class PostRepository
{
    private string $storage;

    public function __construct(string $storagePath)
    {
        $this->storage = $storagePath;
    }

    /** @return array[] */
    public function getPosts(): array
    {
        if (!file_exists($this->storage)) return [];
        return json_decode(file_get_contents($this->storage), true) ?? [];
    }

    public function getPost(int $id): ?array
    {
        $posts = $this->getPosts();
        return $posts[$id] ?? null;
    }

    public function addPost(array $data): int
    {
        $posts = $this->getPosts();
        $id = count($posts) ? max(array_keys($posts)) + 1 : 1;
        $data['id'] = $id;
        $data['date'] = date('Y-m-d H:i:s');
        $posts[$id] = $data;
        $this->savePosts($posts);
        return $id;
    }

    public function removePost(int $id): bool
    {
        $posts = $this->getPosts();
        if (!isset($posts[$id])) return false;
        unset($posts[$id]);
        $this->savePosts($posts);
        return true;
    }

    private function savePosts(array $posts): void
    {
        file_put_contents($this->storage, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}