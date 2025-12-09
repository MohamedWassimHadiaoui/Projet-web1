<?php
// controllers/ContenuController.php
require_once __DIR__ . '/../model/Contenu.php';

class ContenuController
{
    private $model;

    public function __construct()
    {
        $this->model = new Contenu();
    }

    // Front list
    public function index()
    {
        $contenus = $this->model->all();
        include __DIR__ . '/../views/frontoffice/contenus.php';
    }

    // Front details
    public function details()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=contenu&action=index');
            exit;
        }
        $contenu = $this->model->find($id);
        include __DIR__ . '/../views/frontoffice/contenu_details.php';
    }

    // Admin listing
    public function adminIndex()
    {
        $contenus = $this->model->all();
        include __DIR__ . '/../views/backoffice/contenu.php';
    }

    // Show create form (admin unified page)
    public function create()
    {
        $contenus = $this->model->all();
        include __DIR__ . '/../views/backoffice/contenu.php';
    }

    public function frontCreate()
    {
        header('Location: index.php?controller=contenu&action=index');
        exit;
    }

    public function store()
    {
        $data = [
            'title' => $_POST['title'] ?? '',
            'body' => $_POST['body'] ?? '',
            'author' => $_POST['author'] ?? null,
            'status' => $_POST['status'] ?? 'draft',
            'tags' => $_POST['tags'] ?? ''
        ];
        // Basic server-side validation
        if (trim($data['title']) === '' || trim($data['body']) === '') {
            header('Location: index.php?controller=contenu&action=admin&error=missing_fields');
            exit;
        }

        $this->model->create($data);
        header('Location: index.php?controller=contenu&action=admin');
        exit;
    }

    public function frontStore()
    {
        $data = [
            'title' => $_POST['title'] ?? '',
            'body' => $_POST['body'] ?? '',
            'author' => $_POST['author'] ?? null,
            'status' => $_POST['status'] ?? 'draft',
            'tags' => $_POST['tags'] ?? ''
        ];
        // Basic server-side validation for front submissions
        if (trim($data['title']) === '' || trim($data['body']) === '') {
            header('Location: index.php?controller=contenu&action=index&error=missing_fields');
            exit;
        }

        $this->model->create($data);
        header('Location: index.php?controller=contenu&action=index');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=contenu&action=admin');
            exit;
        }
        $contenu = $this->model->find($id);
        $contenus = $this->model->all();
        include __DIR__ . '/../views/backoffice/contenu.php';
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=contenu&action=admin');
            exit;
        }
        $data = [
            'title' => $_POST['title'] ?? '',
            'body' => $_POST['body'] ?? '',
            'author' => $_POST['author'] ?? null,
            'status' => $_POST['status'] ?? 'draft',
            'tags' => $_POST['tags'] ?? ''
        ];
        // Validate
        if (trim($data['title']) === '' || trim($data['body']) === '') {
            header('Location: index.php?controller=contenu&action=edit&id=' . urlencode($id) . '&error=missing_fields');
            exit;
        }

        $this->model->update($id, $data);
        header('Location: index.php?controller=contenu&action=admin');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->delete($id);
        }
        header('Location: index.php?controller=contenu&action=admin');
        exit;
    }

    // Simple like handler - increments likes then redirect back
    public function like()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=contenu&action=index');
            exit;
        }

        $newCount = $this->model->incrementLike($id);

        // If this is an AJAX request, return JSON with new count
        $isAjax = isset($_GET['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            header('Content-Type: application/json');
            if ($newCount === false) {
                echo json_encode(['success' => false]);
            } else {
                echo json_encode(['success' => true, 'likes' => (int)$newCount]);
            }
            exit;
        }

        // Redirect back to referer if present, else to listing
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer) {
            header('Location: ' . $referer);
        } else {
            header('Location: index.php?controller=contenu&action=index');
        }
        exit;
    }
}
