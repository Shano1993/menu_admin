<?php

namespace App;

use App\Controller\Controller;
use App\Controller\ErrorController;
use ReflectionException;
use ReflectionMethod;

class Router
{
    /**
     * @throws ReflectionException
     */
    public static function Route()
    {
        $strController = self::getParam('c', 'home');
        $method = self::getParam('a');
        $controller = self::guessController($strController);

        if ($controller instanceof ErrorController) {
            $controller->error404();
            exit();
        }

        $method = self::guessMethod($controller, $method);
        if (null === $method) {
            $controller->index();
        }
        else {
            $params = self::guessParam($controller, $method);
            if (count($params) === 0) {
                $controller->$method();
            }
            else {
                $parameters = [];
                foreach ($params as $param) {
                    $get = $_GET[$param['paramName']];
                    $parameters[] = $get;
                }
                $controller->$method(...$parameters);
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private static function guessParam(Controller $controller, string $method): array
    {
        $paramsArray = [];
        $reflexion = new ReflectionMethod($controller, $method);
        $parameters = $reflexion->getParameters();
        foreach ($parameters as $parameter) {
            $paramsArray[] = [
                'paramName' => $parameter->name,
                'paramType' => $parameter->getType(),
            ];
        }
        return $paramsArray;
    }

    private static function guessMethod(Controller $controller, ?string $method): ?string
    {
        if (strpos($method, '-') !== 1) {
            $method = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $method))));
        }
        $method = lcfirst($method);
        return method_exists($controller, $method) ? $method : null;
    }

    private static function guessController(string $controller)
    {
        $controller = "App\Controller\\" . ucfirst($controller) . "Controller";
        return class_exists($controller) ? new $controller : new ErrorController();
    }

    private static function getParam(string $param, $default = null): ?string
    {
        if (isset($_GET[$param])) {
            return filter_var($_GET[$param], FILTER_SANITIZE_STRING);
        }
        return $default;
    }
}