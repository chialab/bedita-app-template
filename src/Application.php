<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use BEdita\API\App\BaseApplication;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Chialab\FrontendKit\Middleware\ExceptionWrapperMiddleware;
use Chialab\FrontendKit\Middleware\StatusMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI !== 'cli') {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false)
            );
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin('BEdita/DevTools');
            $this->addPlugin('DebugKit');
        }

        $this->addPlugin('BEdita/Core');
        $this->addPlugin('BEdita/AWS');
        $this->addPlugin('BEdita/I18n');
        $this->addPlugin('Chialab/FrontendKit');

        if (Configure::check('FrontendPlugin')) {
            $this->addPlugin(Configure::read('FrontendPlugin'));
        }
    }

    /**
     * @inheritDoc
     */
    protected function bootstrapCli(): void
    {
        parent::bootstrapCli();

        $this->addOptionalPlugin('Cake/Repl');
    }

    /**
     * @inheritDoc
     */
    public function middleware($middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue = parent::middleware($middlewareQueue)

            // Handle plugin/theme assets like CakePHP normally does.
            ->insertBefore(
                RoutingMiddleware::class,
                new AssetMiddleware([
                    'cacheTime' => Configure::read('Asset.cacheTime'),
                ]),
            )

            ->insertBefore(
                RoutingMiddleware::class,
                new StatusMiddleware(['BEdita/Core']),
            );

        if (Configure::read('FrontendPlugin') === 'BEdita/API') {
            return $middlewareQueue;
        }

        return $middlewareQueue
            // Add base exception handling middleware
            ->insertAfter(
                ErrorHandlerMiddleware::class,
                new ExceptionWrapperMiddleware(),
            )

            // Cross Site Request Forgery (CSRF) Protection Middleware
            // https://book.cakephp.org/4/en/security/csrf.html#cross-site-request-forgery-csrf-middleware
            ->insertAfter(
                BodyParserMiddleware::class,
                new CsrfProtectionMiddleware([
                    'httponly' => true,
                ])
            );
    }

    /**
     * @inheritDoc
     */
    public function services(ContainerInterface $container): void
    {
        parent::services($container);
    }
}
