<?php

// Класс для работы с таблицей discounts

class DiscountRepository extends AbstractRepository
{
    protected $table = 'discounts';
    protected $primaryKey = 'discount_id';

    // Получить активную скидку клиента
    public function findActiveByClient($clientId)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE client_id = :client_id
                AND is_active = TRUE
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetch();
    }

    // Создать скидку для клиента
    public function create($data)
    {
        // Проверяем, нет ли уже активной скидки
        $existing = $this->findActiveByClient($data['client_id']);
        if ($existing) {
            throw new RepositoryException('У клиента уже есть активная скидка');
        }

        $sql = "INSERT INTO {$this->table} (client_id, discount_percent, is_active, granted_date)
                VALUES (:client_id, :discount_percent, :is_active, :granted_date)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'client_id'        => $data['client_id'],
            'discount_percent' => $data['discount_percent'],
            'is_active'        => $data['is_active'] ?? true,
            'granted_date'     => $data['granted_date'] ?? date('Y-m-d'),
        ]);

        return $this->pdo->lastInsertId();
    }

    // Деактивировать скидку
    public function deactivate($id)
    {
        $sql = "UPDATE {$this->table} SET is_active = FALSE WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
