<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Error;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Error\ExceptionRenderer;

class AppExceptionRenderer extends ExceptionRenderer
{
    /**
     * @inheritDoc
     */
    protected function _getController()
    {
        if ($this->error->getCode() < 400 || $this->error->getCode() >= 500) {
            return parent::_getController();
        }

        $plugin = Configure::read('FrontendPlugin');
        if ($plugin === null) {
            return parent::_getController();
        }

        $className = App::className($plugin . '.Error', 'Controller', 'Controller');
        if (!class_exists($className)) {
            return parent::_getController();
        }

        return new $className($this->request);
    }
}
