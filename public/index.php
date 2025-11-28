<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
RateLimit::check($clientIP, 500, 3600);

$authController = new AuthController();
if (!in_array($_SERVER['REQUEST_URI'], ['/login.php', '/logout.php'], true)) {
    $authController->checkAuth();
}

$request = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$parts = array_values(array_filter(explode('/', trim($request, '/'))));
$controller = $parts[0] ?? 'dashboard';
$action = $parts[1] ?? 'index';
$id = isset($parts[2]) ? (int) $parts[2] : null;

if ($controller === 'api' && $parts[1] === 'notifications' && ($parts[2] ?? '') === 'count') {
    header('Content-Type: application/json');
    $notificationModel = new Notification();
    $count = $notificationModel->getUnreadCount($_SESSION['user_id']);
    echo json_encode(['count' => $count]);
    exit;
}

switch ($controller) {
    case 'dashboard':
        $user = getCurrentUser();
        if ($user['role'] === 'superadmin') {
            include __DIR__ . '/../views/dashboard/admin-dashboard.php';
        } else {
            include __DIR__ . '/../views/dashboard/affiliato-dashboard.php';
        }
        break;

    case 'contracts':
        $contractController = new ContractController();
        if ($action === 'create') {
            $contractController->create();
        } elseif ($action === 'edit' && $id) {
            $contractController->edit($id);
        } elseif ($action === 'delete' && $id) {
            $contractController->delete($id);
        } elseif ($action === 'export') {
            $contractController->export();
        } else {
            $contractController->index();
        }
        break;

    case 'users':
        $userController = new UserController();
        if ($action === 'create') {
            $userController->create();
        } elseif ($action === 'edit' && $id) {
            $userController->edit($id);
        } elseif ($action === 'toggle' && $id) {
            $userController->toggleStatus($id);
        } else {
            $userController->index();
        }
        break;

    case 'coverage':
        (new CoverageController())->index();
        break;

    case 'profile':
        (new ProfileController())->index();
        break;

    case 'reports':
        (new ReportsController())->index();
        break;

    default:
        http_response_code(404);
        echo '<h1>404 - Pagina non trovata</h1>';
        break;
}
?>
