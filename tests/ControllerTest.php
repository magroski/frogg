<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testGetDefaultParam()
    {
        $di           = new \Phalcon\Di\FactoryDefault();
        $controller   = new \Frogg\Controller();
        $request      = new Phalcon\Http\Request();
        $nameGET      = "Test Get";
        $defaultValue = 'Default Value';
        $_GET['name'] = $nameGET;

        $di->set('request', function () use ($request) {
            return $request;
        });
        $controller->setDI($di);

        $this->assertEquals($nameGET, $controller->getParam('name', $defaultValue));
        $this->assertEquals($defaultValue, $controller->getParam('color', $defaultValue));
    }
}