<?php

class WorkspaceController
{
    private $repo;

    public function __construct(WorkspaceRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $workspaces = $this->repo->findAll('workspace_name ASC');
        if (!empty($search)) {
            $workspaces = array_filter($workspaces, function($w) use ($search) {
                return stripos($w['workspace_name'], $search) !== false;
            });
        }
        if (!empty($type)) {
            $workspaces = array_filter($workspaces, function($w) use ($type) {
                return $w['workspace_type'] === $type;
            });
        }
        $title = 'Список рабочих мест';
        $entity = 'workspaces';
        require_once __DIR__ . '/../views/workspaces/list.php';
    }

    public function create()
    {
        $title = 'Добавить рабочее место';
        $entity = 'workspaces';
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        require_once __DIR__ . '/../views/workspaces/create.php';
    }

    public function store()
    {
        $data = [
            'workspace_name' => $_POST['workspace_name'] ?? '',
            'workspace_type' => $_POST['workspace_type'] ?? '',
            'price_per_hour' => $_POST['price_per_hour'] ?? '',
        ];
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?entity=workspaces&action=create');
            exit;
        }
        try {
            $this->repo->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Место создано'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=workspaces&action=list');
        exit;
    }

    public function edit($id)
    {
        $workspace = $this->repo->findById($id);
        if (!$workspace) die('Место не найдено');
        $title = 'Редактировать место';
        $entity = 'workspaces';
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? $workspace;
        unset($_SESSION['errors'], $_SESSION['old']);
        require_once __DIR__ . '/../views/workspaces/edit.php';
    }

    public function update($id)
    {
        $data = [
            'workspace_name' => $_POST['workspace_name'] ?? '',
            'workspace_type' => $_POST['workspace_type'] ?? '',
            'price_per_hour' => $_POST['price_per_hour'] ?? '',
        ];
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?entity=workspaces&action=edit&id=' . $id);
            exit;
        }
        try {
            $pdo = Database::getInstance()->getConnection();
            $sql = "UPDATE workspaces SET workspace_name=:n, workspace_type=:t, price_per_hour=:p WHERE workspace_id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['n'=>$data['workspace_name'], 't'=>$data['workspace_type'], 'p'=>$data['price_per_hour'], 'id'=>$id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Место обновлено'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=workspaces&action=list');
        exit;
    }

    public function delete($id)
    {
        $workspace = $this->repo->findById($id);
        if (!$workspace) die('Место не найдено');
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) as cnt FROM bookings WHERE workspace_id = :id AND status = 'активно'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $hasBookings = $stmt->fetch()['cnt'] > 0;
        $title = 'Удалить место';
        $entity = 'workspaces';
        require_once __DIR__ . '/../views/workspaces/delete.php';
    }

    public function destroy($id)
    {
        try {
            $this->repo->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Место удалено'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()];
        }
        header('Location: index.php?entity=workspaces&action=list');
        exit;
    }

    private function validate($data)
    {
        $errors = [];
        if (empty(trim($data['workspace_name']))) $errors['workspace_name'] = 'Название обязательно';
        if (!in_array($data['workspace_type'], ['открытое', 'переговорная', 'офис'])) $errors['workspace_type'] = 'Выберите тип';
        if (empty($data['price_per_hour']) || $data['price_per_hour'] <= 0) $errors['price_per_hour'] = 'Цена должна быть больше нуля';
        return $errors;
    }
}
