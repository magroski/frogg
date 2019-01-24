<?php

namespace Frogg;

use Detection\MobileDetect;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View as PhalconView;

/**
 * @property \Phalcon\Mvc\Dispatcher|\Phalcon\Mvc\DispatcherInterface                                  $dispatcher
 * @property \Phalcon\Mvc\Router|\Phalcon\Mvc\RouterInterface                                          $router
 * @property \Phalcon\Mvc\Url|\Phalcon\Mvc\UrlInterface                                                $url
 * @property \Phalcon\Http\Request|\Phalcon\Http\RequestInterface                                      $request
 * @property \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface                                    $response
 * @property \Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface                    $cookies
 * @property \Phalcon\Filter|\Phalcon\FilterInterface                                                  $filter
 * @property \Phalcon\Flash\Direct                                                                     $flash
 * @property \Phalcon\Flash\Session                                                                    $flashSession
 * @property \Phalcon\Session\Adapter\Files|\Phalcon\Session\Adapter|\Phalcon\Session\AdapterInterface $session
 * @property \Phalcon\Events\Manager|\Phalcon\Events\ManagerInterface                                  $eventsManager
 * @property \Phalcon\Db\AdapterInterface                                                              $db
 * @property \Phalcon\Security                                                                         $security
 * @property \Phalcon\Crypt|\Phalcon\CryptInterface                                                    $crypt
 * @property \Phalcon\Tag                                                                              $tag
 * @property \Phalcon\Escaper|\Phalcon\EscaperInterface                                                $escaper
 * @property \Phalcon\Annotations\Adapter\Memory|\Phalcon\Annotations\Adapter                          $annotations
 * @property \Phalcon\Mvc\Model\Manager|\Phalcon\Mvc\Model\ManagerInterface                            $modelsManager
 * @property \Phalcon\Mvc\Model\MetaData\Memory|\Phalcon\Mvc\Model\MetadataInterface                   $modelsMetadata
 * @property \Phalcon\Mvc\Model\Transaction\Manager|\Phalcon\Mvc\Model\Transaction\ManagerInterface
 *           $transactionManager
 * @property \Phalcon\Assets\Manager                                                                   $assets
 * @property \Phalcon\Di|\Phalcon\DiInterface                                                          $di
 * @property \Phalcon\Session\Bag|\Phalcon\Session\BagInterface                                        $persistent
 * @property \Phalcon\Mvc\View|\Phalcon\Mvc\ViewInterface                                              $view
 */
class Controller extends PhalconController
{

    public $unauthorizedUrl = '/';
    public $authLabel       = 'auth';

    protected $publicActions = [];

    public function isAllowed($actionName, $auth)
    {
        return in_array($actionName, $this->publicActions) || $auth;
    }

    public function isMobile() : bool
    {
        $mobileDetector = new MobileDetect();

        return $mobileDetector->isMobile() && !$mobileDetector->isTablet();
    }

    public function login($auth)
    {
        $this->auth = $auth;
    }

    public function getUnauthorizedUrl()
    {
        return $this->unauthorizedUrl;
    }

    /**
     * Syntactic sugar for Phalcon's $dispatcher->forward
     *
     * @param array|string $routeInfo Array : Key-value array containing route information [namespace, module,
     *                                controller, action, ...] String : Route url or route name
     * @param array        $params    Key-value array containing parameters and values
     */
    public function forward($routeInfo, $params = [])
    {
        if (is_string($routeInfo)) {
            $routeInfo = $this->extractRoutePath($routeInfo);
        }
        $requestData           = $routeInfo;
        $requestData['params'] = $params;
        $this->dispatcher->forward($requestData);
    }

    public function redirect($url)
    {
        $this->response->redirect($url);
        $this->view->disable();
    }

    public function partial($file, $params)
    {
        $this->view->disableLevel(PhalconView::LEVEL_LAYOUT);

        return $this->view->getRender('partials', $file, $params);
    }

