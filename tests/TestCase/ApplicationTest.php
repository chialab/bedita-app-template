<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase;

use App\Application;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Core\PluginInterface;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Chialab\FrontendKit\Middleware\ExceptionWrapperMiddleware;
use Chialab\FrontendKit\Middleware\StatusMiddleware;

/**
 * Test {@see \App\Application} class.
 *
 * @coversDefaultClass \App\Application
 */
class ApplicationTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Plugin::getCollection()->clear();
    }

    /**
     * Test {@see Application::bootstrap()} method with debug enabled and no frontend plugin enabled.
     *
     * @return void
     * @covers ::bootstrap()
     */
    public function testBootstrapInDebug()
    {
        Configure::write('debug', true);
        Configure::write('FrontendPlugin', null);

        $expectedPlugins = [
            'Bake',
            'BEdita/AWS',
            'BEdita/Core',
            'BEdita/DevTools',
            'BEdita/I18n',
            'Chialab/FrontendKit',
            'DebugKit',
            'Migrations',
        ];

        $app = new Application(dirname(dirname(__DIR__)) . '/config');

        static::assertCount(0, $app->getPlugins());
        $app->bootstrap();
        $plugins = $app->getPlugins();

        static::assertSameSize($expectedPlugins, $plugins);
        foreach ($expectedPlugins as $plugin) {
            static::assertInstanceOf(PluginInterface::class, $plugins->get($plugin));
            static::assertSame($plugin, $plugins->get($plugin)->getName());
        }
    }

    /**
     * Test {@see Application::bootstrap()} method with debug disabled and `BEdita/API` as frontend plugin.
     *
     * @return void
     * @covers ::bootstrap()
     */
    public function testBootstrap()
    {
        Configure::write('debug', false);
        Configure::write('FrontendPlugin', 'BEdita/API');

        $expectedPlugins = [
            'Bake',
            'BEdita/API',
            'BEdita/AWS',
            'BEdita/Core',
            'BEdita/I18n',
            'Chialab/FrontendKit',
            'Migrations',
        ];

        $app = new Application(dirname(dirname(__DIR__)) . '/config');

        static::assertCount(0, $app->getPlugins());
        $app->bootstrap();
        $plugins = $app->getPlugins();

        static::assertSameSize($expectedPlugins, $plugins);
        foreach ($expectedPlugins as $plugin) {
            static::assertInstanceOf(PluginInterface::class, $plugins->get($plugin));
            static::assertSame($plugin, $plugins->get($plugin)->getName());
        }
    }

    /**
     * Test {@see Application::middleware()} method.
     *
     * @return void
     * @covers ::middleware()
     */
    public function testMiddleware()
    {
        Configure::write('debug', false);
        Configure::write('FrontendPlugin', 'BEdita/App');

        $expectedMiddlewares = [
            ErrorHandlerMiddleware::class,
            ExceptionWrapperMiddleware::class,
            AssetMiddleware::class,
            StatusMiddleware::class,
            RoutingMiddleware::class,
            BodyParserMiddleware::class,
            CsrfProtectionMiddleware::class,
        ];

        $app = new Application(dirname(dirname(__DIR__)) . '/config');

        $middlewareQueue = $app->middleware(new MiddlewareQueue());

        static::assertSameSize($expectedMiddlewares, $middlewareQueue);
        foreach ($middlewareQueue as $i => $middleware) {
            static::assertInstanceOf($expectedMiddlewares[$i], $middleware);
        }
    }
}
