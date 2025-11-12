<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$request_url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];


require_once(__DIR__ . '/controllers/AuthController.php');
require_once(__DIR__ . '/controllers/UserController.php');
require_once(__DIR__ . '/controllers/PostController.php');
require_once(__DIR__ . '/controllers/LikeController.php');
require_once(__DIR__ . '/controllers/CommentController.php');
require_once(__DIR__ . '/controllers/FavoriteController.php');


$routes = [
   
    'auth/login' => ['POST' => 'AuthController@login'],
    'auth/register' => ['POST' => 'AuthController@register'],
 
    'user/profile' => [
        'GET' => 'UserController@getProfile',
        'POST' => 'UserController@updateProfile',
    ],

    'user/password' => [
        'PUT' => 'UserController@actualizarContrasena'
    ],
    
    'post' => [
        'POST' => 'PostController@create',
        'GET' => 'PostController@getpost'
    ],

    'likes/toggle' => ['POST' => 'LikeController@toggle'],
    'likes/check' => ['GET' => 'LikeController@checkLike'],

    'comments' => [
        'POST' => 'CommentController@newcomment',
        'GET' => 'CommentController@getComments'
    ],

    'favorites' => [
        'POST' => 'FavoriteController@newFavorite',
        'GET' => 'FavoriteController@getFavorite'
    ]
];

$routeFound = false;
foreach ($routes as $route => $methods) {
    if (strpos($request_url, $route) === 0) {
        if (isset($methods[$method])) {
            $handler = $methods[$method];
            list($controllerName, $methodName) = explode('@', $handler);

            $controller = new $controllerName();
            $controller->$methodName();

            $routeFound = true;
            break;
        }
    }
}


if (!$routeFound) {
    http_response_code(404);
    echo json_encode([
        "message" => "Endpoint no encontrado"
    ]);
}
