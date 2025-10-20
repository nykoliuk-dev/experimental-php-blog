<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Service\FileUploaderInterface;
use App\Service\PostService;
use App\Validation\PostValidator;

class PostController extends Controller
{
    public function index(): void
    {
        $posts = $this->repo->getPosts();

        $this->render('posts/index', [
            'title' => 'Список постов',
            'posts' => $posts,
        ]);
    }
    public function show(array $params): void
    {
        $id = (int)$params['id'];
        $post = $this->repo->getPost($id);

        if (!$post) {
            http_response_code(404);
            echo 'Пост не найден';
            return;
        }

        $this->render('posts/show', [
            'title' => $post->getTitle(),
            'post'  => $post,
        ]);
    }
    public function create(): void
    {
        $this->render('posts/create', [
            'title' => 'Добавить статью',
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

        $id = $postService->createPost($_POST['title'], $_POST['content'], $imageName);

        echo json_encode(['success' => true, 'message' => "Пост $id успешно добавлен!"]);
        exit;
    }
}