<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Core\Controller;
use App\Service\AuthService;

class LogoutController extends Controller
{
    public function logout(AuthService $authService): void
    {
        $authService->logout();

        header("Location: /");
        exit;
    }
}