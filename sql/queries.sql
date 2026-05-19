-- Запрос 1. Вывод всех бронирований с JOIN
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

-- Запрос 2. Клиенты с количеством бронирований больше 1
SELECT
    c.last_name AS Фамилия,
    c.first_name AS Имя,
    COUNT(b.booking_id) AS Количество_бронирований
FROM clients c
LEFT JOIN bookings b ON c.client_id = b.client_id
GROUP BY c.client_id, c.last_name, c.first_name
HAVING COUNT(b.booking_id) > 1
ORDER BY Количество_бронирований DESC;

-- Запрос 3. Популярность типов мест по дням недели
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
