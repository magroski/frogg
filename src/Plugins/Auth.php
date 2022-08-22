<?php

namespace Frogg\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;

/**
 * @property \Phalcon\Session\ManagerInterface $session
 */
class Auth extends Injectable
{

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        /** @var \Frogg\Controller $controller */
        $controller = $dispatcher->getActiveController();
        $actionName = $dispatcher->getActionName();
        $auth       = $this->session->get($controller->authLabel);

        if (!$controller->isAllowed($actionName, $auth)) {
            $controller->redirect($controller->getUnauthorizedUrl());

            return false;
        }

        $controller->login($auth);
    }
}
