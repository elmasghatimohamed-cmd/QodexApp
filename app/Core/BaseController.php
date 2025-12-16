<?php

namespace App\Core;

use App\Helpers\Security;
use App\Middleware\CSRFMiddleware;

abstract class BaseController
{
    protected function view(string $path, array $data = []): void
    {
        Security::setSecurityHeaders();
        $csrfToken = CSRFMiddleware::getToken();
        extract($data);
        require __DIR__ . '/../views/' . $path . '.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

