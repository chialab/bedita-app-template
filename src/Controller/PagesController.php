<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Chialab\FrontendKit\Traits\GenericActionsTrait;

/**
 * Pages Controller.
 */
class PagesController extends AppController
{
    use GenericActionsTrait {
        fallback as private _fallback;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(EventInterface $event): void
    {
        $publication = $this->Publication->getPublication();
        $menu = [
            'publication' => $publication->uname,
        ];
        foreach ($menu as $key => $uname) {
            try {
                $menu[$key] = $this->Menu->load($uname);
            } catch (RecordNotFoundException) {
                //
            }
        }
        $this->set(compact('menu'));
    }

    /**
     * Home page
     *
     * @return void
     */
    public function home(): void
    {
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
            // If path is wrong, but the requested object exists, redirect to `/objects/{uname}`.
            // First, read last path element.
            $parts = array_filter(explode('/', $path));
            $object = array_pop($parts);
            try {
                // Now, try to load the object.
                $object = $this->Objects->loadObject($object);

                // If we reach this point, the object does exist, but the path at which it was being accessed was wrong.
                // Try to redirect to `/objects/{object}` to see if we can display it somehow.
                return $this->redirect(['_name' => 'pages:objects', 'uname' => $object->uname]);
            } catch (RecordNotFoundException $err) {
                // No object exists under this name. Re-throw original exception.
                throw $e;
            }
        }
    }
}
