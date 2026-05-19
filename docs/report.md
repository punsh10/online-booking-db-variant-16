МИНИСТЕРСТВО ОБРАЗОВАНИЯ КУЗБАССА

ГПОУ «ЮРГИНСКИЙ ТЕХНОЛОГИЧЕСКИЙ КОЛЛЕДЖ»
ИМ. ПАВЛЮЧКОВА Г.А.
Отделение АиТ 

ОТЧЕТ ПО учебной практической работе

Специальность		09.02.09 Веб-разработка

Выполнил студент гр. 454 
Олейников М.В.
Проверил преподаватель
Поликарпочкин М.В.

2026 г. 

Цель: создать базу данных для коворкинга, наполнить её, написать запросы и оформить отчёт. 

РАЗДЕЛ 1. АНАЛИЗ ПРЕДМЕТНОЙ ОБЛАСТИ
База данных предназначена для онлайн-бронирования рабочих мест в коворкинг-центре с почасовой оплатой. Клиенты выбирают место, время и бронируют его через систему.
Основные бизнес-правила:
    1. Каждый клиент регистрируется с указанием фамилии, имени, телефона, email и даты рождения. Минимальный возраст — 18 лет.
    2. Рабочие места делятся на три типа: открытое пространство, переговорная комната и отдельный офис. У каждого типа своя стоимость часа.
    3. Нельзя забронировать одно место дважды на одно и то же время. Защита реализована уникальным ключом в таблице bookings.
    4. Клиенты с более чем десятью успешными арендами получают персональную скидку. Данные хранятся в таблице discounts.
    5. Бронирование имеет статус: активно, завершено или отменено. Отменённые не учитываются в аналитике.
    6. Время окончания аренды всегда позже времени начала.

РАЗДЕЛ 2. КОНЦЕПТУАЛЬНАЯ МОДЕЛЬ
Сущность Клиент (clients):
Первичный ключ: client_id.
Атрибуты: last_name, first_name, patronymic, phone, email, birth_date, registration_date.
Сущность Рабочее место (workspaces):
Первичный ключ: workspace_id.
Атрибуты: workspace_name, workspace_type (открытое, переговорная, офис), price_per_hour.
Сущность Бронирование (bookings):
Первичный ключ: booking_id.
Атрибуты: client_id, workspace_id, start_time, end_time, total_price, status, created_at.
Сущность Скидка (discounts):
Первичный ключ: discount_id.
Атрибуты: client_id, discount_percent, is_active, granted_date.
Связи:
Клиент и Бронирование — связь один-ко-многим. Один клиент может иметь много бронирований.
Рабочее место и Бронирование — связь один-ко-многим. Одно место бронируется много раз.
Клиент и Скидка — связь один-к-одному. У клиента только одна активная скидка.
ER-диаграмма:

