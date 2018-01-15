<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testGetDefaultParam()
    {
        $controller   = new \Frogg\Controller();
        $request      = null;
        $defaultValue = 'Default Value';
        $nameGET      = "Test Get";
        $_GET['name'] = $nameGET;

        $controller->request = $request;

        $this->markTestIncomplete(
            'This test has not been implemented, not possible inject [Request]. Phalcon not installed for circle CI'
        );
    }
}