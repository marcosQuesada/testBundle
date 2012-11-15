<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcos
 * Date: 7/11/12
 * Time: 22:57
 * To change this template use File | Settings | File Templates.
 */
namespace ADR\TestBundle\Tests;

use ADR\PoolBundle\Tests\WebTestCase;

Class MainTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testMain()
    {
        //$kernel = self::getKernelClass();

        $container = $this->getContainer();
        $poolHandler = $container->get('adr_pool.hash.handler');

        //$poolHandler->hset('test', 'name', 'okis');
        //var_dump($poolHandler->hget('test', 'name'));
        $router = $container->get('router');
        $routes = $router->getRouteCollection()->all();
        foreach ($routes as $route) {
            var_dump($route->getPattern());


        }

        //var_dump($this->runCommand('router:debug'));
        $this->assertTrue(true);
    }
}