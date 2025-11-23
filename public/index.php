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

$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];


$base_path = dirname($script_name);
if ($base_path != '/') {
    $request_uri = str_replace($base_path, '', $request_uri);
}


$request_uri = parse_url($request_uri, PHP_URL_PATH);
$request_uri = trim($request_uri, '/');

$request_url = $request_uri;

$method = $_SERVER['REQUEST_METHOD'];

require_once(__DIR__ . '/../controllers/AuthController.php');
require_once(__DIR__ . '/../controllers/UserController.php');
require_once(__DIR__ . '/../controllers/PostController.php');
require_once(__DIR__ . '/../controllers/LikeController.php');
require_once(__DIR__ . '/../controllers/CommentController.php');
require_once(__DIR__ . '/../controllers/FavoriteController.php');

$routes = [
    'auth/login' => ['POST' => 'AuthController@login'],
    'auth/register' => ['POST' => 'AuthController@register'],

    'user/profile' => [
        'POST' => 'UserController@updateProfile',
    ],

    'post/update' => ['POST' => 'PostController@update'],

    'post/delete' => ['POST' => 'PostController@delete'],

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

    'favorites/create' => [
        'POST' => 'FavoriteController@newFavorite'
    ],

    'favorites/get' => [
        'GET' => 'FavoriteController@getFavorite'
    ],

     'favorites/delete' => [
        'POST' => 'FavoriteController@eliminarFavorito'
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
        "success" => false,
        "message" => "Endpoint no encontrado: " . $request_url,
        "method" => $method,
        "available_routes" => array_keys($routes)
    ]);
}
