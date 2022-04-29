<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    /**
     * Name of root folder.
     *
     * @var string
     */
    protected const ROOT_FOLDER = 'root';

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);

        $this->loadComponent('Chialab/FrontendKit.Objects');
        $this->loadComponent('Chialab/FrontendKit.Publication', [
            'publication' => static::ROOT_FOLDER,
        ]);
        $this->loadComponent('Chialab/FrontendKit.Staging');
    }
}
