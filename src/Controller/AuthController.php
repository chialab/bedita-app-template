<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Auth Controller.
 */
class AuthController extends AppController
{
    /**
     * Return home route, where users will be redirected after logout or when they try to login to a non-staging site.
     *
     * @return array
     */
    protected function getHomeRoute(): array
    {
        return ['_name' => 'pages:home'];
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        if (!Configure::read('StagingSite')) {
            return $this->redirect($this->getHomeRoute());
        }

        $this->Authentication->allowUnauthenticated(['login']);

        return null;
    }

    /**
     * Login action.
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            // Login succeeded.
            return $this->redirect($this->Authentication->getLoginRedirect() ?? $this->getHomeRoute());
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Incorrect username or password'));
        }

        return null;
    }

    /**
     * Logout action.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->Authentication->logout();

        return $this->redirect($this->getHomeRoute());
    }
}