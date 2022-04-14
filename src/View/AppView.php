<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\Core\Configure;
use Chialab\FrontendKit\View\AppView as BaseAppView;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/3/en/views.html#the-app-view
 */
class AppView extends BaseAppView
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        if (Configure::check('FrontendPlugin')) {
            $this->setTheme(Configure::read('FrontendPlugin'));
        }
    }
}
