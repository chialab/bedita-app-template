<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;

/**
 * Auth Controller
 *
 *
 * @method \App\Model\Entity\Auth[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AuthController extends AppController
{
    /**
     * @inheritDoc
     *
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(Event $event): ?Response
    {
        parent::beforeFilter($event);

        if (!Configure::read('StagingSite')) {
            return $this->redirect($this->getHomeRoute());
        }

        $this->Authentication->allowUnauthenticated(['login']);

        return null;
    }

    /**
     * Return home route, where users will be redirected after logout or when they try to login to a non-staging site.
     *
     * @return array|string
     */
    protected function getHomeRoute()
    {
        return ['_name' => 'pages:home'];
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

        if ($this->getRequest()->is('post') && !$result->isValid()) {
            $this->loadComponent('Flash');
            $this->Flash->error(__('Username and password mismatch'));
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
