<?php
declare(strict_types=1);

namespace Service;

use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\PostTagService;
use PHPUnit\Framework\TestCase;

class PostTagServiceTest extends TestCase
{
    public function testSetPostTags(): void
    {
        $postId = new PostId(1);
        $tagIds = [
          new TagId(1),
          new TagId(2),
        ];

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('clearPostTags');

        $postRepositoryMock->expects($this->once())
            ->method('addPostTags')
            ->with(
                $this->isInstanceOf(PostId::class),
                $this->callback(function ($tagIds)
                {
                    foreach ($tagIds as $tagId) {
                        if (! $tagId instanceof TagId) {
                            return false;
                        }
                    }

                    return true;
                })
            );

        $postTagService = new PostTagService($postRepositoryMock);
        $postTagService->setPostTags($postId, $tagIds);
    }
}
