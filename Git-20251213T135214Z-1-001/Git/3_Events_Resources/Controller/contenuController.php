<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Contenu.php";

class ContenuController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addContenu($contenu) {
        $sql = "INSERT INTO contenus (title, body, author, status, tags, likes) 
                VALUES (:title, :body, :author, :status, :tags, :likes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $contenu->getTitle(),
            ':body' => $contenu->getBody(),
            ':author' => $contenu->getAuthor(),
            ':status' => $contenu->getStatus(),
            ':tags' => $contenu->getTags(),
            ':likes' => $contenu->getLikes()
        ]);
        return $this->db->lastInsertId();
    }

    public function listContenus($search = null, $sort = 'newest') {
        $sql = "SELECT * FROM contenus WHERE status = 'published'";
        $params = [];
        if ($search) {
            $sql .= " AND (title LIKE :search OR body LIKE :search OR author LIKE :search OR tags LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        switch ($sort) {
            case 'oldest': $sql .= " ORDER BY created_at ASC"; break;
            case 'title_asc': $sql .= " ORDER BY title ASC"; break;
            case 'title_desc': $sql .= " ORDER BY title DESC"; break;
            case 'likes': $sql .= " ORDER BY likes DESC"; break;
            default: $sql .= " ORDER BY created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listAllContenus() {
        $sql = "SELECT * FROM contenus ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listPendingContenus() {
        $sql = "SELECT * FROM contenus WHERE status = 'pending' ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getContenuById($id) {
        $sql = "SELECT * FROM contenus WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function approveContenu($id) {
        $sql = "UPDATE contenus SET status = 'published' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function rejectContenu($id) {
        $sql = "UPDATE contenus SET status = 'rejected' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) as total FROM contenus WHERE status = 'pending'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function updateContenu($contenu) {
        $sql = "UPDATE contenus SET title = :title, body = :body, author = :author, 
                status = :status, tags = :tags, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $contenu->getId(),
            ':title' => $contenu->getTitle(),
            ':body' => $contenu->getBody(),
            ':author' => $contenu->getAuthor(),
            ':status' => $contenu->getStatus(),
            ':tags' => $contenu->getTags()
        ]);
    }

    public function incrementLikes($id) {
        $sql = "UPDATE contenus SET likes = likes + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function deleteContenu($id) {
        $sql = "DELETE FROM contenus WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM contenus";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new ContenuController();
    $action = $_POST['action'] ?? '';

    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
    }

    $listPath = ($source === 'backoffice') ? "../View/backoffice/resources.php" : "../View/frontoffice/resources.php";
    $formPath = ($source === 'backoffice') ? "../View/backoffice/resource_form.php" : "../View/frontoffice/resources.php";

    $redirectBack = function ($fallback) {
        $returnTo = $_POST['return_to'] ?? '';
        if (!empty($returnTo)) {
            header("Location: " . $returnTo);
            exit;
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!empty($referer)) {
            header("Location: " . $referer);
            exit;
        }
        header("Location: " . $fallback);
        exit;
    };

    if ($action === 'add') {
        $userRole = $_SESSION['user_role'] ?? 'client';
        $userName = $_SESSION['user_name'] ?? 'Anonymous';
        
        if ($userRole === 'admin') {
            $status = $_POST['status'] ?? 'published';
        } else {
            $status = 'pending';
        }
        
        $author = $_POST['author'] ?? $userName;
        $contenu = new Contenu(null, $_POST['title'], $_POST['body'] ?? '', $author, $status, $_POST['tags'] ?? '', 0);
        $controller->addContenu($contenu);
        
        if ($userRole === 'admin') {
            header("Location: " . $listPath);
        } else {
            $_SESSION['success'] = "Your resource has been submitted and is pending admin approval.";
            header("Location: ../View/frontoffice/resources.php");
        }
        exit;
    }

    if ($action === 'update') {
        $contenu = new Contenu($_POST['id'], $_POST['title'], $_POST['body'] ?? '', $_POST['author'] ?? 'Admin',
                               $_POST['status'] ?? 'draft', $_POST['tags'] ?? '', 0);
        $controller->updateContenu($contenu);
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'approve') {
        if (!empty($_POST['id'])) $controller->approveContenu($_POST['id']);
        header("Location: ../View/backoffice/resources.php");
        exit;
    }

    if ($action === 'reject') {
        if (!empty($_POST['id'])) $controller->rejectContenu($_POST['id']);
        header("Location: ../View/backoffice/resources.php");
        exit;
    }

    if ($action === 'like') {
        if (!empty($_POST['id'])) $controller->incrementLikes($_POST['id']);
        $redirectBack($listPath);
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deleteContenu($_POST['id']);
        $redirectBack($listPath);
    }
}
?>
