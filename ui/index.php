<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/AbstractRepository.php';
require_once __DIR__ . '/src/RepositoryException.php';
require_once __DIR__ . '/src/Repositories/ClientRepository.php';
require_once __DIR__ . '/src/Repositories/WorkspaceRepository.php';
require_once __DIR__ . '/src/Repositories/BookingRepository.php';
require_once __DIR__ . '/src/Repositories/DiscountRepository.php';

$database = Database::getInstance();
$pdo = $database->getConnection();

$clientRepo = new ClientRepository($pdo);
$workspaceRepo = new WorkspaceRepository($pdo);
$discountRepo = new DiscountRepository($pdo);

$entity = $_GET['entity'] ?? 'clients';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($entity) {
    case 'clients':
        require_once __DIR__ . '/controllers/ClientController.php';
        $controller = new ClientController($clientRepo);
        break;
    case 'workspaces':
        require_once __DIR__ . '/controllers/WorkspaceController.php';
        $controller = new WorkspaceController($workspaceRepo);
        break;
    case 'discounts':
        require_once __DIR__ . '/controllers/DiscountController.php';
        $controller = new DiscountController($discountRepo, $clientRepo);
        break;
    default:
        die('Неизвестный справочник');
}

switch ($action) {
    case 'list': $controller->list(); break;
    case 'create': $controller->create(); break;
    case 'edit': $controller->edit($id); break;
    case 'delete': $controller->delete($id); break;
    case 'store': $controller->store(); break;
    case 'update': $controller->update($id); break;
    case 'destroy': $controller->destroy($id); break;
    default: die('Неизвестное действие');
}
