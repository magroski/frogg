<?php

namespace Frogg;

use Detection\MobileDetect;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View as PhalconView;
use TypeError;

/**
 * @property \Phalcon\Mvc\Dispatcher|\Phalcon\Mvc\DispatcherInterface                                  $dispatcher
 * @property \Phalcon\Mvc\Router|\Phalcon\Mvc\RouterInterface                                          $router
 * @property \Phalcon\Url|\Phalcon\Url\UrlInterface                                                $url
 * @property \Phalcon\Http\Request|\Phalcon\Http\RequestInterface                                      $request
 * @property \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface                                    $response
 * @property \Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface                    $cookies
 * @property \Phalcon\Filter|\Phalcon\Filter\FilterInterface                                                  $filter
 * @property \Phalcon\Flash\Direct                                                                     $flash
 * @property \Phalcon\Flash\Session                                                                    $flashSession
 * @property \Phalcon\Session\Adapter\Stream|\Phalcon\Session\Adapter\AbstractAdapter|\Phalcon\Storage\Adapter\AdapterInterface $session
 * @property \Phalcon\Events\Manager|\Phalcon\Events\ManagerInterface                                  $eventsManager
 * @property \Phalcon\Db\Adapter\AdapterInterface                                                              $db
 * @property \Phalcon\Security                                                                         $security
 * @property \Phalcon\Crypt                                                $crypt
 * @property \Phalcon\Tag                                                                              $tag
 * @property \Phalcon\Escaper                                     $escaper
 * @property \Phalcon\Annotations\Adapter\Memory|\Phalcon\Annotations\Adapter\AbstractAdapter                          $annotations
 * @property \Phalcon\Mvc\Model\Manager|\Phalcon\Mvc\Model\ManagerInterface                            $modelsManager
 * @property \Phalcon\Mvc\Model\MetaData\Memory|\Phalcon\Mvc\Model\MetadataInterface                   $modelsMetadata
 * @property \Phalcon\Mvc\Model\Transaction\Manager|\Phalcon\Mvc\Model\Transaction\ManagerInterface    $transactionManager
 * @property \Phalcon\Assets\Manager                                                                   $assets
 * @property \Phalcon\Di|\Phalcon\Di\DiInterface                                                       $di
 * @property \Phalcon\Session\Bag                                        $persistent
 * @property \Phalcon\Mvc\View                                                                         $view
 * @property mixed $auth
 */
class Controller extends PhalconController
{

    public string $unauthorizedUrl = '/';
    public string $authLabel       = 'auth';

    /**
     * @var array<string>
     */
    protected array $publicActions = [];

    /**
     * @param  mixed      $auth
     */
    public function isAllowed(string $actionName, $auth) : bool
    {
        return in_array($actionName, $this->publicActions) || $auth;
    }

    public function isMobile() : bool
    {
        $mobileDetector = new MobileDetect();

        return $mobileDetector->isMobile() && !$mobileDetector->isTablet();
    }

    /**
     * @param mixed $auth
     */
    public function login($auth) : void
    {
        $this->auth = $auth;
    }

    public function getUnauthorizedUrl() : string
    {
        return $this->unauthorizedUrl;
    }

    /**
     * Syntactical sugar for Phalcon's $dispatcher->forward
     *
     * @param array<string>|string $routeInfo Array : Key-value array containing route information [namespace, module,
     *                                controller, action, ...] String : Route url or route name
     * @param array<string,string>        $params    Key-value array containing parameters and values
     */
    public function forward($routeInfo, $params = []) : void
    {
        if (is_string($routeInfo)) {
            $routeInfo = $this->extractRoutePath($routeInfo);
        }
        $requestData           = $routeInfo;
        $requestData['params'] = $params;
        $this->dispatcher->forward($requestData);
    }

    public function redirect(string $url, bool $externalRedirect = false, int $statusCode = 302) : void
    {
        $this->response->redirect($url, $externalRedirect, $statusCode);
        $this->view->disable();
    }

    /**
     * @param array<string,mixed>  $params
     */
    public function partial(string $file, array $params) : string
    {
        $this->view->disableLevel(PhalconView::LEVEL_LAYOUT);

        return $this->view->getRender('partials', $file, $params);
    }

    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
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
     * @param array|float|int|mixed|string|null       $defaultValue
     * @return array|float|int|mixed|string|null
     */
    public function getParam(string $name, $defaultValue = null)
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
     * @param  mixed      $defaultValue
     * @return mixed|null
     */
    public function getDecodedParam(string $name, $defaultValue = null)
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

    public function getIntParam(string $name, ?int $defaultValue = null) : int
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new TypeError('Parameter ' . $name . ' is null, int expected');
        }

        if (!ctype_digit($value)) {
            throw new TypeError('Parameter ' . $name . ' is not an int : ' . $value);
        }

        return intval($value);
    }

    public function getFloatParam(string $name, ?float $defaultValue = null) : float
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new TypeError('Parameter ' . $name . ' is null, float expected');
        }

        $floatVal = floatval($value);
        if ($floatVal && intval($floatVal) !== $floatVal) {
            return $floatVal;
        }

        throw new TypeError('Parameter ' . $name . ' is not a float : ' . $value);
    }

    public function getBoolParam(string $name, ?bool $defaultValue = null) : bool
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new TypeError('Parameter ' . $name . ' is null, bool expected');
        }

        $filteredValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (!is_bool($filteredValue)) {
            throw new TypeError('Parameter ' . $name . ' is not a bool : ' . $value);
        }

        return $filteredValue;
    }

    public function getStringParam(string $name, ?string $defaultValue = null) : string
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new TypeError('Parameter ' . $name . ' is null, string expected');
        }

        if (!is_string($value)) {
            throw new TypeError('Parameter ' . $name . ' is not a string : ' . $value);
        }

        return (string)$value;
    }

    /**
     * @param array<mixed>|null $defaultValue
     * @return array<mixed>
     */
    public function getArrayParam(string $name, ?array $defaultValue = null) : array
    {
        $value = $this->getDecodedParam($name);
        if (is_null($value)) {
            if (!is_null($defaultValue)) {
                return $defaultValue;
            }
            throw new TypeError('Parameter ' . $name . ' is null, array expected');
        }

        if (!is_array($value)) {
            throw new TypeError('Parameter ' . $name . ' is not an array : ' . $value);
        }

        return $value;
    }

    /**
     * Short way to build and return a JSON response
     *
     * @param array|mixed $data Data to be json encoded
     */
    public function setJsonResponse($data = []) : ResponseInterface
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
        $url = (strpos($route, '/') === false) ? $this->url($route) : $route;
        $this->router->handle($url);

        return $this->router->getMatchedRoute() ? $this->router->getMatchedRoute()->getPaths() : null;
    }

    /**
     * @param array<string>  $params
     * @param array<string,string>  $query
     */
    private function url(string $routeName, array $params = [], array $query = []) : string
    {
        $di     = \Phalcon\Di\Di::getDefault();
        if ($di=== null) {
            throw new \RuntimeException('Container does not exist');
        }
        $params = array_merge(['for' => $routeName], $params);
        $url    = $di->get('url');
        $url->setBaseUri('/');

        return $url->get($params, $query);
    }

    /**
     * Remove BOM of UTF8 string
     */
    protected function utf8WithoutBom(?string $string) : ?string
    {
        if ($string === null) {
            return null;
        }

        $bom = pack('H*', 'EFBBBF');

        $string = str_replace($bom, '', $string);

        return $string;
    }

}
