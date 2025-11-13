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

// =============================================
// NUEVO: MANEJO DE RUTAS PARA RAILWAY
// =============================================

// Obtener la ruta solicitada (sin .htaccess)
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remover el base path si existe
$base_path = dirname($script_name);
if ($base_path != '/') {
    $request_uri = str_replace($base_path, '', $request_uri);
}

// Limpiar la URI
$request_uri = parse_url($request_uri, PHP_URL_PATH);
$request_uri = trim($request_uri, '/');

// Si es la raíz, mostrar estado
if ($request_uri === '' || $request_uri === 'public' || $request_uri === 'public/') {
    echo json_encode([
        "status" => "success",
        "message" => "API funcionando en Railway",
        "timestamp" => date('Y-m-d H:i:s'),
        "endpoints" => [
            "POST /auth/login" => "Iniciar sesión",
            "POST /auth/register" => "Registrar usuario",
            "GET|POST /user/profile" => "Perfil de usuario",
            "PUT /user/password" => "Actualizar contraseña",
            "POST|GET /post" => "Gestión de posts",
            "POST /likes/toggle" => "Toggle like",
            "GET /likes/check" => "Verificar like",
            "POST|GET /comments" => "Gestión de comentarios",
            "POST|GET /favorites" => "Gestión de favoritos"
        ]
    ]);
    exit();
}

// Asignar la ruta para el enrutador
$request_url = $request_uri;

$method = $_SERVER['REQUEST_METHOD'];

// Incluir controladores con rutas absolutas
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
        "success" => false,
        "message" => "Endpoint no encontrado: " . $request_url,
        "method" => $method,
        "available_routes" => array_keys($routes)
    ]);
}
?>
