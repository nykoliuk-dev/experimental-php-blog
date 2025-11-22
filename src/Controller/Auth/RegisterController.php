<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Core\Controller;
use App\Service\AuthService;
use App\Validation\RegisterValidator;

class RegisterController extends Controller
{
    public function showRegisterForm(): void
    {
        $this->render('auth/register', [
            'title' => 'Форма регистрации',
        ]);
    }

    public function register(RegisterValidator $validator, AuthService $authService): void
    {
        header('Content-Type: application/json');

        $errors = $validator->validate($_POST);

        if($authService->existsByUsername($_POST['name'])){
            $errors[] = "User name: {$_POST['name']} already exists";
        }
        if($authService->existsByEmail($_POST['email'])){
            $errors[] = "User with email: {$_POST['email']} already exists";
        }

        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $user = $authService->register(
            username: $_POST['name'],
            email: $_POST['email'],
            password: $_POST['password'],
        );

        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $user->getId(),
        ]);
        exit;
    }
}