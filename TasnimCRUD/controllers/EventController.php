<?php
// controllers/EventController.php
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../model/Contenu.php';

class EventController
{
    private $model;
    private $contenuModel;

    public function __construct()
    {
        $this->model = new Event();
        $this->contenuModel = new Contenu();
    }

    public function index()
    {
        $events = $this->model->all();
        include __DIR__ . '/../views/frontoffice/events.php';
    }

    public function details()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=event&action=index');
            exit;
        }
        $event = $this->model->find($id);
        include __DIR__ . '/../views/frontoffice/event_details.php';
    }

    public function adminIndex()
    {
        $events = $this->model->all();
        include __DIR__ . '/../views/backoffice/event.php';
    }

    public function create()
    {
        // Show unified backoffice page with create form
        $events = $this->model->all();
        include __DIR__ . '/../views/backoffice/event.php';
    }

    // Front-facing creation form for public users
    public function frontCreate()
    {
        // The create form is now embedded in the front office `events.php` page.
        header('Location: index.php?controller=event&action=index');
        exit;
    }

    public function store()
    {
        // Only trust client-side validation; server uses prepared statements
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'date_event' => $_POST['date_event'] ?? '',
            'type' => $_POST['type'] ?? '',
            'location' => $_POST['location'] ?? '',
            'participants' => $_POST['participants'] ?? 0,
            'tags' => $_POST['tags'] ?? ''
        ];

        $this->model->create($data);
        header('Location: index.php?controller=event&action=admin');
        exit;
    }

    // Store from front end, redirect to front listing
    public function frontStore()
    {
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'date_event' => $_POST['date_event'] ?? '',
            'type' => $_POST['type'] ?? '',
            'location' => $_POST['location'] ?? '',
            'participants' => $_POST['participants'] ?? 0,
            'tags' => $_POST['tags'] ?? ''
        ];

        $this->model->create($data);
        header('Location: index.php?controller=event&action=index');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=event&action=admin');
            exit;
        }
        $event = $this->model->find($id);
        $events = $this->model->all();
        include __DIR__ . '/../views/backoffice/event.php';
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=event&action=admin');
            exit;
        }
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'date_event' => $_POST['date_event'] ?? '',
            'type' => $_POST['type'] ?? '',
            'location' => $_POST['location'] ?? '',
            'participants' => $_POST['participants'] ?? 0,
            'tags' => $_POST['tags'] ?? ''
        ];
        $this->model->update($id, $data);
        header('Location: index.php?controller=event&action=admin');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->delete($id);
        }
        header('Location: index.php?controller=event&action=admin');
        exit;
    }

    // Combined view: Events + Contenus together
    public function combined()
    {
        $events = $this->model->all();
        $contenus = $this->contenuModel->all();
        
        // Recherche
        $searchQuery = $_GET['search'] ?? '';
        if (!empty($searchQuery)) {
            $searchLower = strtolower($searchQuery);
            $events = array_filter($events, function($event) use ($searchLower) {
                return stripos($event['title'], $searchLower) !== false ||
                       stripos($event['description'], $searchLower) !== false ||
                       stripos($event['location'], $searchLower) !== false ||
                       stripos($event['tags'] ?? '', $searchLower) !== false;
            });
        }
        
        // Tri
        $sortBy = $_GET['sort'] ?? 'date_desc';
        switch ($sortBy) {
            case 'date_asc':
                usort($events, fn($a, $b) => strtotime($a['date_event']) <=> strtotime($b['date_event']));
                break;
            case 'date_desc':
                usort($events, fn($a, $b) => strtotime($b['date_event']) <=> strtotime($a['date_event']));
                break;
            case 'title_asc':
                usort($events, fn($a, $b) => strcasecmp($a['title'], $b['title']));
                break;
            case 'title_desc':
                usort($events, fn($a, $b) => strcasecmp($b['title'], $a['title']));
                break;
            case 'participants_asc':
                usort($events, fn($a, $b) => ($a['participants'] ?? 0) <=> ($b['participants'] ?? 0));
                break;
            case 'participants_desc':
                usort($events, fn($a, $b) => ($b['participants'] ?? 0) <=> ($a['participants'] ?? 0));
                break;
        }
        
        include __DIR__ . '/../views/frontoffice/events_and_contenus.php';
    }
}