РАЗДЕЛ 3. ЛОГИЧЕСКАЯ МОДЕЛЬ И НОРМАЛИЗАЦИЯ
Реляционная схема:
Таблица clients:
Столбцы: client_id (INT, PRIMARY KEY, AUTO_INCREMENT), last_name (VARCHAR(50), NOT NULL), first_name (VARCHAR(50), NOT NULL), patronymic (VARCHAR(50)), phone (VARCHAR(20), NOT NULL, UNIQUE), email (VARCHAR(100), NOT NULL, UNIQUE), birth_date (DATE, NOT NULL), registration_date (DATE, NOT NULL, DEFAULT CURRENT_DATE).
Ограничение CHECK: birth_date <= '2008-05-19' (возраст от 18 лет).
Таблица workspaces:
Столбцы: workspace_id (INT, PRIMARY KEY, AUTO_INCREMENT), workspace_name (VARCHAR(50), NOT NULL), workspace_type (ENUM 'открытое', 'переговорная', 'офис', NOT NULL), price_per_hour (DECIMAL(10,2), NOT NULL, CHECK > 0).
Таблица bookings:
Столбцы: booking_id (INT, PRIMARY KEY, AUTO_INCREMENT), client_id (INT, NOT NULL, FOREIGN KEY REFERENCES clients), workspace_id (INT, NOT NULL, FOREIGN KEY REFERENCES workspaces), start_time (DATETIME, NOT NULL), end_time (DATETIME, NOT NULL), total_price (DECIMAL(10,2), NOT NULL, CHECK > 0), status (ENUM 'активно', 'завершено', 'отменено', DEFAULT 'активно'), created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP).
Ограничения: UNIQUE KEY (workspace_id, start_time), CHECK (end_time > start_time).
Таблица discounts:
Столбцы: discount_id (INT, PRIMARY KEY, AUTO_INCREMENT), client_id (INT, NOT NULL, FOREIGN KEY REFERENCES clients, UNIQUE), discount_percent (INT, NOT NULL, CHECK BETWEEN 5 AND 50), is_active (BOOLEAN, DEFAULT TRUE), granted_date (DATE, NOT NULL, DEFAULT CURRENT_DATE).
Нормализация:
Первая нормальная форма (1НФ): все таблицы соответствуют 1НФ, так как каждый столбец содержит только атомарные значения. Нет повторяющихся групп или массивов данных. Например, телефон хранится одним значением в столбце phone.
Вторая нормальная форма (2НФ): все таблицы соответствуют 2НФ. Первичный ключ каждой таблицы состоит из одного столбца, поэтому частичные зависимости невозможны. Все неключевые атрибуты зависят от полного первичного ключа.
Третья нормальная форма (3НФ): все таблицы соответствуют 3НФ. Транзитивные зависимости отсутствуют. Стоимость часа хранится в workspaces, а не в bookings. Поле total_price в bookings является осознанной денормализацией — оно фиксирует историческую цену на момент бронирования, так как стоимость часа может меняться со временем.

РАЗДЕЛ 4. SQL-СКРИПТ СОЗДАНИЯ БАЗЫ ДАННЫХ
sql
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS kovorking_variant16
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE kovorking_variant16;

-- Таблица клиентов
CREATE TABLE clients (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    patronymic VARCHAR(50),
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    registration_date DATE NOT NULL DEFAULT (CURRENT_DATE)
);

-- Ограничение на минимальный возраст клиента (18 лет)
ALTER TABLE clients ADD CONSTRAINT chk_age CHECK (birth_date <= '2008-05-19');

-- Таблица рабочих мест
CREATE TABLE workspaces (
    workspace_id INT AUTO_INCREMENT PRIMARY KEY,
    workspace_name VARCHAR(50) NOT NULL,
    workspace_type ENUM('открытое', 'переговорная', 'офис') NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL CHECK (price_per_hour > 0)
);

-- Таблица бронирований
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    workspace_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    total_price DECIMAL(10,2) NOT NULL CHECK (total_price > 0),
    status ENUM('активно', 'завершено', 'отменено') DEFAULT 'активно',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(workspace_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_booking_slot (workspace_id, start_time),
    CHECK (end_time > start_time)
);

-- Таблица скидок
CREATE TABLE discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    discount_percent INT NOT NULL CHECK (discount_percent BETWEEN 5 AND 50),
    is_active BOOLEAN DEFAULT TRUE,
    granted_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_active_discount (client_id)
);

-- Индексы для ускорения запросов
CREATE INDEX idx_bookings_start_time ON bookings(start_time);
CREATE INDEX idx_workspace_type ON workspaces(workspace_type);
CREATE INDEX idx_discounts_active ON discounts(is_active);