    private function encodedArray(array $array) : array
    {
        $encodedArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $encodedArray[$key] = $this->encodedArray($value);
            } else {
                if ($value === null || is_numeric($value)) {
                    $encodedArray[$key] = $value;
                } else {
                    $encodedArray[$key] = $this->utf8WithoutBom(htmlspecialchars($value));
                }
            }
        }

        return $encodedArray;
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getParam($name, $defaultValue = null)
    {
        $encodedParam = null;
        if ($this->dispatcher->hasParam($name)) {
            $encodedParam = $this->dispatcher->getParam($name);
        }
        if ($this->request->has($name)) {
            $encodedParam = $this->request->get($name);
        }
        if ($this->request->hasPost($name)) {
            $encodedParam = $this->request->getPost($name);
        }
        if ($this->request->hasPut($name)) {
            $encodedParam = $this->request->getPut($name);
        }
        if ($this->request->hasQuery($name)) {
            $encodedParam = $this->request->getQuery($name);
        }

        if ($encodedParam === null) {
            return $defaultValue;
        }

        if (is_array($encodedParam)) {
            $encodedParam = $this->encodedArray($encodedParam);
        }

        if (is_numeric($encodedParam) || is_array($encodedParam)) {
            return $encodedParam;
        }

        return $this->utf8WithoutBom(htmlspecialchars($encodedParam));
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getDecodedParam($name, $defaultValue = null)
    {
        $decodedParam = null;
        if ($this->dispatcher->hasParam($name)) {
            $decodedParam = $this->dispatcher->getParam($name);
        }
        if ($this->request->has($name)) {
            $decodedParam = $this->request->get($name);
        }
        if ($this->request->hasPost($name)) {
            $decodedParam = $this->request->getPost($name);
        }
        if ($this->request->hasPut($name)) {
            $decodedParam = $this->request->getPut($name);
        }
        if ($this->request->hasQuery($name)) {
            $decodedParam = $this->request->getQuery($name);
        }

        return $decodedParam ? $decodedParam : $defaultValue;
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getIntParam(string $name, ?int $defaultValue = null) : int
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new \TypeError('Parameter ' . $name . ' is null, int expected');
        }

        if (!ctype_digit($value)) {
            throw new \TypeError('Parameter ' . $name . ' is not an int : ' . $value);
        }

        return intval($value);
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getFloatParam(string $name, ?float $defaultValue = null) : float
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new \TypeError('Parameter ' . $name . ' is null, float expected');
        }

        $floatVal = floatval($value);
        if ($floatVal && intval($floatVal) !== $floatVal) {
            return $floatVal;
        }

        throw new \TypeError('Parameter ' . $name . ' is not a float : ' . $value);
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getBoolParam(string $name, ?bool $defaultValue = null) : bool
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new \TypeError('Parameter ' . $name . ' is null, bool expected');
        }

        $filteredValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($filteredValue)) {
            throw new \TypeError('Parameter ' . $name . ' is not a bool : ' . $value);
        }

        return $filteredValue;
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getStringParam(string $name, ?string $defaultValue = null) : string
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new \TypeError('Parameter ' . $name . ' is null, string expected');
        }

        if (!is_string($value)) {
            throw new \TypeError('Parameter ' . $name . ' is not a string : ' . $value);
        }

        return (string)$value;
    }

    /**
     * @deprecated use \Psr\Http\Message\ServerRequestInterface
     */
    public function getArrayParam(string $name, ?array $defaultValue = null) : array
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new \TypeError('Parameter ' . $name . ' is null, array expected');
        }

        if (!is_array($value)) {
            throw new \TypeError('Parameter ' . $name . ' is not an array : ' . $value);
        }

        return $value;
    }

    /**
     * Short way to build and return a JSON response
     *
     * @param array|mixed $data Data to be json encoded
     *
     * @return Response
     * @deprecated use \Psr\Http\Message\ResponseInterface
     */
    public function setJsonResponse($data = []) : Response
    {
        $response = new Response();

        return $response->setJsonContent($data, JSON_NUMERIC_CHECK);
    }

    /**
     * Extract path data (namespace, controller, action) from a given route name.
     *
     * @param string $route
     *
     * @return mixed
     */
    public function extractRoutePath(string $route)
    {
        $url = (strpos($route, '/') === false) ? url($route) : $route;
        $this->router->handle($url);

        return $this->router->getMatchedRoute()->getPaths();
    }

    /**
     * Remove BOM of UTF8 string
     *
     * @param string $string
     *
     * @return string
     */
    protected function utf8WithoutBom($string)
    {
        if ($string === null) {
            return null;
        }

        $bom = pack('H*', 'EFBBBF');

        $string = str_replace($bom, '', $string);

        return $string;
    }
}
