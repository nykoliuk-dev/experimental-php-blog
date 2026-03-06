<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ValueObject\UserId;
use App\Service\Interface\CurrentUserProviderInterface;

class SessionUserProvider implements CurrentUserProviderInterface
{
    public function getCurrentUserId(): ?UserId
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user']['id'])) {
            return new UserId((int)$_SESSION['user']['id']);
        }
        return null;
    }
}