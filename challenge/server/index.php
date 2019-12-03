<?php

ini_set('display_errors', 0);
error_reporting(null);
session_start();

// ---------
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$slimConfig = [
    'settings' => [
        'displayErrorDetails' => false,
    ],
];
$container = new \Slim\Container($slimConfig);
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('html');

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

$postlog = file_get_contents('/var/log/apache2/post.log');
file_put_contents(
    '/var/log/apache2/post.log', 
    $postlog."\n\n---".
    uniqid()."---\n".
    json_encode($_SERVER) . "\n".
    json_encode($_POST)."\n".
    json_encode(file_get_contents('php://input'))."\n".
    json_encode($_GET));

$app = new \Slim\App($container);

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('X-Powered-By', base64_decode('VW4gTGFtYSBub21tw6kgSmVhbi1DbGF1ZGU='))
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/client/vortex/{source}/{device}/{build}/{options}', function (Request $request, Response $response, array $args) {
    $args['options'] = json_decode($args['options'], true);
    $args['redirect'] = true;
    $args['from'] = $_SERVER['REMOTE_ADDR'];
    $args['@timestamp'] = Date('Y-m-d\TH:i:s');
    return $response->withRedirect('/client/handler/'.base64_encode(json_encode($args)));
});

$app->get('/client/handler/{b64}', function (Request $request, Response $response, array $args) {
    $config = json_decode(file_get_contents('config.json'), true);
    $status = 0;
    if($_SERVER['HTTP_USER_AGENT'] === $config['vortex']['authorized_ua']) {
        $status = file_put_contents('./status/'.uniqid(true).'.json', base64_decode(json_encode($args['b64'])));
    } else {
        return $response->withJson([401 => "Not Authorized"], 401);
    }
    return $response->withRedirect('/client/_status/'.$status);
});

$app->get('/client/_status/{status}', function (Request $request, Response $response, array $args) {
    return $response->withJson(['processed' => ($args['status'] > 0)]);
});

$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->withJson([401 => "Not Authorized"], 401);
});

$app->get('/client_update', function (Request $request, Response $response, array $args) {

    $id = $_SERVER['REMOTE_ADDR'].'|'.$_SERVER['HTTP_USER_AGENT'];
    $knock = json_decode(file_get_contents('./cu/knock.json'), true);
    $knock[$id] += 1;
    file_put_contents('./cu/knock.json', json_encode($knock));

    if($knock[$id] % 3 !== 0) {
        return $response->withJson([404 => "Not Found"], 404);
    }

    $files = array_diff(scandir('./cu/repo'), array('.', '..'));
    foreach($files as $file) {
        $items[$file] = base64_encode(file_get_contents('./cu/repo/'.$file));
    }

    return $response->withJson(['files' => $items, 'knock' => $knock], 200);
});

$app->get('/_emf_tf_admin', function ($request, $response, $args) {

    $name = null;
    $config = json_decode(file_get_contents('config.json'), true);

    $isAuthorized = false;

    if(isset($_SESSION['uid'])) {
        foreach($config['admin']['users'] as $user) {
            if($user['uid'] == $_SESSION['uid']) {
                $name = $user['username'];
                $isAuthorized = true;
            }
        } 
    }

    if(!$isAuthorized) {
        return $response->withRedirect('/_emf_tf_admin/login');
    }

    $files = array_diff(scandir('./status'), array('.', '..'));
    foreach($files as $file) {
        $items[] = json_encode(json_decode(file_get_contents('./status/'.$file)), JSON_PRETTY_PRINT);
    }

    return $this->view->render($response, 'admin.html', [
        'name' => $name,
        'items' => array_reverse($items)
    ]); 
})->setName('admin');

$app->get('/_emf_tf_admin/logout', function ($request, $response, $args) {
   unset($_SESSION['uid']);
   return $response->withRedirect('/_emf_tf_admin');
})->setName('admin-logout');

$app->any('/_emf_tf_admin/login', function ($request, $response, $args) {
    sleep(rand(1, 4));
    $config = json_decode(file_get_contents('config.json'), true);
    $errors = [];
    if(isset($_POST['username']) && isset($_POST['password'])) {
        foreach($config['admin']['users'] as $user) {
            $errors[] = json_encode($user);
            $errors[] = ($user['username'] == $_POST['username']);
            $errors[] = ($user['password'] == $_POST['password']);
            $errors[] = json_encode($_POST);
            if($user['username'] == $_POST['username'] && $user['password'] == $_POST['password']) {
                $_SESSION['uid'] = $user['uid'];
                return $response->withRedirect('/_emf_tf_admin');
            }
        } 
        $errors = ['username and/or password not valid'];
    }

    return $this->view->render($response, 'login.html', [
        'errors' => $errors
    ]);
 })->setName('admin-login');

$app->run();