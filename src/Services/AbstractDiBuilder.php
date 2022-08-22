<?php

namespace Frogg\Services;

use Phalcon\Di\FactoryDefault;

class AbstractDiBuilder extends FactoryDefault
{
    /**
     * AbstractService constructor.
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->setShared('config', $config);
        $this->bindServices();
    }

    /**
     *  Register services in di, all methods with prefix [init, initShared]
     */
    protected function bindServices()
    {
        $reflection = new \ReflectionObject($this);
        $methods    = $reflection->getMethods();
        foreach ($methods as $method) {
            if ((strlen($method->name) > 10) && (strpos($method->name, 'initShared') === 0)) {
                $this->setShared(lcfirst(substr($method->name, 10)), $method->getClosure($this));
                continue;
            }
            if ((strlen($method->name) > 4) && (strpos($method->name, 'init') === 0)) {
                $this->set(lcfirst(substr($method->name, 4)), $method->getClosure($this));
            }
        }
    }
}
