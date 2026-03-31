<?php
// Fichier : Backend/public/index.php
// FRONT CONTROLLER : Point d'entrée unique de l'API MVC

// 1. Démarrage de session
session_start();

// 2. Headers CORS globaux
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Inclusion des classes globales (Base de données, etc.)
require_once __DIR__ . '/../../config/Database.php';

// 4. Chargement des routes
$routes = require_once __DIR__ . '/../route/api.php';

// 5. Routage
$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'] ?? 'home';

// Page d'accueil de l'API pour les tests
if ($route === 'home' || $route === '') {
    http_response_code(200);
    echo json_encode([
        'status' => 'success', 
        'message' => 'Bienvenue sur l\'API de CampusConnect ! (Architecture MVC Active 🚀)',
        'version' => '1.0'
    ]);
    exit();
}

if (isset($routes[$method][$route])) {
    $handler = $routes[$method][$route];
    list($controllerName, $action) = explode('@', $handler);

    $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        // Instancier le contrôleur
        $controller = new $controllerName();
        
        // Appeler la méthode de l'action
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => "La méthode '$action' n'existe pas dans le contrôleur '$controllerName'."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => "Le contrôleur '$controllerName' n'a pas été trouvé."]);
    }
} else {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => "La route '$route' (Méthode: $method) n'existe pas."]);
}
