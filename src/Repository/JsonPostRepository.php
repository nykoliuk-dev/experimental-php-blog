<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
use App\Repository\Interface\PostRepositoryInterface;

class JsonPostRepository implements PostRepositoryInterface
{
    private string $storage;
    private string $postTagsStorage;
    private string $postCategoriesStorage;

    public function __construct(string $storagePath)
    {
        $this->storage = $storagePath;
        $dir = dirname($storagePath);
        $this->postTagsStorage = $dir . '/post_tags.json';
        $this->postCategoriesStorage = $dir . '/post_categories.json';
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
            $userId = !empty($item['user_id']) ? new UserId((int)$item['user_id']) : null;
            $posts[(int)$item['id']] = new Post(
                id:         new PostId((int)$item['id']),
                userId:     $userId,
                date:       $item['date'],
                title:      $item['title'],
                slug:       $item['slug'],
                content:    $item['content'],
                imageName:  $item['image_name'],
            );
        }

        return $posts;
    }

    public function getPost(PostId $id): ?Post
    {
        $posts = $this->getPosts();
        return $posts[$id->value()] ?? null;
    }

    private function newPostId(array $posts): PostId
    {
        if (empty($posts)) {
            return new PostId(1);
        }
        $id = max(array_keys($posts)) + 1;
        return new PostId($id);
    }

    public function addPost(Post $post): PostId
    {
        $posts = $this->getPosts();
        $id = $this->newPostId($posts);

        $postWithId  = new Post(
            id:         $id,
            userId:     $post->getUserId(),
            date:       $post->getDate(),
            title:      $post->getTitle(),
            slug:       $post->getSlug(),
            content:    $post->getContent(),
            imageName:  $post->getImgName(),
        );

        $posts[$id->value()] = $postWithId;
        $this->savePosts($posts);
        return $id;
    }

    public function removePost(PostId $id): bool
    {
        $posts = $this->getPosts();
        if (!isset($posts[$id->value()])){
            return false;
        }
        unset($posts[$id->value()]);
        $this->savePosts($posts);
        $this->clearPostTags($id);
        $this->clearPostCategories($id);
        return true;
    }

    public function clearPostTags(PostId $postId): bool
    {
        $currentRelations = $this->loadJsonData($this->postTagsStorage);
        $countBefore = count($currentRelations[$postId->value()] ?? []);

        if (isset($currentRelations[$postId->value()])) {
            unset($currentRelations[$postId->value()]);
            $this->saveJsonData($this->postTagsStorage, $currentRelations);
            return $countBefore > 0;
        }
        return false;
    }

    public function clearPostCategories(PostId $postId): bool
    {
        $currentRelations = $this->loadJsonData($this->postCategoriesStorage);
        $countBefore = count($currentRelations[$postId->value()] ?? []);

        if (isset($currentRelations[$postId->value()])) {
            unset($currentRelations[$postId->value()]);
            $this->saveJsonData($this->postCategoriesStorage, $currentRelations);
            return $countBefore > 0;
        }
        return false;
    }

    public function addPostTags(PostId $postId, array $tagIds): void
    {
        if (empty($tagIds)) {
            return;
        }

        $currentRelations = $this->loadJsonData($this->postTagsStorage);
        $postIdValue = $postId->value();

        $currentTags = array_map(fn(TagId $t) => $t->value(), $tagIds);

        $existingTags = $currentRelations[$postIdValue] ?? [];
        $newTags = array_unique(array_merge($existingTags, $currentTags));

        $currentRelations[$postIdValue] = array_values($newTags);

        $this->saveJsonData($this->postTagsStorage, $currentRelations);
    }

    public function addPostCategories(PostId $postId, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $currentRelations = $this->loadJsonData($this->postCategoriesStorage);
        $postIdValue = $postId->value();

        $currentCategories = array_map(fn(CategoryId $c) => $c->value(), $categoryIds);

        $existingCategories = $currentRelations[$postIdValue] ?? [];
        $newCategories = array_unique(array_merge($existingCategories, $currentCategories));

        $currentRelations[$postIdValue] = array_values($newCategories);

        $this->saveJsonData($this->postCategoriesStorage, $currentRelations);
    }

    private function loadJsonData(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private function saveJsonData(string $filePath, array $data): void
    {
        $res = file_put_contents(
            $filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if ($res === false) {
            throw new \RuntimeException("Cannot write to relation DB file: {$filePath}");
        }
    }

    private function postToArray(Post $post): array
    {
        return [
            'id'         => $post->getId()->value(),
            'user_id'    => $post->getUserId()?->value(),
            'date'       => $post->getDate(),
            'title'      => $post->getTitle(),
            'slug'       => $post->getSlug(),
            'content'    => $post->getContent(),
            'image_name' => $post->getImgName(),
        ];
    }

    private function savePosts(array $posts): void
    {
        $data = array_map([$this, 'postToArray'], $posts);

        $res = file_put_contents(
            $this->storage,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if ($res === false) {
            throw new \RuntimeException('Cannot write to DB file.');
        }
    }
}