<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Core\Controller;
use App\Service\AuthService;
use App\Validation\LoginValidator;

class LoginController extends Controller
{
    public function showLoginForm(): void
    {
        $this->render('auth/login', [
            'title' => 'Форма регистрации',
        ]);
    }

    public function register(LoginValidator $validator, AuthService $authService): void
    {
        header('Content-Type: application/json');

        $errors = $validator->validate($_POST);

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        try {
            $user = $authService->login($_POST['email'], $_POST['password']);

            $_SESSION['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful'
            ]);

        } catch (\RuntimeException $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}