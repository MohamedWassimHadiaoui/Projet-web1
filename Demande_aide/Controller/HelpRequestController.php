<?php
// Controller/HelpRequestController.php
require_once __DIR__ . '/../Model/help_request_logic.php';

class HelpRequestController {
    public static function index() {
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        
        // Accepter 'admin' ou 'backoffice' comme backoffice
        if ($section === 'admin' || $section === 'backoffice') {
            // Pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;
            $limit = 3;
            $offset = ($page - 1) * $limit;

            $requests = hr_get_paginated($limit, $offset);
            $totalRequests = hr_count_all();
            $totalPendingRequests = hr_count_by_status('pending');
            $totalPages = ceil($totalRequests / $limit);

            require __DIR__ . '/../View/BackOffice/help_requests/list.php';
        } else {
            $requests = hr_get_all();
            require __DIR__ . '/../View/FrontOffice/help_requests/list.php';
        }
    }

    public static function show($id) {
        $req = hr_get($id);
        if (!$req) {
            header('Location: index.php');
            exit;
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        require __DIR__ . '/../View/FrontOffice/help_requests/show.php';
    }

    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $payload = hr_get_post_data();
                $id = hr_create($payload);
                $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
                header('Location: index.php?action=help-requests' . $section);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/help_requests/form.php';
        } else {
            require __DIR__ . '/../View/FrontOffice/help_requests/form.php';
        }
    }

    public static function edit($id) {
        $request = hr_get($id);
        if (!$request) {
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $payload = hr_get_post_data();
                hr_update($id, $payload);
                $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
                header('Location: index.php?action=help-requests' . $section);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/help_requests/form.php';
        } else {
            require __DIR__ . '/../View/FrontOffice/help_requests/form.php';
        }
    }

    public static function delete($id) {
        hr_delete($id);
        $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
        header('Location: index.php?action=help-requests' . $section);
        exit;
    }
}

?>
