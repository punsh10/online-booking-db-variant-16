<?php

// Класс для работы с таблицей clients
// Наследует общие методы от AbstractRepository

class ClientRepository extends AbstractRepository
{
    // Название таблицы
    protected $table = 'clients';

    // Первичный ключ
    protected $primaryKey = 'client_id';

    // Найти клиента по номеру телефона
    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM {$this->table} WHERE phone = :phone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch();
    }

    // Найти клиента по email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    // Создать нового клиента
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (last_name, first_name, patronymic, phone, email, birth_date)
                VALUES (:last_name, :first_name, :patronymic, :phone, :email, :birth_date)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'last_name'   => $data['last_name'],
            'first_name'  => $data['first_name'],
            'patronymic'  => $data['patronymic'] ?? null,
            'phone'       => $data['phone'],
            'email'       => $data['email'],
            'birth_date'  => $data['birth_date'],
        ]);

        // Возвращаем ID созданного клиента
        return $this->pdo->lastInsertId();
    }

    // Обновить данные клиента
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        // Собираем только те поля, которые пришли в $data
        if (isset($data['last_name'])) {
            $fields[] = "last_name = :last_name";
            $params['last_name'] = $data['last_name'];
        }
        if (isset($data['first_name'])) {
            $fields[] = "first_name = :first_name";
            $params['first_name'] = $data['first_name'];
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params['phone'] = $data['phone'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }

        if (empty($fields)) {
            return 0; // Нечего обновлять
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    // Получить клиентов, у которых больше указанного количества бронирований
    public function getClientsWithManyBookings($minBookings = 1)
    {
        $sql = "SELECT c.*, COUNT(b.booking_id) as booking_count
                FROM {$this->table} c
                LEFT JOIN bookings b ON c.client_id = b.client_id
                GROUP BY c.client_id
                HAVING COUNT(b.booking_id) >= :min_bookings
                ORDER BY booking_count DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['min_bookings' => $minBookings]);
        return $stmt->fetchAll();
    }
}
