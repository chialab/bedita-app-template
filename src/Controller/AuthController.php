<?php
declare(strict_types=1);

namespace App\Controller;

use Chialab\FrontendKit\Traits\AuthTrait;

/**
 * Auth Controller.
 */
class AuthController extends AppController
{
    use AuthTrait;

    /**
     * Return home route, where users will be redirected after logout or when they try to login to a non-staging site.
     *
     * @return array
     */
    protected function getHomeRoute(): array
    {
        return ['_name' => 'pages:home'];
    }
}
