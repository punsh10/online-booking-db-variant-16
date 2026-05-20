<?php

class DiscountController
{
    private $repo;
    private $clientRepo;

    public function __construct(DiscountRepository $repo, ClientRepository $clientRepo)
    {
        $this->repo = $repo;
        $this->clientRepo = $clientRepo;
    }

    public function list()
    {
        $discounts = $this->repo->findAll();
        $title = 'Список скидок';
        $entity = 'discounts';
        require_once __DIR__ . '/../views/discounts/list.php';
    }

    public function create()
    {
        $clients = $this->clientRepo->findAll('last_name ASC');
        $title = 'Добавить скидку';
        $entity = 'discounts';
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        require_once __DIR__ . '/../views/discounts/create.php';
    }

    public function store()
    {
        $data = [
            'client_id' => $_POST['client_id'] ?? '',
            'discount_percent' => $_POST['discount_percent'] ?? '',
        ];
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?entity=discounts&action=create');
            exit;
        }
        try {
            $this->repo->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Скидка создана'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=discounts&action=list');
        exit;
    }

    public function delete($id)
    {
        $discount = $this->repo->findById($id);
        if (!$discount) die('Скидка не найдена');
        $title = 'Удалить скидку';
        $entity = 'discounts';
        require_once __DIR__ . '/../views/discounts/delete.php';
    }

    public function destroy($id)
    {
        try {
            $this->repo->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Скидка удалена'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=discounts&action=list');
        exit;
    }

    private function validate($data)
    {
        $errors = [];
        if (empty($data['client_id'])) $errors['client_id'] = 'Выберите клиента';
        if (empty($data['discount_percent']) || $data['discount_percent'] < 5 || $data['discount_percent'] > 50)
            $errors['discount_percent'] = 'Скидка от 5 до 50';
        return $errors;
    }
}
