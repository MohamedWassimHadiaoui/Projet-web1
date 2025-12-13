<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Event.php";

class EventController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addEvent($event, $status = 'approved', $createdBy = null) {
        if ($this->columnExists('status')) {
            $sql = "INSERT INTO events (title, description, date_event, type, location, participants, tags, status, created_by) 
                    VALUES (:title, :description, :date_event, :type, :location, :participants, :tags, :status, :created_by)";
            $params = [
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':date_event' => $event->getDateEvent(),
                ':type' => $event->getType(),
                ':location' => $event->getLocation(),
                ':participants' => $event->getParticipants(),
                ':tags' => $event->getTags(),
                ':status' => $status,
                ':created_by' => $createdBy
            ];
        } else {
            $sql = "INSERT INTO events (title, description, date_event, type, location, participants, tags) 
                    VALUES (:title, :description, :date_event, :type, :location, :participants, :tags)";
            $params = [
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':date_event' => $event->getDateEvent(),
                ':type' => $event->getType(),
                ':location' => $event->getLocation(),
                ':participants' => $event->getParticipants(),
                ':tags' => $event->getTags()
            ];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->db->lastInsertId();
    }

    public function listEvents($search = null, $sort = 'newest') {
        $sql = "SELECT * FROM events";
        $params = [];
        $conditions = [];
        
        if ($this->columnExists('status')) {
            $conditions[] = "(status = 'approved' OR status IS NULL)";
        }
        
        if ($search) {
            $conditions[] = "(title LIKE :search OR description LIKE :search OR location LIKE :search OR tags LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        switch ($sort) {
            case 'oldest': $sql .= " ORDER BY date_event ASC"; break;
            case 'title_asc': $sql .= " ORDER BY title ASC"; break;
            case 'title_desc': $sql .= " ORDER BY title DESC"; break;
            case 'participants': $sql .= " ORDER BY participants DESC"; break;
            default: $sql .= " ORDER BY date_event DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function columnExists($column) {
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM events LIKE '$column'");
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function listAllEvents() {
        if ($this->columnExists('created_by')) {
            $sql = "SELECT e.*, u.name as creator_name FROM events e LEFT JOIN users u ON e.created_by = u.id ORDER BY e.created_at DESC";
        } else {
            $sql = "SELECT * FROM events ORDER BY created_at DESC";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listPendingEvents() {
        if (!$this->columnExists('status')) {
            return [];
        }
        $sql = "SELECT e.*, u.name as creator_name FROM events e LEFT JOIN users u ON e.created_by = u.id WHERE e.status = 'pending' ORDER BY e.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function approveEvent($id) {
        if (!$this->columnExists('status')) return;
        $sql = "UPDATE events SET status = 'approved' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function rejectEvent($id) {
        if (!$this->columnExists('status')) return;
        $sql = "UPDATE events SET status = 'rejected' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countPending() {
        if (!$this->columnExists('status')) return 0;
        $sql = "SELECT COUNT(*) as total FROM events WHERE status = 'pending'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function getEventById($id) {
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateEvent($event) {
        $sql = "UPDATE events SET title = :title, description = :description, date_event = :date_event,
                type = :type, location = :location, participants = :participants, tags = :tags WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $event->getId(),
            ':title' => $event->getTitle(),
            ':description' => $event->getDescription(),
            ':date_event' => $event->getDateEvent(),
            ':type' => $event->getType(),
            ':location' => $event->getLocation(),
            ':participants' => $event->getParticipants(),
            ':tags' => $event->getTags()
        ]);
    }

    public function deleteEvent($id) {
        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM events";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function countUpcoming() {
        $sql = "SELECT COUNT(*) as total FROM events WHERE date_event IS NOT NULL AND date_event >= NOW()";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function subscribeToEvent($eventId, $userId, $name, $email) {
        $sql = "INSERT INTO event_subscriptions (event_id, user_id, name, email) VALUES (:event_id, :user_id, :name, :email)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId, ':user_id' => $userId, ':name' => $name, ':email' => $email]);
        $this->incrementParticipants($eventId);
        return $this->db->lastInsertId();
    }

    public function unsubscribeFromEvent($eventId, $userId) {
        $sql = "DELETE FROM event_subscriptions WHERE event_id = :event_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId, ':user_id' => $userId]);
        $this->decrementParticipants($eventId);
    }

    public function isUserSubscribed($eventId, $userId) {
        $sql = "SELECT id FROM event_subscriptions WHERE event_id = :event_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId, ':user_id' => $userId]);
        return $stmt->fetch() !== false;
    }

    public function getEventSubscribers($eventId) {
        $sql = "SELECT * FROM event_subscriptions WHERE event_id = :event_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll();
    }

    private function incrementParticipants($eventId) {
        $sql = "UPDATE events SET participants = participants + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $eventId]);
    }

    private function decrementParticipants($eventId) {
        $sql = "UPDATE events SET participants = GREATEST(0, participants - 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $eventId]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new EventController();
    $action = $_POST['action'] ?? '';

    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
    }

    $listPath = ($source === 'backoffice') ? "../View/backoffice/events.php" : "../View/frontoffice/events.php";

    if ($action === 'add') {
        $event = new Event(null, $_POST['title'], $_POST['description'] ?? '', $_POST['date_event'] ?? null,
                           $_POST['type'] ?? 'offline', $_POST['location'] ?? '', $_POST['participants'] ?? 0, $_POST['tags'] ?? '');
        
        $userRole = $_SESSION['user_role'] ?? 'client';
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userRole === 'admin') {
            $controller->addEvent($event, 'approved', $userId);
            header("Location: " . $listPath);
        } else {
            $controller->addEvent($event, 'pending', $userId);
            $_SESSION['success'] = "Your event has been submitted and is pending admin approval.";
            header("Location: ../View/frontoffice/events.php");
        }
        exit;
    }

    if ($action === 'update') {
        $event = new Event($_POST['id'], $_POST['title'], $_POST['description'] ?? '', $_POST['date_event'] ?? null,
                           $_POST['type'] ?? 'offline', $_POST['location'] ?? '', $_POST['participants'] ?? 0, $_POST['tags'] ?? '');
        $controller->updateEvent($event);
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'approve') {
        if (!empty($_POST['id'])) $controller->approveEvent($_POST['id']);
        header("Location: ../View/backoffice/events.php");
        exit;
    }

    if ($action === 'reject') {
        if (!empty($_POST['id'])) $controller->rejectEvent($_POST['id']);
        header("Location: ../View/backoffice/events.php");
        exit;
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deleteEvent($_POST['id']);
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'subscribe') {
        $eventId = $_POST['event_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        $name = $_POST['name'] ?? $_SESSION['user_name'] ?? 'Guest';
        $email = $_POST['email'] ?? '';
        
        if ($eventId) {
            if (!$controller->isUserSubscribed($eventId, $userId)) {
                $controller->subscribeToEvent($eventId, $userId, $name, $email);
                $_SESSION['success'] = "You have successfully registered for this event!";
            }
        }
        header("Location: ../View/frontoffice/event_details.php?id=" . $eventId);
        exit;
    }

    if ($action === 'unsubscribe') {
        $eventId = $_POST['event_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($eventId && $userId) {
            $controller->unsubscribeFromEvent($eventId, $userId);
            $_SESSION['success'] = "You have been unsubscribed from this event.";
        }
        header("Location: ../View/frontoffice/event_details.php?id=" . $eventId);
        exit;
    }
}
?>
