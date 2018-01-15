<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testGetDefaultParam()
    {
        $controller   = new \Frogg\Controller();
        $dispatcher   = new \Phalcon\Mvc\Dispatcher();
        $request      = new Phalcon\Http\Request();
        $defaultValue = 'Default Value';
        $nameGET      = "Test Get";
        $_GET['name'] = $nameGET;

        $controller->request    = $request;
        $controller->dispatcher = $dispatcher;

        $this->assertEquals($nameGET, $controller->getParam('name', $defaultValue));
        $this->assertEquals($defaultValue, $controller->getParam('color', $defaultValue));
    }
}