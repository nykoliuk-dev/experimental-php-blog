<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\Post;
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

    public function store(PostValidator $validator, PostService $postService): void
    {
        header('Content-Type: application/json');

        $errors = $validator->validate(array_merge($_POST, $_FILES));

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $imageName = $this->handleFileUpload($_FILES['file']);

        $id = $postService->createPost($_POST['title'], $_POST['content'], $imageName);

        echo json_encode(['success' => true, 'message' => "Пост $id успешно добавлен!"]);
        exit;
    }

    private function handleFileUpload(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Ошибка загрузки файла.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed, true)) {
            throw new \RuntimeException('Недопустимый тип файла.');
        }

        $uniqueName = uniqid('img_', true) . '.' . $ext;
        $targetDir = $this->config['paths']['gallery'] . '/';
        $targetPath = $targetDir . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Не удалось сохранить файл.');
        }

        return $uniqueName;
    }
}