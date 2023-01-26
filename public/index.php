<?php

use RedBeanPHP\R;
use App\Router;
use Symfony\Component\ErrorHandler\Debug;

require_once '../vendor/autoload.php';
require __DIR__ . '/../Router.php';

Debug::enable();

R::setup('mysql:host=localhost;dbname=menu-admin', 'root', '');

try {
    Router::route();
}
catch (ReflectionException $e) {

}