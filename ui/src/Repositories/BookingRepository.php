<?php

// Класс для работы с таблицей bookings

class BookingRepository extends AbstractRepository
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    // Получить все бронирования с именами клиентов и названиями мест
    public function findAllWithDetails()
    {
        $sql = "SELECT
                    b.booking_id,
                    c.last_name AS client_last_name,
                    c.first_name AS client_first_name,
                    w.workspace_name,
                    w.workspace_type,
                    b.start_time,
                    b.end_time,
                    b.total_price,
                    b.status
                FROM {$this->table} b
                JOIN clients c ON b.client_id = c.client_id
                JOIN workspaces w ON b.workspace_id = w.workspace_id
                ORDER BY b.start_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Получить бронирования на определённую дату
    public function findByDate($date)
    {
        $sql = "SELECT * FROM {$this->table} WHERE DATE(start_time) = :date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }

    // Создать новое бронирование с проверкой (транзакция)
    public function create($data)
    {
        try {
            // Начинаем транзакцию
            $this->pdo->beginTransaction();

            // Проверяем, не занято ли место в это время
            $checkSql = "SELECT COUNT(*) as count FROM {$this->table}
                         WHERE workspace_id = :workspace_id
                         AND start_time = :start_time
                         AND status != 'отменено'";

            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([
                'workspace_id' => $data['workspace_id'],
                'start_time'   => $data['start_time'],
            ]);
            $result = $checkStmt->fetch();

            if ($result['count'] > 0) {
                throw new RepositoryException('Это место уже забронировано на указанное время');
            }

            // Вставляем бронирование
            $sql = "INSERT INTO {$this->table} (client_id, workspace_id, start_time, end_time, total_price, status)
                    VALUES (:client_id, :workspace_id, :start_time, :end_time, :total_price, :status)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'client_id'    => $data['client_id'],
                'workspace_id' => $data['workspace_id'],
                'start_time'   => $data['start_time'],
                'end_time'     => $data['end_time'],
                'total_price'  => $data['total_price'],
                'status'       => $data['status'] ?? 'активно',
            ]);

            $bookingId = $this->pdo->lastInsertId();

            // Подтверждаем транзакцию
            $this->pdo->commit();

            return $bookingId;

        } catch (PDOException $e) {
            // Если ошибка — отменяем все изменения
            $this->pdo->rollBack();
            throw new RepositoryException('Ошибка при создании бронирования: ' . $e->getMessage());
        }
    }

    // Изменить статус бронирования
    public function updateStatus($id, $status)
    {
        // Проверяем, что статус допустимый
        $allowedStatuses = ['активно', 'завершено', 'отменено'];
        if (!in_array($status, $allowedStatuses)) {
            throw new RepositoryException('Недопустимый статус: ' . $status);
        }

        $sql = "UPDATE {$this->table} SET status = :status WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'id'     => $id,
        ]);
        return $stmt->rowCount();
    }

    // Получить бронирования конкретного клиента
    public function findByClient($clientId)
    {
        $sql = "SELECT b.*, w.workspace_name, w.workspace_type
                FROM {$this->table} b
                JOIN workspaces w ON b.workspace_id = w.workspace_id
                WHERE b.client_id = :client_id
                ORDER BY b.start_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }
}
