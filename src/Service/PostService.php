<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Post;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use App\Repository\PostRepositoryInterface;

class PostService
{
    public function __construct(private PostRepositoryInterface $repo)
    {
    }

    public function createPost(?UserId $userId, string $title, string $content, string $imageName): PostId
    {
        $slug = $this->generateSlug($title);

        $post = new Post(
            id: null,
            userId: $userId,
            date: date('Y-m-d H:i:s'),
            title: $title,
            slug: $slug,
            content: $content,
            imageName: $imageName,
        );

        return $this->repo->addPost($post);
    }

    private function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title, 'UTF-8');
        $slug = $this->transliterate($slug);
        $slug = preg_replace('/[^a-z0-9]+/u', ' ', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Simple transliteration of Cyrillic characters into Latin.
     */
    private function transliterate(string $string): string
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
            'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'і' => 'i', 'ї' => 'yi', 'є' => 'ye', 'ґ' => 'g'
        ];

        return strtr($string, $converter);
    }
}