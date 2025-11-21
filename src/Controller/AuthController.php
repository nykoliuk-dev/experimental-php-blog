<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;

class AuthController extends Controller
{
    public function showRegisterForm(): void
    {
        $this->render('auth/register', [
            'title' => 'Форма регистрации',
        ]);
    }
}