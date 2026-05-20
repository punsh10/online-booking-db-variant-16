<?php

// Абстрактный класс с общими методами для всех таблиц
// Другие классы будут наследовать эти методы

abstract class AbstractRepository
{
    // Подключение к базе данных
    protected $pdo;

    // Название таблицы (задаётся в дочернем классе)
    protected $table;

    // Первичный ключ таблицы (обычно id)
    protected $primaryKey;

    // Конструктор принимает подключение PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Получить все записи из таблицы
    // Можно добавить сортировку и ограничение количества
    public function findAll($orderBy = '', $limit = 0)
    {
        $sql = "SELECT * FROM {$this->table}";

        // Если указана сортировка — добавляем
        if (!empty($orderBy)) {
            // Проверяем, что сортировка безопасна (только буквы, пробел и подчёркивание)
            if (preg_match('/^[a-zA-Z_\s]+$/', $orderBy)) {
                $sql .= " ORDER BY {$orderBy}";
            }
        }

        // Если указан лимит — добавляем
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Найти запись по ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Удалить запись по ID
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount(); // Сколько строк удалено
    }
}
