<?php
// Controller/HelpRequestController.php
require_once __DIR__ . '/../Model/help_request_logic.php';

class HelpRequestController {
    public static function index() {
        $requests = hr_get_all();
        require __DIR__ . '/../View/help_requests/list.php';
    }

    public static function show($id) {
        $req = hr_get($id);
        require __DIR__ . '/../View/help_requests/show.php';
    }

    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = hr_get_post_data();
            $id = hr_create($payload);
            header('Location: index.php');
            exit;
        }
        require __DIR__ . '/../View/help_requests/form.php';
    }

    public static function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = hr_get_post_data();
            hr_update($id, $payload);
            header('Location: index.php');
            exit;
        }
        $request = hr_get($id);
        require __DIR__ . '/../View/help_requests/form.php';
    }

    public static function delete($id) {
        hr_delete($id);
        header('Location: index.php');
        exit;
    }
}
