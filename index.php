<?php

require_once "vendor/autoload.php";
session_start();

use App\Template;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//$loader = new FilesystemLoader('public/views');
//$twig = new Environment($loader);
//$twig->addGlobal('session', $_SESSION);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/', ['App\Controllers\CryptoController', 'index']);
    $route->addRoute('GET', '/login', ['App\Controllers\LoginController', 'show']);
    $route->addRoute('POST', '/login', ['App\Controllers\LoginController', 'store']);
    $route->addRoute('GET', '/registration', ['App\Controllers\RegistrationController', 'show']);
    $route->addRoute('POST', '/registration', ['App\Controllers\RegistrationController', 'store']);
    $route->addRoute('GET', '/logout', ['App\Controllers\LogoutController', 'logout']);
    $route->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'show']);
//    $route->addRoute('GET', '/edit', ['App\Controllers\EditController', 'show']);
//    $route->addRoute('POST', '/edit', ['App\Controllers\EditController', 'store']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        //404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        //405
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = $handler;
        $info = (new $controller)->{$method}();
        if ($info instanceof Template) {
            $info->render();
            unset($_SESSION['error']);
        }
        break;
}
