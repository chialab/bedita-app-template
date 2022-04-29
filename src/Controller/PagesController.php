<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Chialab\FrontendKit\Traits\GenericActionsTrait;

/**
 * Pages Controller.
 */
class PagesController extends AppController
{
    use GenericActionsTrait {
        objects as private _objects;
        object as private _object;
        fallback as private _fallback;
    }

    /**
     * Generic object route.
     *
     * @param string $uname Object `id` or `uname`.
     * @return \Cake\Http\Response
     */
    public function objects(string $uname): Response
    {
        try {
            return $this->_objects($uname);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException(__('Page not found'), null, $e);
        }
    }

    /**
     * Generic object route.
     *
     * @param string $uname Object `id` or `uname`.
     * @return \Cake\Http\Response
     */
    public function object(string $uname): Response
    {
        try {
            return $this->_object($uname);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException(__('Page not found'), null, $e);
        }
    }

    /**
     * Generic object view.
     *
     * @param string $path Object path.
     * @return \Cake\Http\Response
     */
    public function fallback(string $path): Response
    {
        try {
            return $this->_fallback($path);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException(__('Page not found'), null, $e);
        }
    }
}
