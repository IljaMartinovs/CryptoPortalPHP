<?php

require_once "../vendor/autoload.php";

session_start();

use App\Controllers\CryptoCurrencyController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\ProfileController;
use App\Controllers\RegistrationController;
use App\Redirect;
use App\View;
use App\ViewVariables\AuthViewVariables;
use App\ViewVariables\ErrorsViewVariables;
use App\ViewVariables\MyCryptoViewVariables;
use App\ViewVariables\SuccessViewVariables;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

$loader = new FilesystemLoader('views');
$twig = new Environment($loader);

$viewVariables = [
    AuthViewVariables::class,
    ErrorsViewVariables::class,
    SuccessViewVariables::class,
    MyCryptoViewVariables::class
];
foreach ($viewVariables as $variable) {
    $variable = new $variable;
    $twig->addGlobal($variable->getName(), $variable->getValue());
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/', [CryptoCurrencyController::class, 'index']);
    $route->addRoute('POST', '/buy', [CryptoCurrencyController::class, 'buy']);
    $route->addRoute('POST', '/sell', [CryptoCurrencyController::class, 'sell']);
    $route->addRoute('GET', '/login', [LoginController::class, 'show']);
    $route->addRoute('POST', '/login', [LoginController::class, 'store']);
    $route->addRoute('GET', '/registration', [RegistrationController::class, 'show']);
    $route->addRoute('POST', '/registration', [RegistrationController::class, 'store']);
    $route->addRoute('GET', '/logout', [LogoutController::class, 'logout']);
    $route->addRoute('GET', '/profile', [ProfileController::class, 'show']);
    $route->addRoute('POST', '/deposit', [ProfileController::class, 'addMoney']);
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
        $response = (new $controller)->{$method}();
        if ($response instanceof View) {
            echo $twig->render($response->getPath(), $response->getData());
            unset($_SESSION['errors']);
            unset($_SESSION['success']);
        }

        if ($response instanceof Redirect) {
            header('Location: ' . $response->getUrl());
        }
        break;
}

// ispeja parsutit citam profilam