<?php
/**
 * Matches the request URI against config/routes.php and calls
 * {Name}Controller::action{Action}($params).
 */
class Router
{

    private function getURI()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return trim(rawurldecode($uri), '/');
    }

	private function includeController($controllerClassName, $actionMethodName, $parameters)
    {
    	$pathControllerFile = ROOT . Config::getdirs('controllers') . '/' . $controllerClassName . '.php';

        if (!is_file($pathControllerFile))
        {
            throw new Exception("Controller file $controllerClassName.php is not found");
        }

        require_once $pathControllerFile;
        $controllerObject = new $controllerClassName;

        if (!method_exists($controllerObject, $actionMethodName))
        {
            $this->notFound();
            return;
        }

        $controllerObject->$actionMethodName($parameters);
    }

    private function notFound()
    {
        http_response_code(404);
        include ROOT . '/error.php';
    }

    public function run()
    {
        $uri = $this->getURI();

        Csrf::catch();

        foreach (Config::getroutes() as $uriPattern => $path)
        {
            if (preg_match('#^' . $uriPattern . '$#u', $uri))
            {
                $internalRoute = preg_replace('#^' . $uriPattern . '$#u', $path, $uri);
                $segments = explode('/', $internalRoute);

                $controllerClassName = ucfirst(array_shift($segments)) . 'Controller';
                $actionMethodName = 'action' . ucfirst(array_shift($segments));

                $this->includeController($controllerClassName, $actionMethodName, $segments);
                return;
            }
        }

        $this->notFound();
    }

}
