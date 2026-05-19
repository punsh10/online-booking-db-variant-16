<?php

// Демонстрационный скрипт
// Показывает работу всех репозиториев

// Подключаем файлы вручную
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/RepositoryException.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/AbstractRepository.php';
require_once __DIR__ . '/src/Repositories/ClientRepository.php';
require_once __DIR__ . '/src/Repositories/WorkspaceRepository.php';
require_once __DIR__ . '/src/Repositories/BookingRepository.php';
require_once __DIR__ . '/src/Repositories/DiscountRepository.php';

echo "<h1>Демонстрация уровня доступа к данным</h1>";
echo "<pre>";

try {
    // Получаем подключение к базе данных
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    echo "✓ Подключение к базе данных установлено\n\n";

    // Создаём репозитории
    $clientRepo = new ClientRepository($pdo);
    $workspaceRepo = new WorkspaceRepository($pdo);
    $bookingRepo = new BookingRepository($pdo);
    $discountRepo = new DiscountRepository($pdo);

    // ===== ДЕМОНСТРАЦИЯ CLIENT REPOSITORY =====
    echo "===== 1. CLIENT REPOSITORY =====\n\n";

    // 1.1 Получить всех клиентов
    echo "--- Все клиенты ---\n";
    $clients = $clientRepo->findAll('last_name ASC');
    foreach ($clients as $client) {
        echo "ID: {$client['client_id']}, Имя: {$client['last_name']} {$client['first_name']}, Телефон: {$client['phone']}\n";
    }
    echo "\n";

    // 1.2 Найти клиента по ID
    echo "--- Клиент с ID = 1 ---\n";
    $client = $clientRepo->findById(1);
    echo "Найден: {$client['last_name']} {$client['first_name']}, Email: {$client['email']}\n\n";

    // 1.3 Найти клиента по телефону
    echo "--- Поиск клиента по телефону ---\n";
    $client = $clientRepo->findByPhone('+79123456701');
    if ($client) {
        echo "Найден: {$client['last_name']} {$client['first_name']}\n\n";
    }

    // 1.4 Клиенты с количеством бронирований
    echo "--- Клиенты с 2 и более бронированиями ---\n";
    $activeClients = $clientRepo->getClientsWithManyBookings(2);
    foreach ($activeClients as $c) {
        echo "{$c['last_name']} {$c['first_name']}: {$c['booking_count']} бронирований\n";
    }
    echo "\n";

    // ===== ДЕМОНСТРАЦИЯ WORKSPACE REPOSITORY =====
    echo "===== 2. WORKSPACE REPOSITORY =====\n\n";

    // 2.1 Все рабочие места
    echo "--- Все рабочие места ---\n";
    $workspaces = $workspaceRepo->findAll();
    foreach ($workspaces as $ws) {
        echo "ID: {$ws['workspace_id']}, Название: {$ws['workspace_name']}, Тип: {$ws['workspace_type']}, Цена: {$ws['price_per_hour']} руб.\n";
    }
    echo "\n";

    // 2.2 Места по типу
    echo "--- Только переговорные ---\n";
    $meetingRooms = $workspaceRepo->findByType('переговорная');
    foreach ($meetingRooms as $ws) {
        echo "{$ws['workspace_name']}: {$ws['price_per_hour']} руб/час\n";
    }
    echo "\n";

    // 2.3 Популярность по дням недели
    echo "--- Популярность типов мест по дням недели ---\n";
    $popularity = $workspaceRepo->getPopularityByDayOfWeek();
    foreach ($popularity as $row) {
        echo "{$row['day_of_week']} — {$row['workspace_type']}: {$row['total_bookings']} бронирований\n";
    }
    echo "\n";

    // ===== ДЕМОНСТРАЦИЯ BOOKING REPOSITORY =====
    echo "===== 3. BOOKING REPOSITORY =====\n\n";

    // 3.1 Все бронирования с деталями
    echo "--- Все бронирования с деталями ---\n";
    $bookings = $bookingRepo->findAllWithDetails();
    foreach ($bookings as $b) {
        echo "ID: {$b['booking_id']}, Клиент: {$b['client_last_name']}, Место: {$b['workspace_name']}, Статус: {$b['status']}\n";
    }
    echo "\n";

    // 3.2 Бронирования клиента с ID = 1
    echo "--- Бронирования клиента ID = 1 ---\n";
    $clientBookings = $bookingRepo->findByClient(1);
    foreach ($clientBookings as $b) {
        echo "Место: {$b['workspace_name']}, Начало: {$b['start_time']}, Статус: {$b['status']}\n";
    }
    echo "\n";

    // 3.3 Изменение статуса бронирования
    echo "--- Изменение статуса бронирования ID = 5 ---\n";
    $updated = $bookingRepo->updateStatus(5, 'завершено');
    echo "Обновлено записей: {$updated}\n";
    $booking = $bookingRepo->findById(5);
    echo "Новый статус: {$booking['status']}\n\n";

    // ===== ДЕМОНСТРАЦИЯ DISCOUNT REPOSITORY =====
    echo "===== 4. DISCOUNT REPOSITORY =====\n\n";

    // 4.1 Активная скидка клиента
    echo "--- Активная скидка клиента ID = 1 ---\n";
    $discount = $discountRepo->findActiveByClient(1);
    if ($discount) {
        echo "Скидка: {$discount['discount_percent']}%, Дата: {$discount['granted_date']}\n";
    } else {
        echo "Активной скидки нет\n";
    }
    echo "\n";

    // 4.2 Все скидки
    echo "--- Все скидки ---\n";
    $discounts = $discountRepo->findAll();
    foreach ($discounts as $d) {
        echo "ID: {$d['discount_id']}, Клиент ID: {$d['client_id']}, Процент: {$d['discount_percent']}%, Активна: " . ($d['is_active'] ? 'Да' : 'Нет') . "\n";
    }
    echo "\n";

    echo "✓ Демонстрация завершена успешно\n";

} catch (RepositoryException $e) {
    echo "✗ Ошибка репозитория: " . $e->getMessage() . "\n";
} catch (PDOException $e) {
    echo "✗ Ошибка базы данных: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "✗ Общая ошибка: " . $e->getMessage() . "\n";
}

echo "</pre>";
