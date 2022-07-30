<?php
class TinyPHP_Router
{

    //put your code here

    private static $instance;
    private $routes = array();
    private $matchedRouteId = "";
    private $matchedRoute;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function addRoute($routeId, $route)
    {
        $this->routes[$routeId] = $route;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    public function matchRoute($uriParts = array())
    {


        foreach ($this->routes as $id => $route) {

            if ($route->getRouteType() == 'static') {
                $pattern = $route->getRoutePattern();
                $pattern = $this->removeEndSlashes($pattern);
                $patternParts = explode("/", $pattern);
                $arrLength = count($patternParts) - 1;
                if ("*" == $patternParts[$arrLength]) {
                    array_pop($patternParts);
                    $ignoreLength = true;
                } else {
                    $ignoreLength = false;
                }

                if ($ignoreLength || (count($patternParts) == count($uriParts))) {

                    $matched = true;

                    if (is_array($uriParts) && is_array($patternParts)) {
                        foreach ($patternParts as $key => $val) {
                            if (strpos($val, ":", 0) === false) { #static url part
                                if (array_key_exists($key, $uriParts)) {
                                    if ($uriParts[$key] != $val) {
                                        $matched = false;
                                    }
                                }
                            }
                        }
                    }

                    if ($matched == true) {
                        $this->matchedRoute = $route;
                        $this->matchedRouteId = $id;

                        break;
                    }
                }

            } else //handle regex route
            {
                $pattern = $route->getRoutePattern();
                $uri =   implode("/", $uriParts);
                $matches = array();



                $matchcount = preg_match('/^' . $pattern . '$/', $uri, $matches);

               /* echo "<pre>";
                print_r($matches);
                echo "</pre>";*/

                if ($matchcount > 0) {
                    $this->matchedRoute = $route;
                    $this->matchedRouteId = $id;

                        break;
                }


            }
        }

       /* echo "<pre>";
        print_r($this->matchedRoute);
        echo "</pre>";*/
        if (is_object($this->matchedRoute)) {
            return $this->matchedRoute;
        } else {
            return FALSE;
        }
    }

    public function convertRoute($route, $request)
    {
        $handler = $route->getRouteHandler();

        if (array_key_exists('module', $handler)) {
            $request->setModuleName($handler['module']);
        }

        if (array_key_exists('controller', $handler)) {
            $request->setControllerName($handler['controller']);
        }

        if (array_key_exists('action', $handler)) {
            $request->setActionName($handler['action']);
        }

        $routeParams = array();
        if (array_key_exists('params', $handler)) {
            $routeParams = $handler['params'];
        }
        $params = $routeParams;

        if($route->getRouteType() == 'static')
        {
            $pattern = $route->getRoutePattern();
            $pattern = $this->removeEndSlashes($pattern);
            $patternParts = explode("/", $pattern);
            $uriParts = $request->getURIParts();
            if ((is_array($uriParts) && is_array($patternParts)) && (count($patternParts) == count($uriParts))) {
                foreach ($patternParts as $key => $val) {
                    if (strpos($val, ":", 0) !== false) {
                        if (array_key_exists($key, $uriParts)) {
                            $params[str_replace(":", "", $val)] = $uriParts[$key];
                        }

                    }
                }
            }

        }
        else
        {
            $pattern = $route->getRoutePattern();
            $uriParts = $request->getURIParts();
            $uri =   implode("/", $uriParts);
            $matches = array();

            if (array_key_exists('regex_params', $handler)) {
                $regex_params = $handler['regex_params'];
            }

            $matchcount = preg_match('/^' . $pattern . '$/', $uri, $matches);
            if ($matchcount > 0) {
                foreach($matches as $key=>$val)
                {
                    if(array_key_exists($key,$regex_params))
                    {
                        $params[$regex_params[$key]]= $val;
                    }
                }
            }
        }

        $request->setParams($params);
    }

    private function removeEndSlashes($_path)
    {
        $path = $_path;
        if ($path != "") {
            if (substr($path, 0, 1) == "/") {
                $path = substr($path, 1, strlen($path));
            }
            if (substr($path, strlen($path) - 1, 1) == "/") {
                $path = substr($path, 0, strlen($path) - 1);
            }
        }
        return $path;
    }

}

?>
