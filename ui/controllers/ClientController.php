<?php

class ClientController
{
    private $repo;

    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        $search = $_GET['search'] ?? '';
        $clients = $this->repo->findAll('last_name ASC');
        if (!empty($search)) {
            $clients = array_filter($clients, function($c) use ($search) {
                return stripos($c['last_name'], $search) !== false || stripos($c['phone'], $search) !== false;
            });
        }
        $title = 'Список клиентов';
        $entity = 'clients';
        require_once __DIR__ . '/../views/clients/list.php';
    }

    public function create()
    {
        $title = 'Добавить клиента';
        $entity = 'clients';
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        require_once __DIR__ . '/../views/clients/create.php';
    }

    public function store()
    {
        $data = [
            'last_name' => $_POST['last_name'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'patronymic' => $_POST['patronymic'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? '',
        ];
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?entity=clients&action=create');
            exit;
        }
        try {
            $this->repo->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Клиент создан'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=clients&action=list');
        exit;
    }

    public function edit($id)
    {
        $client = $this->repo->findById($id);
        if (!$client) die('Клиент не найден');
        $title = 'Редактировать клиента';
        $entity = 'clients';
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? $client;
        unset($_SESSION['errors'], $_SESSION['old']);
        require_once __DIR__ . '/../views/clients/edit.php';
    }

    public function update($id)
    {
        $data = [
            'last_name' => $_POST['last_name'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'patronymic' => $_POST['patronymic'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? '',
        ];
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?entity=clients&action=edit&id=' . $id);
            exit;
        }
        try {
            $this->repo->update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Клиент обновлён'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=clients&action=list');
        exit;
    }

    public function delete($id)
    {
        $client = $this->repo->findById($id);
        if (!$client) die('Клиент не найден');
        $bookingRepo = new BookingRepository(Database::getInstance()->getConnection());
        $bookings = $bookingRepo->findByClient($id);
        $hasBookings = count($bookings) > 0;
        $title = 'Удалить клиента';
        $entity = 'clients';
        require_once __DIR__ . '/../views/clients/delete.php';
    }

    public function destroy($id)
    {
        try {
            $this->repo->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Клиент удалён'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=clients&action=list');
        exit;
    }

    private function validate($data)
    {
        $errors = [];
        if (empty(trim($data['last_name']))) $errors['last_name'] = 'Фамилия обязательна';
        if (empty(trim($data['first_name']))) $errors['first_name'] = 'Имя обязательно';
        if (empty(trim($data['phone']))) $errors['phone'] = 'Телефон обязателен';
        if (empty(trim($data['email']))) $errors['email'] = 'Email обязателен';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный email';
        if (empty($data['birth_date'])) $errors['birth_date'] = 'Дата рождения обязательна';
        else {
            $birthDate = strtotime($data['birth_date']);
            if ($birthDate > time()) $errors['birth_date'] = 'Дата не может быть в будущем';
            elseif ($birthDate > strtotime('-18 years')) $errors['birth_date'] = 'Клиент должен быть старше 18 лет';
        }
        return $errors;
    }
}
