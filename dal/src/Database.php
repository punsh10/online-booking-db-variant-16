<?php

// Класс для подключения к базе данных
// Использует паттерн Singleton — подключение создаётся только один раз

class Database
{
    // Здесь будет храниться единственное подключение
    private static $instance = null;

    // Само подключение PDO
    private $connection;

    // Конструктор — вызывается, когда мы пишем new Database()
    // Но мы сделаем его закрытым, чтобы нельзя было создать объект снаружи
    private function __construct()
    {
        // Строка для подключения к MySQL
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        // Настройки PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ошибки будут выбрасывать исключения
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Данные возвращаются как массив
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Отключаем эмуляцию (безопасность)
        ];

        // Создаём подключение
        $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    // Метод для получения подключения
    // Если подключения ещё нет — создаём, если есть — возвращаем готовое
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Возвращает объект PDO для выполнения запросов
    public function getConnection()
    {
        return $this->connection;
    }
}
