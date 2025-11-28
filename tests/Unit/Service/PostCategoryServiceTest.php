<?php
declare(strict_types=1);

namespace Service;

use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\PostCategoryService;
use PHPUnit\Framework\TestCase;

class PostCategoryServiceTest extends TestCase
{
    public function testSetPostCategories(): void
    {
        $postId = new PostId(1);
        $categoryIds = [
            new CategoryId(1),
            new CategoryId(2),
        ];

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('clearPostCategories')
            ->with($this->isInstanceOf(PostId::class));
        $postRepositoryMock->expects($this->once())
            ->method('addPostCategories')
            ->with(
                $this->isInstanceOf(PostId::class),
                $this->callback(function ($categoryIds)
                {
                    foreach ($categoryIds as $categoryId) {
                        if (! $categoryId instanceof CategoryId) {
                            return false;
                        }
                    }

                    return true;
                })
            );

        $postTagService = new PostCategoryService($postRepositoryMock);
        $postTagService->setPostCategories($postId, $categoryIds);
    }
}
