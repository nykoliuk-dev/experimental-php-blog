<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use App\Service\FileUploaderInterface;
use App\Service\PostFacade;
use App\Service\PostService;
use App\Validation\PostValidator;
use App\ValueObject\Pagination;

class PostController extends Controller
{
    public function index(PostRepositoryInterface $repo): void
    {
        $posts = $repo->getPosts();

        $this->render('posts/index', [
            'title' => 'Список постов',
            'posts' => $posts,
        ]);
    }
    public function show(array $params, PostFacade $facade): void
    {
        $id = new PostId((int)$params['id']);
        $limit = 20;
        $page = max(1, (int)($params['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $pagination = new Pagination($limit, $offset);

        $postFullData = $facade->getPostWithRelations($id, $pagination);

        if (!$postFullData) {
            http_response_code(404);
            echo 'Пост не найден';
            return;
        }

        $this->render('posts/show', [
            'title' => $postFullData->getPost()->getTitle(),
            'post' => $postFullData->getPost(),
            'tags' => $postFullData->getTags(),
            'categories' => $postFullData->getCategories(),
            'comments' => $postFullData->getComments(),
        ]);
    }
    public function create(
        CategoryRepositoryInterface $categoryRepo,
        TagRepositoryInterface $tagRepo,
    ): void
    {
        $this->render('posts/create', [
            'title' => 'Добавить статью',
            'categories' => $categoryRepo->getCategories(),
            'tags' => $tagRepo->getTags(),
        ]);
    }

    public function store(PostValidator $validator, PostService $postService, FileUploaderInterface $fileService): void
    {
        header('Content-Type: application/json');

        $errors = $validator->validate(array_merge($_POST, $_FILES));

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $imageName = $fileService->upload($_FILES['file']);

        $userId = !empty($_SESSION['user']) ? new UserId($_SESSION['user']['id']) : null;

        $id = $postService->createPost(
            userId: $userId,
            title: $_POST['title'],
            content: $_POST['content'],
            imageName: $imageName,
            categories: $_POST['categories'],
            tags: $_POST['tags'],
        );

        echo json_encode(['success' => true, 'message' => "Пост {$id->value()} успешно добавлен!"]);
        exit;
    }
}