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

use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\Middleware\AuthorizationMiddleware;
use BEdita\API\App\BaseApplication;
use BEdita\API\Middleware\ApplicationMiddleware;
use BEdita\API\Middleware\LoggedUserMiddleware;
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
use Chialab\FrontendKit\Authentication\AuthenticationServiceProvider;
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

        if (Configure::read('Status.level') === 'on') {
            // Ensure BEdita to load objects using `published` filter
            Configure::write('Publish.checkDate', true);
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
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            ->add(new StatusMiddleware(['BEdita/Core']))

            // Add routing middleware.
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware());

        if (Configure::read('FrontendPlugin') === 'BEdita/API') {
            return $middlewareQueue
                // Add the AuthenticationMiddleware.
                // It should be after routing and body parser.
                ->add(new AuthenticationMiddleware($this))

                // Setup current BEdita application.
                // It should be after AuthenticationMiddleware.
                ->add(new ApplicationMiddleware([
                    'blockAnonymousApps' => Configure::read('Security.blockAnonymousApps', true),
                ]))

                // Setup current logged user.
                // It should be after AuthenticationMiddleware.
                ->add(new LoggedUserMiddleware())

                // Add the AuthorizationMiddleware *after* routing, body parser
                // and authentication middleware.
                ->add(new AuthorizationMiddleware($this));
        }

        if (Configure::read('StagingSite', false)) {
            $middlewareQueue
                // Add the AuthenticationMiddleware.
                // It should be after routing and body parser.
                ->add(new AuthenticationMiddleware(new AuthenticationServiceProvider('/login')));
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
