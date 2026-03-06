<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use App\Service\CommentService;
use App\Validation\CommentValidator;

class CommentController extends Controller
{
    public function store(array $params, CommentValidator $validator, CommentService $commentService): void
    {
        header('Content-Type: application/json');

        $postId = new PostId((int)$params['postId']);

        $data = $_POST;
        $data['post_id'] = $postId->value();

        $errors = $validator->validate($data);

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $userId = !empty($_SESSION['user']) ? new UserId($_SESSION['user']['id']) : null;

        try {
            $commentService->createComment($postId, $userId, $data['content']);

            echo json_encode(['success' => true, 'message' => "Комментарий успешно добавлен."]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'errors' => ['system' => 'Ошибка при сохранении комментария.']]);
        }
        exit;
    }
}