<?php

class FrontofficeController {
    
    public function login() {
        include __DIR__ . '/../views/frontoffice/login.php';
    }
    
    public function register() {
        include __DIR__ . '/../views/frontoffice/register.php';
    }
    
    public function forum() {
        include __DIR__ . '/../views/frontoffice/forum.php';
    }
    
    public function profile() {
        include __DIR__ . '/../views/frontoffice/profile.php';
    }
    
    public function createReport() {
        include __DIR__ . '/../views/frontoffice/create-report.php';
    }
    
    public function helpRequest() {
        include __DIR__ . '/../views/frontoffice/help-request.php';
    }
}
