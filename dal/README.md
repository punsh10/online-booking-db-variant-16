Data Access Layer для коворкинг-центра

PHP-классы для работы с базой данных онлайн-бронирования рабочих мест.

Уровень доступа к данным (Data Access Layer)

Реализация уровня доступа к данным на PHP с использованием PDO.

Стек: PHP 7.4+, MySQL 8.0, PDO.

Структура

- `dal/config.example.php` — пример файла конфигурации
- `dal/demo.php` — демонстрационный скрипт
- `dal/src/Database.php` — класс подключения к БД (Singleton)
- `dal/src/AbstractRepository.php` — базовый репозиторий с CRUD-операциями
- `dal/src/RepositoryException.php` — класс исключений
- `dal/src/Repositories/ClientRepository.php` — работа с таблицей clients
- `dal/src/Repositories/WorkspaceRepository.php` — работа с таблицей workspaces
- `dal/src/Repositories/BookingRepository.php` — работа с таблицей bookings
- `dal/src/Repositories/DiscountRepository.php` — работа с таблицей discounts

Установка и запуск

1. Скопируйте файлы на хостинг с поддержкой PHP
2. Переименуйте `config.example.php` в `config.php`
3. Укажите в `config.php` настройки подключения к вашей базе данных
4. Откройте `demo.php` в браузере

Безопасность

Все SQL-запросы используют подготовленные выражения PDO для защиты от SQL-инъекций. Операции, затрагивающие несколько таблиц, обёрнуты в транзакции.