РАЗДЕЛ 5. ТЕСТОВЫЕ ДАННЫЕ И ПРИМЕРЫ ЗАПРОСОВ
Заполнение таблицы clients (10 записей):
sql
INSERT INTO clients (last_name, first_name, patronymic, phone, email, birth_date) VALUES
('Иванов', 'Иван', 'Иванович', '+79123456701', 'ivanov@example.com', '1985-05-15'),
('Петрова', 'Мария', 'Сергеевна', '+79123456702', 'petrova@example.com', '1992-11-23'),
('Сидоров', 'Алексей', 'Владимирович', '+79123456703', 'sidorov@example.com', '1978-03-02'),
('Козлова', 'Елена', 'Анатольевна', '+79123456704', 'kozlovae@example.com', '2000-07-19'),
('Морозов', 'Дмитрий', 'Павлович', '+79123456705', 'morozov@example.com', '1995-12-01'),
('Новикова', 'Анна', 'Игоревна', '+79123456706', 'novikova@example.com', '1988-09-10'),
('Кузнецов', 'Павел', 'Олегович', '+79123456707', 'kuznetsov@example.com', '1990-04-25'),
('Смирнова', 'Ольга', 'Дмитриевна', '+79123456708', 'smirnova@example.com', '1983-07-14'),
('Федоров', 'Сергей', 'Николаевич', '+79123456709', 'fedorov@example.com', '1998-01-30'),
('Васильева', 'Татьяна', 'Андреевна', '+79123456710', 'vasileva@example.com', '1991-06-05');
Заполнение таблицы workspaces (10 записей):
sql
INSERT INTO workspaces (workspace_name, workspace_type, price_per_hour) VALUES
('Стол у окна 1', 'открытое', 300.00),
('Стол у окна 2', 'открытое', 300.00),
('Стол в центре 1', 'открытое', 250.00),
('Стол в центре 2', 'открытое', 250.00),
('Переговорная Альфа', 'переговорная', 800.00),
('Переговорная Бета', 'переговорная', 800.00),
('Офис Люкс', 'офис', 1500.00),
('Офис Стандарт', 'офис', 1200.00),
('Стол в тихой зоне 1', 'открытое', 350.00),
('Стол в тихой зоне 2', 'открытое', 350.00);
Заполнение таблицы bookings (16 записей):
sql
INSERT INTO bookings (client_id, workspace_id, start_time, end_time, total_price, status) VALUES
(1, 1, '2026-05-20 09:00:00', '2026-05-20 12:00:00', 900.00, 'завершено'),
(2, 5, '2026-05-20 10:00:00', '2026-05-20 11:00:00', 800.00, 'завершено'),
(3, 7, '2026-05-21 14:00:00', '2026-05-21 18:00:00', 6000.00, 'завершено'),
(4, 3, '2026-05-21 09:00:00', '2026-05-21 17:00:00', 2000.00, 'завершено'),
(1, 2, '2026-05-22 10:00:00', '2026-05-22 12:00:00', 600.00, 'активно'),
(5, 6, '2026-05-22 11:00:00', '2026-05-22 13:00:00', 1600.00, 'активно'),
(6, 8, '2026-05-23 09:00:00', '2026-05-23 18:00:00', 10800.00, 'активно'),
(7, 1, '2026-05-23 14:00:00', '2026-05-23 16:00:00', 600.00, 'активно'),
(8, 4, '2026-05-24 10:00:00', '2026-05-24 15:00:00', 1250.00, 'активно'),
(9, 9, '2026-05-24 09:00:00', '2026-05-24 11:00:00', 700.00, 'активно'),
(2, 5, '2026-05-25 10:00:00', '2026-05-25 12:00:00', 1600.00, 'активно'),
(10, 3, '2026-05-25 13:00:00', '2026-05-25 18:00:00', 1250.00, 'активно'),
(3, 7, '2026-05-26 09:00:00', '2026-05-26 12:00:00', 4500.00, 'активно'),
(4, 10, '2026-05-26 14:00:00', '2026-05-26 17:00:00', 1050.00, 'активно'),
(5, 2, '2026-05-27 10:00:00', '2026-05-27 13:00:00', 900.00, 'активно'),
(6, 6, '2026-05-27 11:00:00', '2026-05-27 14:00:00', 2400.00, 'активно');
Заполнение таблицы discounts (4 записи):
sql
INSERT INTO discounts (client_id, discount_percent, is_active, granted_date) VALUES
(1, 10, TRUE, '2026-04-01'),
(3, 15, TRUE, '2026-03-15'),
(6, 5, FALSE, '2026-02-20'),
(8, 20, TRUE, '2026-04-10');

Запрос 1. Вывод всех бронирований с соединением трёх таблиц.
Бизнес-задача: отобразить полную информацию о бронированиях для администратора — кто, где, когда и на сколько забронировал место.
sql
SELECT
    c.last_name AS Фамилия,
    c.first_name AS Имя,
    w.workspace_name AS Место,
    w.workspace_type AS Тип,
    b.start_time AS Начало,
    b.end_time AS Конец,
    b.total_price AS Цена,
    b.status AS Статус
