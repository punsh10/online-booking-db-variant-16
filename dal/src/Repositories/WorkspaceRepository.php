<?php

// Класс для работы с таблицей workspaces

class WorkspaceRepository extends AbstractRepository
{
    protected $table = 'workspaces';
    protected $primaryKey = 'workspace_id';

    // Найти места по типу (открытое, переговорная, офис)
    public function findByType($type)
    {
        $sql = "SELECT * FROM {$this->table} WHERE workspace_type = :type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll();
    }

    // Создать новое рабочее место
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (workspace_name, workspace_type, price_per_hour)
                VALUES (:workspace_name, :workspace_type, :price_per_hour)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'workspace_name' => $data['workspace_name'],
            'workspace_type' => $data['workspace_type'],
            'price_per_hour' => $data['price_per_hour'],
        ]);

        return $this->pdo->lastInsertId();
    }

    // Получить популярность типов мест по дням недели
    public function getPopularityByDayOfWeek()
    {
        $sql = "SELECT
                    CASE DAYOFWEEK(b.start_time)
                        WHEN 1 THEN 'Воскресенье'
                        WHEN 2 THEN 'Понедельник'
                        WHEN 3 THEN 'Вторник'
                        WHEN 4 THEN 'Среда'
                        WHEN 5 THEN 'Четверг'
                        WHEN 6 THEN 'Пятница'
                        WHEN 7 THEN 'Суббота'
                    END AS day_of_week,
                    w.workspace_type,
                    COUNT(b.booking_id) AS total_bookings
                FROM bookings b
                JOIN {$this->table} w ON b.workspace_id = w.workspace_id
                WHERE b.status != 'отменено'
                GROUP BY DAYOFWEEK(b.start_time), w.workspace_type
                ORDER BY DAYOFWEEK(b.start_time), total_bookings DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
