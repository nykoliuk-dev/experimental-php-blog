<?php
declare(strict_types=1);

namespace App\Service\Interfaces;

use App\Model\ValueObject\UserId;

/**
 * Interface for providing information about the currently logged in user.
 * Used by components that only need to read session state (e.g., controllers, template engines).
 */
interface CurrentUserProviderInterface
{
    public function getCurrentUserId(): ?UserId;
}