FROM bookings b
JOIN clients c ON b.client_id = c.client_id
JOIN workspaces w ON b.workspace_id = w.workspace_id
ORDER BY b.start_time;


Запрос 2. Группировка с HAVING (постоянные клиенты).
Бизнес-задача: найти клиентов с более чем одним бронированием для программы лояльности.
sql
SELECT
    c.last_name AS Фамилия,
    c.first_name AS Имя,
    COUNT(b.booking_id) AS Количество_бронирований
FROM clients c
LEFT JOIN bookings b ON c.client_id = b.client_id
GROUP BY c.client_id, c.last_name, c.first_name
HAVING COUNT(b.booking_id) > 1
ORDER BY Количество_бронирований DESC;


Запрос 3. Самые популярные типы мест по дням недели.
Бизнес-задача: проанализировать востребованность разных типов мест по дням недели для оптимизации цен и планирования загрузки коворкинга.
sql
SELECT
    CASE DAYOFWEEK(b.start_time)
        WHEN 1 THEN 'Воскресенье'
        WHEN 2 THEN 'Понедельник'
        WHEN 3 THEN 'Вторник'
        WHEN 4 THEN 'Среда'
        WHEN 5 THEN 'Четверг'
        WHEN 6 THEN 'Пятница'
        WHEN 7 THEN 'Суббота'
    END AS День_недели,
    w.workspace_type AS Тип_места,
    COUNT(b.booking_id) AS Количество_бронирований
FROM bookings b
JOIN workspaces w ON b.workspace_id = w.workspace_id
WHERE b.status != 'отменено'
GROUP BY DAYOFWEEK(b.start_time), w.workspace_type
ORDER BY DAYOFWEEK(b.start_time), Количество_бронирований DESC;

РАЗДЕЛ 6. ПРОВЕРКА ОГРАНИЧЕНИЙ ЦЕЛОСТНОСТИ
Проверка 1. Нарушение уникальности (двойное бронирование).
Попытка забронировать место №1 на время, которое уже занято:
sql
INSERT INTO bookings (client_id, workspace_id, start_time, end_time, total_price, status)
VALUES (5, 1, '2026-05-20 09:00:00', '2026-05-20 11:00:00', 600.00, 'активно');
Сообщение об ошибке: #1062 - Duplicate entry '1-2026-05-20 09:00:00' for key 'unique_booking_slot'

Проверка 2. Нарушение внешнего ключа (удаление клиента с бронированиями).
Попытка удалить клиента, у которого есть записи в таблице bookings:
sql
DELETE FROM clients WHERE client_id = 1;
Сообщение об ошибке: #1451 - Cannot delete or update a parent row: a foreign key constraint fails

Проверка 3. Нарушение CHECK-ограничения (клиент младше 18 лет).
Попытка добавить клиента с датой рождения 2015 год:
sql
INSERT INTO clients (last_name, first_name, phone, email, birth_date)
VALUES ('Тестов', 'Тест', '+79999999999', 'test@example.com', '2015-01-01');
Сообщение об ошибке: #3819 - Check constraint 'chk_age' is violated.

РАЗДЕЛ 7. ВЫВОДЫ
В ходе практической работы спроектирована и реализована база данных для онлайн-бронирования рабочих мест в коворкинге. База включает четыре таблицы: clients, workspaces, bookings и discounts.

СПИСОК ЛИТЕРАТУРЫ
    1. Официальная документация MySQL 8.0 Reference Manual. URL: https://dev.mysql.com/doc/refman/8.0/en/
    2. Официальная документация MariaDB. URL: https://mariadb.com/kb/en/documentation/
    3. Дейт К. Дж. Введение в системы баз данных. 8-е издание. М.: Вильямс, 2005.
    4. Грофф Дж., Вайнберг П. SQL: полное руководство. 3-е издание. М.: Вильямс, 2015.
    5. Документация phpMyAdmin. URL: https://www.phpmyadmin.net/docs/
    6. СУБД: MariaDB 10.x на хостинге Beget. URL: https://beget.com

ЗАКЛЮЧЕНИЕ
В ходе практической работы была создана база данных для коворкинга, наполнил её, написал запросы и оформил отчёт. 
