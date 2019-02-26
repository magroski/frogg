<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /** @var \Frogg\Controller */
    private $controller;

    protected function setUp()
    {
        $this->controller             = new \Frogg\Controller();
        $this->controller->request    = new Phalcon\Http\Request();
        $this->controller->dispatcher = new \Phalcon\Mvc\Dispatcher();;
    }

    public function testGetDefaultParam()
    {
        $defaultValue = 'Default Value';
        $nameGET      = "Test Get";
        $_GET['name'] = $nameGET;

        $this->assertEquals($nameGET, $this->controller->getParam('name', $defaultValue));
        $this->assertEquals($defaultValue, $this->controller->getParam('color', $defaultValue));

        $this->assertEquals($nameGET, $this->controller->getDecodedParam('name', $defaultValue));
        $this->assertEquals($defaultValue, $this->controller->getDecodedParam('color', $defaultValue));
    }

    public function testShouldGetIntParam()
    {
        $myValue         = 5;
        $_GET['myParam'] = '5';

        $this->assertEquals($myValue, $this->controller->getIntParam('myParam'));
    }

    public function testShouldGetIntParamWithDefault()
    {
        $defaultValue    = 10;
        $_GET['myParam'] = null;

        $this->assertEquals($defaultValue, $this->controller->getIntParam('myParam', $defaultValue));
    }

    public function testShouldNotGetNullIntParam()
    {
        $_GET['myParam'] = null;

        $this->expectException(TypeError::class);
        $this->controller->getIntParam('myParam');
    }

    public function testShouldNotGetIntParam()
    {
        $_GET['myParam'] = 'pipoca';

        $this->expectException(TypeError::class);
        $this->controller->getIntParam('myParam');
    }

    public function testShouldGetFloatParam()
    {
        $myValue         = 5.1;
        $_GET['myParam'] = '5.1';

        $this->assertEquals($myValue, $this->controller->getFloatParam('myParam'));
    }

    public function testShouldGetFloatParamWithDefault()
    {
        $defaultValue    = 10.1;
        $_GET['myParam'] = null;

        $this->assertEquals($defaultValue, $this->controller->getFloatParam('myParam', $defaultValue));
    }

    public function testShouldNotGetNullFloatParam()
    {
        $_GET['myParam'] = null;

        $this->expectException(TypeError::class);
        $this->controller->getFloatParam('myParam');
    }

    public function testShouldNotGetFloatParam()
    {
        $_GET['myParam'] = 'pipoca';

        $this->expectException(TypeError::class);
        $this->controller->getFloatParam('myParam');
    }

    public function testShouldGetBoolParam()
    {
        $_GET['myParam']  = 'true';
        $_GET['myParam2'] = '1';
        $_GET['myParam3'] = 'on';
        $_GET['myParam4'] = 'false';
        $_GET['myParam5'] = 'off';

        $this->assertTrue($this->controller->getBoolParam('myParam'));
        $this->assertTrue($this->controller->getBoolParam('myParam2'));
        $this->assertTrue($this->controller->getBoolParam('myParam3'));
        $this->assertFalse($this->controller->getBoolParam('myParam4'));
        $this->assertFalse($this->controller->getBoolParam('myParam5'));
    }

    public function testShouldGetBoolParamWithDefault()
    {
        $defaultValue    = true;
        $_GET['myParam'] = null;

        $this->assertEquals($defaultValue, $this->controller->getBoolParam('myParam', $defaultValue));
    }

    public function testShouldNotGetNullBoolParam()
    {
        $_GET['myParam'] = null;

        $this->expectException(TypeError::class);
        $this->controller->getBoolParam('myParam');
    }

    public function testShouldNotGetBoolParam()
    {
        $_GET['myParam'] = 'pipoca';

        $this->expectException(TypeError::class);
        $this->controller->getBoolParam('myParam');
    }

    public function testShouldGetStringParam()
    {
        $myValue         = 'nice';
        $_GET['myParam'] = 'nice';

        $this->assertEquals($myValue, $this->controller->getStringParam('myParam'));
    }

    public function testShouldGetStringParamWithDefault()
    {
        $defaultValue    = 'awesome';
        $_GET['myParam'] = null;

        $this->assertEquals($defaultValue, $this->controller->getStringParam('myParam', $defaultValue));
    }

    public function testShouldNotGetNullStringParam()
    {
        $_GET['myParam'] = null;

        $this->expectException(TypeError::class);
        $this->controller->getStringParam('myParam');
    }

    public function testShouldNotGetStringParam()
    {
        $_GET['myParam'] = 1;

        $this->expectException(TypeError::class);
        $this->controller->getStringParam('myParam');
    }

    public function testShouldGetArrayParam()
    {
        $myValue         = ['a' => 1];
        $_GET['myParam'] = ['a' => 1];

        $this->assertEquals($myValue, $this->controller->getArrayParam('myParam'));
    }

    public function testShouldGetArrayParamWithDefault()
    {
        $defaultValue    = ['b' => 3];
        $_GET['myParam'] = null;

        $this->assertEquals($defaultValue, $this->controller->getArrayParam('myParam', $defaultValue));
    }

    public function testShouldNotGetNullArrayParam()
    {
        $_GET['myParam'] = null;

        $this->expectException(TypeError::class);
        $this->controller->getArrayParam('myParam');
    }

    public function testShouldNotGetArrayParam()
    {
        $_GET['myParam'] = 'not an array';

        $this->expectException(TypeError::class);
        $this->controller->getArrayParam('myParam');
    }

}
