<?php
// Main Router
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/EventController.php';
require_once __DIR__ . '/controllers/ContenuController.php';
require_once __DIR__ . '/controllers/FrontofficeController.php';

$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;

// Route to Event Controller
if ($controller === 'event') {
    $evc = new EventController();
    switch ($action) {
        case 'admin':
            $evc->adminIndex();
            break;
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $evc->store();
            } else {
                $evc->create();
            }
            break;
        case 'create_front':
            // Front creation should only accept POST (form embedded on events index)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $evc->frontStore();
            } else {
                header('Location: index.php?controller=event&action=index');
                exit;
            }
            break;
        case 'edit':
            $evc->edit();
            break;
        case 'details':
            $evc->details();
            break;
        case 'update':
            $evc->update();
            break;
        case 'delete':
            $evc->delete();
            break;
        case 'combined':
            $evc->combined();
            break;
        default:
            $evc->index();
            break;
    }
    exit;
}

// Route to Contenu Controller
if ($controller === 'contenu') {
    $cc = new ContenuController();
    switch ($action) {
        case 'admin':
            $cc->adminIndex();
            break;
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $cc->store();
            } else {
                $cc->create();
            }
            break;
        case 'create_front':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $cc->frontStore();
            } else {
                header('Location: index.php?controller=contenu&action=index');
                exit;
            }
            break;
        case 'edit':
            $cc->edit();
            break;
        case 'like':
            $cc->like();
            break;
        case 'details':
            $cc->details();
            break;
        case 'update':
            $cc->update();
            break;
        case 'delete':
            $cc->delete();
            break;
        default:
            $cc->index();
            break;
    }
    exit;
}

// Route to Frontoffice Controller
if ($controller === 'frontoffice') {
    $fo = new FrontofficeController();
    switch ($action) {
        case 'login':
            $fo->login();
            break;
        case 'register':
            $fo->register();
            break;
        case 'forum':
            $fo->forum();
            break;
        case 'profile':
            $fo->profile();
            break;
        case 'report':
            $fo->createReport();
            break;
        case 'help':
            $fo->helpRequest();
            break;
        default:
            $fo->login();
            break;
    }
    exit;
}

// Default to Home Controller
$hc = new HomeController();
$hc->index();
